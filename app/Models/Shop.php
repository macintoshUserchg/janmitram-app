<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Shop extends Model
{
    use HasFactory , SoftDeletes;

    protected $guarded = ['id'];

    /**
     * Get the shop user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * get emploees for this shop
     */
    public function employees(): HasMany
    {
        return $this->hasMany(User::class, 'shop_id');
    }

    /**
     * get withdraw model for this user.
     */
    public function withdraws(): HasMany
    {
        return $this->hasMany(Withdraw::class, 'shop_id');
    }

    /**
     * Get the logo media for the Shop.
     */
    public function mediaLogo(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'logo_id');
    }

    /**
     * Retrieve the media banner for this instance.
     */
    public function mediaBanner(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'banner_id');
    }

    /**
     * get all gallery images for this shop
     */
    public function galleries(): HasMany
    {
        return $this->hasMany(Gallery::class, 'shop_id');
    }

    /**
     * Get the logo for the Shop as an attribute.
     */
    public function logo(): Attribute
    {
        $logo = asset('default/default.jpg');
        if ($this->mediaLogo && Storage::exists($this->mediaLogo->src)) {
            $logo = Storage::url($this->mediaLogo->src);
        }

        return Attribute::make(
            get: fn () => $logo
        );
    }

    /**
     * Get the banner for the Shop as an attribute.
     */
    public function banner(): Attribute
    {
        $banner = asset('default/default.jpg');
        if ($this->mediaBanner && Storage::exists($this->mediaBanner->src)) {
            $banner = Storage::url($this->mediaBanner->src);
        }

        return Attribute::make(
            get: fn () => $banner
        );
    }

    /**
     * Get all of the products for the Shop.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Retrieve the categories associated with the shop.
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'shop_categories');
    }

    /**
     * Retrieve the sub categories associated with the shop.
     */
    public function subCategories(): HasMany
    {
        return $this->hasMany(SubCategory::class);
    }

    /**
     * get all of the brands for the shop.
     */
    public function brands(): HasMany
    {
        return $this->hasMany(Brand::class);
    }

    /**
     * Get all of the coupons for the Shop.
     */
    public function coupons(): HasMany
    {
        return $this->hasMany(Coupon::class);
    }

    /**
     * Get all of the colors for the Shop.
     */
    public function colors(): HasMany
    {
        return $this->hasMany(Color::class);
    }

    /**
     * Get the sizes for the shop.
     */
    public function sizes(): HasMany
    {
        return $this->hasMany(Size::class, 'shop_id');
    }

    /**
     * Get all of the units for the Shop.
     */
    public function units(): HasMany
    {
        return $this->hasMany(Unit::class);
    }

    /**
     * Get all of the orders for the Shop.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get the parent shop in the MLM network.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Shop::class, 'parent_shop_id');
    }

    /**
     * Get the direct downline shops of this shop.
     */
    public function children(): HasMany
    {
        return $this->hasMany(Shop::class, 'parent_shop_id');
    }

    /**
     * Get the monthly MLM payouts of this shop.
     */
    public function monthlyPayouts(): HasMany
    {
        return $this->hasMany(ShopMonthlyPayout::class);
    }

    /**
     * Get all of the banners for the Shop.
     */
    public function banners(): HasMany
    {
        return $this->hasMany(Banner::class, 'shop_id');
    }

    /**
     * Scope a query to only include active shops.
     *
     * @param  Builder  $builder  The query builder
     * @return mixed
     */
    public function scopeIsActive(Builder $builder)
    {
        return $builder->whereHas('user', function ($query) {
            $query->where('is_active', 1);
        });
    }

    /**
     * Get all of the reviews for the Shop.
     *
     * @return HasMany.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'shop_id');
    }

    /**
     * Calculates the average rating of the reviews.
     *
     * @return Attribute The average rating attribute.
     */
    public function averageRating(): Attribute
    {
        $avgRating = $this->reviews()->avg('rating');

        return new Attribute(
            get: fn () => (float) number_format($avgRating > 0 ? $avgRating : 5, 1, '.', ''),
        );
    }

    public function returnOrders(): HasMany
    {
        return $this->hasMany(ReturnOrder::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    public function stockRequests(): HasMany
    {
        return $this->hasMany(StockRequest::class);
    }

    /**
     * Get the KYC (Aadhaar / PAN / bank) record for the Shop.
     */
    public function kyc(): HasOne
    {
        return $this->hasOne(ShopKyc::class);
    }

    /**
     * Get unique referral / sponsor code for this shop (e.g. JAN-00002).
     */
    public function referralCode(): Attribute
    {
        return new Attribute(
            get: fn () => 'JAN-'.str_pad((string) $this->id, 5, '0', STR_PAD_LEFT),
        );
    }

    /**
     * Get full public referral registration URL for this shop.
     */
    public function referralUrl(): Attribute
    {
        return new Attribute(
            get: fn () => route('shop.register', ['ref' => $this->referral_code]),
        );
    }

    /**
     * Find shop by referral code (e.g. "JAN-00002", "2", or "SHOP-00002").
     */
    public static function findByReferralCode(?string $code): ?self
    {
        if (! $code) {
            return null;
        }

        $code = trim($code);

        if (is_numeric($code)) {
            return self::find((int) $code);
        }

        if (preg_match('/^(?:JAN|SHOP)-?(\d+)$/i', $code, $matches)) {
            return self::find((int) $matches[1]);
        }

        return self::where('name', 'like', "%{$code}%")->first();
    }

    /**
     * Maximum allowed direct downline shops for standard partner shops.
     * Main Janmitram Shop (ID: 1) is exempt and has unlimited direct capacity.
     */
    public const MAX_DIRECT_DOWNLINES = 10;

    /**
     * Check if this shop is the Main Janmitram Central Shop (unrestricted).
     */
    public function isMainShop(): bool
    {
        return (int) $this->id === 1
            || $this->name === 'Main Janmitram Shop'
            || ($this->parent_shop_id === null && ((int) $this->user_id === 1 || ($this->user && $this->user->hasRole('root'))));
    }

    /**
     * Total direct active downline shops.
     */
    public function directDownlinesCount(): int
    {
        return $this->children()->whereHas('user', fn ($query) => $query->where('is_active', 1))->count();
    }

    /**
     * Check if this shop can accept another direct downline partner.
     */
    public function canAcceptDirectDownline(): bool
    {
        if ($this->isMainShop()) {
            return true;
        }

        return $this->directDownlinesCount() < self::MAX_DIRECT_DOWNLINES;
    }

    /**
     * Remaining direct downline slots available (null for Main Shop = unlimited).
     */
    public function availableDirectDownlineSlots(): ?int
    {
        if ($this->isMainShop()) {
            return null;
        }

        return max(0, self::MAX_DIRECT_DOWNLINES - $this->directDownlinesCount());
    }

    /**
     * Minimum monetary value required for a shop's first stock transfer / initial stocking kit.
     */
    public const MIN_FIRST_STOCK_TRANSFER_AMOUNT = 3000.0;

    /**
     * Check if this shop has ever received completed stock inventory.
     */
    public function hasReceivedStock(): bool
    {
        return $this->stockRequests()->where('status', 'completed')->exists();
    }

    /**
     * Check if the next stock transfer for this shop is its first initial allocation.
     */
    public function isFirstStockTransfer(): bool
    {
        return ! $this->hasReceivedStock();
    }
}
