# Admin Shop KYC Feature — Design Spec

**Date:** 2026-08-03
**Status:** Approved

## Context

The seller shop KYC feature (Aadhaar card/number, PAN card/number, DOB, qualification, bank name/IFSC/account number, other documents) was added to the public registration form at `/shop/register` and persisted to a dedicated `shop_kyc` table (see `database/migrations/2026_08_03_233146_create_shop_kyc_table.php`, `app/Models/ShopKyc.php`, `Shop::kyc()`).

The admin dashboard "Add New Shop" endpoint (`/admin/shop/create`) creates shops through the **same** persistence chain (`Admin\ShopController@store` → `ShopRepository::storeByRequest`) but its form currently collects **no KYC** and the validation toggle treats KYC as nullable for `admin.shop.store`. This spec extends the KYC feature to the admin **create** and **edit** shop forms so every seller — self-registered or admin-onboarded — has complete, reviewable KYC.

## Goals

- Admin "Add New Shop" form collects the same 10 KYC fields as public registration, with identity + bank required and other documents optional.
- Admin "Edit Shop" form can view/correct KYC on existing shops (prefilled; documents replaceable, not re-required).
- Persistence reuses the existing `shop_kyc` table and media-FK pattern. No schema change.
- No impact on the public registration form, the API seller register flow, or the seller downline-create flow.

## Design

### 1. `app/Http/Requests/ShopCreateRequest.php`

Change the route-scoped KYC required toggle (currently `routeIs('shop.register.submit') ? 'required' : 'nullable'`) to:

```php
$kycRequired = $this->routeIs('shop.register.submit') || $this->routeIs('admin.shop.store')
    ? 'required' : 'nullable';
```

Resulting behavior per caller:

| Caller | Route | KYC |
|---|---|---|
| Public registration | `shop.register.submit` | required |
| Admin create | `admin.shop.store` | required |
| Admin edit | `admin.shop.update` | **nullable** (prefilled; legacy shops with no KYC row must still save) |
| API seller register | (api) | nullable — unchanged |
| Seller downline create | `shop.payout.network.store` | nullable — unchanged |

`prepareForValidation()` and the KYC rules themselves need no change.

### 2. `app/Repositories/ShopRepository.php`

Add KYC persistence to `updateByRequest()`, mirroring the create path in `storeByRequest()`. Insert after the existing `self::update($shop, [...])` call:

```php
$kyc = $shop->kyc;

if ($request->filled('aadhaar_number') || $request->filled('pan_number')
    || $request->filled('bank_name') || $request->filled('ifsc')
    || $request->filled('account_number') || $request->filled('qualification')
    || $request->hasFile('aadhaar_card') || $request->hasFile('pan_card')
    || $request->hasFile('other_documents')) {
    ShopKyc::updateOrCreate(
        ['shop_id' => $shop->id],
        [
            'aadhaar_card_id'    => self::storeOrUpdateDocument($request->file('aadhaar_card'), 'shops/kyc', $kyc?->aadhaarCard)?->id,
            'pan_card_id'        => self::storeOrUpdateDocument($request->file('pan_card'), 'shops/kyc', $kyc?->panCard)?->id,
            'other_documents_id' => self::storeOrUpdateDocument($request->file('other_documents'), 'shops/kyc', $kyc?->otherDocuments)?->id,
            'aadhaar_number'     => $request->aadhaar_number,
            'pan_number'         => $request->pan_number,
            'bank_name'          => $request->bank_name,
            'ifsc'               => $request->ifsc,
            'account_number'     => $request->account_number,
            'qualification'      => $request->qualification,
        ]
    );
}
```

