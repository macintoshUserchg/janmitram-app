<?php

namespace App\Repositories;

use App\Http\Requests\ProductRequest;
use App\Models\Media;
use App\Models\Product;
use App\Models\ProductTranslation;
use App\Models\RecentView;
use App\Models\Shop;
use App\Models\User;
use App\Models\VatTax;
use App\Support\Repositories\Repository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Mews\Purifier\Facades\Purifier;

class ProductRepository extends Repository
{
    /**
     * Spreadsheet column name → 0-based index (exact import/export column order).
     */
    private const IMPORT_COLUMNS = [
        'id' => 0,
        'name' => 1,
        'short_description' => 2,
        'description' => 3,
        'brand' => 4,
        'unit' => 5,
        'category' => 6,
        'sub_category' => 7,
        'colors' => 8,
        'sizes' => 9,
        'price' => 10,
        'discount_price' => 11,
        'buy_price' => 12,
        'sku' => 13,
        'quantity' => 14,
        'min_order_quantity' => 15,
        'is_digital' => 16,
        'vat_rate' => 17,
        'meta_title' => 18,
        'meta_description' => 19,
        'meta_keywords' => 20,
    ];

    /**
     * The expected import/export header row, in column order.
     *
     * @return array<int, string>
     */
    public static function importHeaders(): array
    {
        return array_keys(self::IMPORT_COLUMNS);
    }

    /**
     * base method
     *
     * @method model()
     */
    public static function model()
    {
        return Product::class;
    }

    public static function recentView(Product $product)
    {
        $user = Auth::guard('api')->user();
        if ($user) {
            RecentView::where('product_id', $product->id)->where('user_id', $user->id)->firstOrCreate([
                'product_id' => $product->id,
                'user_id' => $user->id,
            ])?->update(['updated_at' => now()]);
        }

        return $product;
    }

    /**
     * Sanitizes a string by removing invalid or non-printable characters.
     *
     * @param  string  $input
     * @return string
     */
    public static function sanitizeUnicode($input)
    {
        $cleanedInput = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $input);
        $cleanedInput = preg_replace('/[\xF0-\xF9][\x80-\xBF][\xF0\x9F]{3}/u', '', $input);
        $cleanedInput = preg_replace('/[\xF0-\xF7][\x80-\xBF]{3}/u', '', $cleanedInput);
        $cleanedInput = preg_replace('/[\x{1F600}-\x{1F64F}]/u', '', $cleanedInput);
        $cleanedInput = preg_replace('/[\x{1F600}-\x{1F64F}]|[\x{1F300}-\x{1F5FF}]|[\x{1F680}-\x{1F6FF}]|[\x{2600}-\x{26FF}]|[\x{2700}-\x{27BF}]/u', '', $cleanedInput);

