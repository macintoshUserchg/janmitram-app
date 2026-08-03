<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ShopKyc extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $table = 'shop_kyc';

    /**
     * Get the shop this KYC record belongs to.
     */
    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    /**
     * Get the Aadhaar card media.
     */
    public function aadhaarCard(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'aadhaar_card_id');
    }

    /**
     * Get the PAN card media.
     */
    public function panCard(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'pan_card_id');
    }

    /**
     * Get the other documents media.
     */
    public function otherDocuments(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'other_documents_id');
    }

    /**
     * Get the Aadhaar card document URL.
     */
    public function aadhaarCardUrl(): Attribute
    {
        $url = asset('default/default.jpg');
        if ($this->aadhaarCard && Storage::exists($this->aadhaarCard->src)) {
            $url = Storage::url($this->aadhaarCard->src);
        }

        return Attribute::make(
            get: fn () => $url
        );
    }

    /**
     * Get the PAN card document URL.
     */
    public function panCardUrl(): Attribute
    {
        $url = asset('default/default.jpg');
        if ($this->panCard && Storage::exists($this->panCard->src)) {
            $url = Storage::url($this->panCard->src);
        }

        return Attribute::make(
            get: fn () => $url
        );
    }

    /**
     * Get the other documents URL.
     */
    public function otherDocumentsUrl(): Attribute
    {
        $url = asset('default/default.jpg');
        if ($this->otherDocuments && Storage::exists($this->otherDocuments->src)) {
            $url = Storage::url($this->otherDocuments->src);
        }

        return Attribute::make(
            get: fn () => $url
        );
    }
}