**Guard uses `filled()`, not `has()`.** The edit form always renders the KYC inputs, so `has()` is true even when empty — on a legacy shop with no KYC row, saving without touching KYC would create an empty row. `filled()` skips the block when no KYC content is present; any actual KYC value or uploaded file creates/updates the row. (A fully-cleared existing KYC row won't update if every field is blank — acceptable; clearing all KYC is not a supported action.)

Add a private helper mirroring the existing `updateLogo` / `updateBanner` pattern:

```php
private static function storeOrUpdateDocument(?UploadedFile $file, string $path, ?Media $existing): ?Media
{
    if (! $file) {
        return $existing;
    }

    return $existing
        ? MediaRepository::updateByRequest($file, $path, 'image', $existing)
        : MediaRepository::storeByRequest($file, $path, 'image');
}
```

`updateByRequest` already handles DOB via `UserRepository::updateByRequest` (`date_of_birth`), and `storeByRequest` already handles it via `UserRepository::registerNewUser`. No change there.

Add imports to `ShopRepository.php`: `use App\Models\Media;` and `use Illuminate\Http\UploadedFile;` (needed by the helper's type hints). `MediaRepository` shares the `App\Repositories` namespace, so no import for it.

### 3. `resources/views/admin/shop/create.blade.php`

Add a "KYC & Bank Details" card after the Shop Information card, before the submit button. Same field set and required-ness as the public registration step 3:

- `aadhaar_card` — `<x-file>` (no preview), required
- `aadhaar_number` — `<x-input type="text">`, required
- `pan_card` — `<x-file>` (no preview), required
- `pan_number` — `<x-input type="text">`, required
- `date_of_birth` — `<x-input type="date">`, required
- `qualification` — `<x-input type="text">`, required
- `bank_name` — `<x-input type="text">`, required
- `ifsc` — `<x-input type="text">`, required
- `account_number` — `<x-input type="text">`, required
- `other_documents` — `<x-file>` (no preview), optional

Layout mirrors the public form's `row`/`col-md-*` grid. Browser `required` attributes block empty submit; format (Aadhaar/PAN/IFSC regex) is enforced server-side with the existing friendly messages. No JS changes.

### 4. `resources/views/admin/shop/edit.blade.php`

Add the same card, prefilled from `$shop->kyc` and `$shop->user`:

- Text fields: `:value="$shop->kyc?->aadhaar_number"`, etc.
- `date_of_birth`: `:value="$shop->user?->date_of_birth"`
- Documents: `<x-file>` **without** `required` (replacement only), each with a "Current: View document" link using the existing accessors (`$shop->kyc?->aadhaar_card_url`, `pan_card_url`, `other_documents_url`).

`updateOrCreate` handles both an existing KYC row (update) and a legacy shop with no row (create on first edit).

## Non-goals / unchanged

- Migration and `shop_kyc` table — already created.
- Admin shop show page — already displays the read-only KYC card.
- Public registration form — untouched.
- API seller register and seller downline-create — KYC stays nullable and uncollected.
- KYC encryption / unique indexes on `aadhaar_number` / `pan_number` — out of scope (business decisions).

## Verification

1. `vendor/bin/pint --dirty --format agent` on touched PHP files.
2. `php -l` on `ShopRepository.php` and `ShopCreateRequest.php`; `php artisan route:list --name=shop.register` sanity.
3. Browser, `/admin/shop/create`: fill KYC → submit → confirm `ShopKyc` row exists with populated media FKs (`php artisan tinker --execute 'echo App\Models\Shop::latest()->first()->kyc;'`). Submit with a malformed Aadhaar → server rejects with friendly message, form keeps entered values.
4. Browser, `/admin/shop/edit/{id}` (a shop with KYC): change bank_name → submit → KYC row updated; upload a new Aadhaar → old media file replaced.
5. Browser, `/admin/shop/edit/{id}` on a legacy shop (no KYC row): save without touching KYC → succeeds, no empty KYC row created; fill KYC → row created.
6. Regression: public `/shop/register` still requires + persists KYC; confirm API seller register and downline-create are unaffected (they send no KYC fields, so `storeByRequest` guard skips KYC creation).