        return $cleanedInput;
    }

    /**
     * store new product.
     *
     * @param  ProductRequest  $request
     *                                   return \App\Models\Product
     */
    public static function storeByRequest(ProductRequest $request): Product
    {
        $thumbnail = MediaRepository::storeByRequest($request->thumbnail, 'products', 'thumbnail');
        $isDigital = false;

        if (isset($request->is_digital)) {
            $isDigital = true;
        }

        $shop = generaleSetting('shop');
        $generaleSetting = generaleSetting('setting');
        $approve = $generaleSetting?->new_product_approval ? false : true;

        /**
         * @var User $user
         */
        $user = auth()->user();
        $isAdmin = false;
        if ($user->hasRole('root') || ($generaleSetting?->shop_type == 'single')) {
            $isAdmin = true;
        }

        $videoMedia = self::videoCreateOrUpdate($request);
        $description = Purifier::clean(self::sanitizeUnicode($request->description));

        $keywords = implode(',', $request->meta_keywords ?? []);

        $product = self::create([
            'shop_id' => $shop?->id,
            'name' => $request->name,
            'description' => $description,
            'short_description' => $request->short_description,
            'brand_id' => $request->brand,
            'unit_id' => $request->unit,
            'price' => $request->price,
            'discount_price' => $request->discount_price,
            'quantity' => $isDigital ? ($request->quantity > 1 ? $request->quantity : 999999) : 0,
            'min_order_quantity' => $request->min_order_quantity ?? 1,
            'media_id' => $thumbnail->id,
            'code' => $request->code,
            'buy_price' => $request->buy_price ?? 0,
            'is_active' => $isAdmin ? true : $approve,
            'is_digital' => $isDigital,
            'is_new' => true,
            'is_approve' => $isAdmin ? true : $approve,
            'video_id' => $videoMedia ? $videoMedia->id : null,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'meta_keywords' => $keywords ? Str::limit($keywords, 200, '') : null,
            'is_stock_managed' => ! $isDigital,
        ]);

        foreach ($request->names ?? [] as $key => $value) {
            if (! $key || ! $value) {
                continue;
            }

            $description = array_key_exists($key, $request->descriptions ?? []) ? $request->descriptions[$key] : null;
            $shortDescription = array_key_exists($key, $request->short_descriptions ?? []) ? $request->short_descriptions[$key] : null;

            ProductTranslation::create([
                'product_id' => $product->id,
                'lang' => $key,
                'name' => $value,
                'description' => $description,
                'short_description' => $shortDescription,
            ]);
        }

        if ($request->is('api/*')) {
            if ($request->color && is_array($request->color)) {
                $colors = array_column($request->color, 'id');
                $product->colors()->sync($colors);
            }
        } else {
            foreach ($request->color ?? [] as $color) {
                $product->colors()->attach($color['id'], ['price' => $color['price']]);
            }
        }

        $product->categories()->sync($request->category ?? []);
        $product->subcategories()->sync($request->sub_category ?? []);

        if ($request->is('api/*')) {
            if ($request->size && is_array($request->size)) {
                foreach ($request->size ?? [] as $size) {
                    $price = 0;
                    $product->sizes()->attach($size, ['price' => $price]);
                }
            }
        } else {
            foreach ($request->size ?? [] as $size) {
                $product->sizes()->attach($size['id'], ['price' => $size['price']]);
            }
        }

        foreach ($request->additionThumbnail ?? [] as $additionThumbnail) {
            $thumbnail = MediaRepository::storeByRequest($additionThumbnail, 'products', 'thumbnail', 'image');
            $product->medias()->attach($thumbnail->id);
        }

        foreach ($request->digitalAttachment ?? [] as $digitalAttachment) {
            $thumbnail = MediaRepository::storeByRequest($digitalAttachment, 'products/digital_attachment');
            $product->attachments()->attach($thumbnail->id);
        }

        if (! empty($request->license_key)) {
            foreach ($request->license_key ?? [] as $license) {
                ProductLicenseRepository::storeByRequest($product->id, $license);
            }
        }

        if ($request->filled('vat_tax_id')) {
            $product->vatTaxes()->sync([$request->vat_tax_id]);
        }

        return $product;
    }

    /**
     * Update the product.
     *
     * @param  ProductRequest  $request
     *                                   return \App\Models\Product
     */
    public static function updateByRequest(ProductRequest $request, Product $product): Product
    {
        $thumbnail = $product->media;
        if ($request->hasFile('thumbnail') && $thumbnail) {
            $thumbnail = MediaRepository::updateByRequest(
                $request->thumbnail,
                'products',
                'image',
                $thumbnail
            );
        }

        if ($request->hasFile('thumbnail') && $thumbnail == null) {
            $thumbnail = MediaRepository::storeByRequest($request->thumbnail, 'products', 'image');
        }

        $generaleSetting = generaleSetting('setting');
        $approve = $generaleSetting?->update_product_approval ? false : true;

        /**
         * @var User $user
         */
        $user = auth()->user();
        $isAdmin = false;
        if ($user->hasRole('root') || ($generaleSetting?->shop_type == 'single')) {
            $isAdmin = true;
        }

        $videoMedia = self::videoCreateOrUpdate($request, $product);
        $description = Purifier::clean(self::sanitizeUnicode($request->description));
        $keywords = implode(',', $request->meta_keywords ?? []);

        self::update($product, [
            'name' => $request->name,
            'description' => $description,
            'short_description' => $request->short_description,
            'brand_id' => $request->brand ?? null,
            'unit_id' => $request->unit ?? null,
            'price' => $request->price,
            'discount_price' => $request->discount_price,
            'quantity' => $product->is_digital ? ($request->quantity > 1 ? $request->quantity : 999999) : $product->quantity,
            'min_order_quantity' => $request->min_order_quantity ?? 1,
            'media_id' => $thumbnail ? $thumbnail->id : null,
            'code' => $request->code,
            'buy_price' => $request->buy_price ?? 0,
            'is_active' => $isAdmin ? true : $approve,
            'is_new' => false,
            'is_approve' => $isAdmin ? true : $approve,
            'video_id' => $videoMedia ? $videoMedia->id : null,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'meta_keywords' => $keywords ? Str::limit($keywords, 200, '') : null,
            'is_stock_managed' => ! $product->is_digital,
        ]);

        foreach ($request->names ?? [] as $key => $value) {
            if (! $key || ! $value) {
                continue;
            }

            $description = array_key_exists($key, $request->descriptions ?? []) ? $request->descriptions[$key] : null;
            $shortDescription = array_key_exists($key, $request->short_descriptions ?? []) ? $request->short_descriptions[$key] : null;

            ProductTranslation::updateOrCreate(
                [
                    'product_id' => $product->id,
                    'lang' => $key,
                ],
                [
                    'name' => $value,
                    'description' => $description,
                    'short_description' => $shortDescription,
                ]
            );
        }

        $product->colors()->detach();
        if ($request->is('api/*')) {
            $colors = [];
            if ($request->color && is_array($request->color)) {
                $colors = array_column($request->color, 'id');
            }
            $product->colors()->attach($colors);
        } else {
            foreach ($request->color ?? [] as $color) {
                $product->colors()->attach($color['id'], ['price' => $color['price']]);
            }
        }

        $product->categories()->sync($request->category ?? []);
        $product->subcategories()->sync($request->sub_category ?? []);

        $product->sizes()->detach();
        if ($request->is('api/*')) {
            if ($request->size && is_array($request->size)) {
                foreach ($request->size ?? [] as $size) {
                    $price = 0;
                    $product->sizes()->attach($size, ['price' => $price]);
                }
            }
        } else {
            foreach ($request->size ?? [] as $size) {
                $product->sizes()->attach($size['id'], ['price' => $size['price']]);
            }
        }

        if ($request->is('api/*')) {
            self::updateAdditionThumbnails($request->previousThumbnail, $product);
        } else {
            foreach ($request->additionThumbnail ?? [] as $additionThumbnail) {
                $thumbnail = MediaRepository::storeByRequest($additionThumbnail, 'products', 'thumbnail', 'image');
                $product->medias()->attach($thumbnail->id);
            }

            self::updatePreviousThumbnail($request->previousThumbnail);
        }

        if ($request->is('api/*')) {
            self::updateAdditionAttachments($request->attachmentPreviousThumbnail, $product);
        } else {
            foreach ($request->digitalAttachment ?? [] as $digitalAttachment) {
                $attachment = MediaRepository::storeByRequest($digitalAttachment, 'products/digital_attachment');
                $product->attachments()->attach($attachment->id);
            }

            self::updatePreviousThumbnail($request->attachmentPreviousThumbnail);
        }

        if (! empty($request->license_key)) {
            foreach ($request->license_key ?? [] as $license) {
                ProductLicenseRepository::storeByRequest($product->id, $license);
            }
        }

        if (! empty($request->previous_license_key)) {
            foreach ($request->previous_license_key ?? [] as $key => $license) {
                ProductLicenseRepository::updateByRequest($product->id, $license, $key);
            }
        }

        if ($request->filled('vat_tax_id')) {
            $product->vatTaxes()->sync([$request->vat_tax_id]);
        }

        return $product;
    }

    private static function videoCreateOrUpdate($request, $product = null): ?Media
    {
        $media = $product?->videoMedia;
        $uploadVideoRequest = $request->uploadVideo;

        if (! $uploadVideoRequest || ! is_countable($uploadVideoRequest)) {
            return $media;
        }

        $type = $uploadVideoRequest['type'];
        $url = isset($uploadVideoRequest[$type.'_'.'url']) ? $uploadVideoRequest[$type.'_'.'url'] : null;

        if ($media && $type == 'file' && isset($uploadVideoRequest['file']) && is_file($uploadVideoRequest['file'])) {
            return MediaRepository::updateByRequest(
                $uploadVideoRequest['file'],
                'products',
                'file',
                $media
            );
        } elseif ($media && $type != 'file' && $url != null) {

            // Replace the width and height attributes in the iframe
            $customWidth = '100%';
            $customHeight = '650';
            $customizedIframe = preg_replace(['/width="(\d+(%?))"/', '/height="(\d+(%?))"/'], ["width=\"$customWidth\"", "height=\"$customHeight\""], $url);

            $media->update([
                'src' => $customizedIframe,
                'type' => $type,
            ]);

            return $media;
        }

        if (! $media && $type == 'file' && isset($uploadVideoRequest['file']) && is_file($uploadVideoRequest['file'])) {
            return MediaRepository::storeByRequest(
                $uploadVideoRequest['file'],
                'products',
                'file'
            );
        } elseif (! $media && $type != 'file' && $url != null) {

            $width = '100%';
            $height = '650';
            $customizedIframe = preg_replace(['/width="(\d+(%?))"/', '/height="(\d+(%?))"/'], ["width=\"$width\"", "height=\"$height\""], $url);

            return Media::create([
                'src' => $customizedIframe,
                'type' => $type,
            ]);
        }

        return $media;
    }

    /**
     * Import spreadsheet rows into root-shop products (upsert by SKU/code).
     *
     * The caller is expected to pass the raw rows with the header already
     * stripped, so index 0 is the first data row. Rows are reported as
     * 1-based spreadsheet row numbers (index + 1).
     *
     * @param  array<int, array<int, mixed>>  $rows
     * @return array{imported: int, updated: int, failed: int, errors: array<int, array{row: int, reason: string}>}
     */
    public static function importRows(array $rows): array
    {
        $rootShop = generaleSetting('rootShop');

        $result = ['imported' => 0, 'updated' => 0, 'failed' => 0, 'errors' => []];

        foreach (array_values($rows) as $index => $row) {
            $rowNumber = $index + 1;

            try {
                $outcome = self::importRow($row, $rootShop);
            } catch (\Throwable $e) {
                $result['failed']++;
                $result['errors'][$rowNumber] = ['row' => $rowNumber, 'reason' => $e->getMessage()];

                continue;
            }

            $result[$outcome]++;
        }

        return $result;
    }

    /**
     * Map and persist a single import row. Throws on validation failure so the
     * caller can record it as a failed row; returns 'imported' or 'updated'.
     *
     * @param  array<int, mixed>  $row
     * @return 'imported'|'updated'
     */
    private static function importRow(array $row, Shop $rootShop): string
    {
        $cell = fn (string $column) => $row[self::IMPORT_COLUMNS[$column]] ?? null;

        $sku = trim((string) $cell('sku'));
        if ($sku === '') {
            throw new InvalidArgumentException('Missing or empty SKU');
        }

        $name = trim((string) $cell('name'));
        if ($name === '') {
            throw new InvalidArgumentException('Missing product name');
        }

        $price = $cell('price');
        if ($price === null || $price === '' || ! is_numeric($price)) {
            throw new InvalidArgumentException('Price must be numeric');
        }
        $price = (float) $price;

        $categoryIds = self::resolveNames($rootShop->categories()->active()->get(), $cell('category'));
        if ($categoryIds === []) {
            throw new InvalidArgumentException('No valid category');
        }

        $subCategoryIds = self::resolveNames($rootShop->subCategories()->isActive()->get(), $cell('sub_category'));
        $colorIds = self::resolveNames($rootShop->colors()->isActive()->get(), $cell('colors'));
        $sizeIds = self::resolveNames($rootShop->sizes()->isActive()->get(), $cell('sizes'));

        $brand = $rootShop->brands()->isActive()->where('name', trim((string) $cell('brand')))->first();
        $unit = $rootShop->units()->isActive()->where('name', trim((string) $cell('unit')))->first();

        $isDigital = self::parseIsDigital($cell('is_digital'));

        $vatRateName = trim((string) $cell('vat_rate'));
        $vatTaxId = null;
        if ($vatRateName !== '') {
            $vatTax = VatTax::where('is_active', true)->where('name', $vatRateName)->first();
            if (! $vatTax) {
                throw new InvalidArgumentException('Unknown VAT rate');
            }
            $vatTaxId = $vatTax->id;
        }

        $quantity = self::nonNegativeNumber($cell('quantity'), 1);
        $minOrderQuantity = self::nonNegativeNumber($cell('min_order_quantity'), 1);
        $buyPrice = self::nonNegativeNumber($cell('buy_price'), 0);

        $discountPrice = $cell('discount_price');
        if ($discountPrice === null || $discountPrice === '' || ! is_numeric($discountPrice)) {
            $discountPrice = null;
        } else {
            $discountPrice = (float) $discountPrice;
            if ($discountPrice < 0) {
                $discountPrice = 0;
            } elseif ($discountPrice > $price) {
                $discountPrice = $price;
            }
        }

        $description = trim((string) $cell('description'));
        $shortDescription = trim((string) $cell('short_description'));
        $metaTitle = trim((string) $cell('meta_title'));
        $metaDescription = trim((string) $cell('meta_description'));
        $metaKeywords = trim((string) $cell('meta_keywords'));

        $data = [
            'name' => $name,
            'short_description' => $shortDescription !== '' ? $shortDescription : null,
            'description' => $description !== '' ? $description : 'description',
            'brand_id' => $brand?->id,
            'unit_id' => $unit?->id,
            'price' => $price,
            'discount_price' => $discountPrice,
            'buy_price' => $buyPrice,
            'quantity' => $quantity,
            'min_order_quantity' => $minOrderQuantity,
            'is_digital' => $isDigital,
            'meta_title' => $metaTitle !== '' ? $metaTitle : null,
            'meta_description' => $metaDescription !== '' ? $metaDescription : null,
            'meta_keywords' => $metaKeywords !== '' ? Str::limit($metaKeywords, 200, '') : null,
        ];

        return DB::transaction(function () use ($rootShop, $sku, $data, $categoryIds, $subCategoryIds, $colorIds, $sizeIds, $vatTaxId) {
            $product = $rootShop->products()->where('code', $sku)->first();

            if ($product) {
                self::update($product, $data);
            } else {
                $data['shop_id'] = $rootShop->id;
                $data['code'] = $sku;
                $data['is_active'] = true;
                $data['is_approve'] = true;
                $data['is_new'] = true;
                $data['is_stock_managed'] = ! $data['is_digital'];
                $product = self::create($data);
            }

            $product->categories()->sync($categoryIds);
            $product->subcategories()->sync($subCategoryIds);
            $product->colors()->sync($colorIds);
            $product->sizes()->sync($sizeIds);
            $product->vatTaxes()->sync($vatTaxId ? [$vatTaxId] : []);

            return $product->wasRecentlyCreated ? 'imported' : 'updated';
        });
    }

    /**
     * Match a comma-separated list of names against a model collection and
     * return the matching ids. Unmatched names are dropped (not an error).
     *
     * @param  Collection<int, Model>  $models
     * @return array<int, int>
     */
    private static function resolveNames($models, $value): array
    {
        $ids = [];

        foreach (explode(',', (string) $value) as $name) {
            $name = trim($name);
            if ($name === '') {
                continue;
            }

            $match = $models->firstWhere('name', $name);
            if ($match) {
                $ids[] = $match->id;
            }
        }

        return array_values(array_unique($ids));
    }

    /**
     * Parse an is_digital cell: 1/Y (case-insensitive) are true.
     *
     * @param  mixed  $value
     */
    private static function parseIsDigital($value): bool
    {
        return in_array(strtoupper(trim((string) $value)), ['1', 'Y', 'YES', 'TRUE'], true);
    }

    /**
     * Parse an optional non-negative number, falling back to $default for
     * blank or non-numeric cells.
     *
     * @param  mixed  $value
     */
    private static function nonNegativeNumber($value, float $default): float
    {
        if ($value === null || trim((string) $value) === '' || ! is_numeric($value)) {
            return $default;
        }

        return max(0, (float) $value);
    }

    /**
     * Update the previous thumbnails.
     *
     * @param  array  $previousThumbnails  The array of previous thumbnails
     */
    private static function updatePreviousThumbnail($previousThumbnails)
    {
        foreach ($previousThumbnails ?? [] as $thumbnail) {
            if (array_key_exists('file', $thumbnail) && array_key_exists('id', $thumbnail) && $thumbnail['file'] != null) {
                $media = Media::find($thumbnail['id']);

                MediaRepository::updateByRequest(
                    $thumbnail['file'],
                    'products',
                    'image',
                    $media
                );
            }
        }
    }

    /**
     * Update the additional thumbnails.
     *
     * @param  array  $additionalThumbnails  The array of additional thumbnails
     * @param  Product  $product
     */
    private static function updateAdditionThumbnails($additionalThumbnails, $product)
    {
        $ids = [];

        foreach ($additionalThumbnails ?? [] as $additionThumbnail) {
            if (array_key_exists('file', $additionThumbnail) && $additionThumbnail['file'] != null) {

                $media = MediaRepository::storeByRequest($additionThumbnail['file'], 'products', 'thumbnail', 'image');

                $ids[] = $media->id;

                $product->medias()->attach($media->id);
            }

            if (array_key_exists('id', $additionThumbnail) && $additionThumbnail['id'] != null && $additionThumbnail['id'] != 0) {
                $ids[] = $additionThumbnail['id'];
            }
        }

        $previousMedias = $product->medias()->whereNotIn('id', $ids)->get();

        foreach ($previousMedias as $media) {

            $product->medias()->detach($media->id);

            if (Storage::exists($media->src)) {
                Storage::delete($media->src);
            }

            $media->delete();
        }
    }

    private static function updateAdditionAttachments($additionalThumbnails, $product)
    {
        $ids = [];

        foreach ($additionalThumbnails ?? [] as $additionThumbnail) {
            if (array_key_exists('file', $additionThumbnail) && $additionThumbnail['file'] != null) {

                $media = MediaRepository::storeByRequest($additionThumbnail['file'], 'products', 'thumbnail', 'image');

                $ids[] = $media->id;

                $product->attachments()->attach($media->id);
            }

            if (array_key_exists('id', $additionThumbnail) && $additionThumbnail['id'] != null && $additionThumbnail['id'] != 0) {
                $ids[] = $additionThumbnail['id'];
            }
        }

        $previousMedias = $product->attachments()->whereNotIn('id', $ids)->get();

        foreach ($previousMedias as $media) {

            $product->attachments()->detach($media->id);

            if (Storage::exists($media->src)) {
                Storage::delete($media->src);
            }

            $media->delete();
        }
    }
}
