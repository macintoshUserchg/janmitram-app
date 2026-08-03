# Admin Shop KYC Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add the seller KYC & bank fields to the admin "Add New Shop" form (required) and "Edit Shop" form (prefilled, replaceable docs), so admin-onboarded sellers get the same KYC as self-registered ones.

**Architecture:** Extend the existing route-scoped validation toggle in `ShopCreateRequest` so `admin.shop.store` requires KYC (update stays nullable). Reuse the `shop_kyc` table and media-FK pattern: create path already persists via `ShopRepository::storeByRequest` (no change); edit path gains an `updateOrCreate` block with a `storeOrUpdateDocument` helper. Add a KYC card to the two admin views using the existing `<x-file>` / `<x-input>` Blade components.

**Tech Stack:** PHP 8.2, Laravel 11, Blade + Bootstrap, PHPUnit 11, Laravel Dusk (browser).

## Global Constraints

- PHP 8.2, Laravel 11, PHPUnit 11, Dusk browser tests.
- Do **not** remove or rename existing tests — only modify to keep them green.
- New test classes via `php artisan make:test --phpunit {name}`.
- After any PHP edit: run `vendor/bin/pint --dirty --format agent`.
- Feature tests use `RefreshDatabase` and seed `RoleSeeder` (and `PermissionSeeder` where the registration test does).
- KYC identity + bank fields required on `shop.register.submit` and `admin.shop.store`; nullable on `admin.shop.update`, API seller register, and seller downline-create.
- Aadhaar regex `/^[1-9]\d{11}$/`; PAN `/^[A-Z]{5}[0-9]{4}[A-Z]$/`; IFSC `/^[A-Z]{4}0[A-Z0-9]{6}$/` (all applied after `prepareForValidation()` uppercases PAN/IFSC and strips Aadhaar spaces).
- Document uploads: `mimes:jpg,png,jpeg,gif,pdf`, `max:5120` (5 MB). Documents render via `<x-file>` **without** the `preview` prop (PDFs cannot preview as `<img>`).

---

### Task 1: Repair the broken public registration test

**Files:**
- Modify: `tests/Feature/ShopRegistrationVerificationTest.php`

**Interfaces:**
- Consumes: nothing new — the existing `shop.register.submit` POST.
- Produces: a green baseline for the public registration flow (this test is currently red on `main` from the prior KYC commit, which made KYC required but never updated the test).

- [ ] **Step 1: Add KYC fields to the successful-submission test**

In `ShopRegistrationVerificationTest.php`, edit `test_shop_register_successful_submission` (currently lines 52–81) so the POST includes the now-required KYC fields, and assert the KYC row is persisted:

```php
public function test_shop_register_successful_submission(): void
{
    $response = $this->post(route('shop.register.submit'), [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john.shop@gmail.com',
        'phone' => '9876543210',
        'password' => 'Password@123',
        'password_confirmation' => 'Password@123',
        'shop_name' => 'Janmitram Supermart',
        'profile_photo' => UploadedFile::fake()->image('profile.jpg', 200, 200),
        'shop_logo' => UploadedFile::fake()->image('logo.jpg', 200, 200),
        'shop_banner' => UploadedFile::fake()->image('banner.jpg', 1000, 250),
        'latitude' => '21.2514',
        'longitude' => '81.6296',
        'aadhaar_card' => UploadedFile::fake()->image('aadhaar.jpg', 400, 400),
        'aadhaar_number' => '123456789012',
        'pan_card' => UploadedFile::fake()->image('pan.jpg', 400, 400),
        'pan_number' => 'ABCDE1234F',
        'date_of_birth' => '1990-01-15',
        'qualification' => 'B.Com',
        'bank_name' => 'HDFC Bank',
        'ifsc' => 'HDFC0001234',
        'account_number' => '12345678901234',
    ]);

    $response->assertRedirect(route('shop.login'));
    $response->assertSessionHas('successAlert');

    $user = User::where('email', 'john.shop@gmail.com')->first();
    $this->assertNotNull($user);
    $this->assertFalse((bool) $user->is_active);

    $shop = Shop::where('user_id', $user->id)->first();
    $this->assertNotNull($shop);
    $this->assertEquals('Janmitram Supermart', $shop->name);
    $this->assertEquals('21.2514', $shop->latitude);
    $this->assertEquals('81.6296', $shop->longitude);

    $this->assertDatabaseHas('shop_kyc', [
        'shop_id' => $shop->id,
        'aadhaar_number' => '123456789012',
        'pan_number' => 'ABCDE1234F',
        'bank_name' => 'HDFC Bank',
    ]);
}
```

- [ ] **Step 2: Extend the wizard-render test to cover step 3**

In `test_shop_register_page_renders_wizard_and_map_elements`, after the existing `assertSee('validateStep2')`, add:

```php
$response->assertSee('validateStep3');
```

- [ ] **Step 3: Run the test to verify it passes**

Run: `php artisan test --filter=test_shop_register_successful_submission --compact`
Expected: PASS (was FAIL with "The PAN number is required. / The qualification field is required. / ...")

- [ ] **Step 4: Commit**

```bash
git add tests/Feature/ShopRegistrationVerificationTest.php
git commit -m "$(printf 'fix: add KYC fields to shop registration test\n\nCo-Authored-By: Claude <noreply@anthropic.com>')"
```

---

### Task 2: Make KYC required on admin shop create (validation)

**Files:**
- Modify: `app/Http/Requests/ShopCreateRequest.php:31-34`
- Modify: `tests/Feature/AdminShopCreateTest.php:48-65`
- Create: `tests/Feature/AdminShopKycTest.php`
- Modify: `tests/Browser/AdminExtended/AdminUserManagementTest.php` (Dusk)

**Interfaces:**
- Consumes: existing `ShopRepository::storeByRequest()` (already persists KYC when fields present — no change here).
- Produces: `$kycRequired` resolved to `required` on `admin.shop.store`; new `AdminShopKycTest` methods the later tasks extend.

- [ ] **Step 1: Write the failing test — admin create requires KYC**

Create `tests/Feature/AdminShopKycTest.php`:

```bash
php artisan make:test --phpunit AdminShopKycTest --no-interaction
```

Replace its contents with:

```php
<?php

namespace Tests\Feature;

use App\Models\Shop;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminShopKycTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        $this->seed(RoleSeeder::class);
    }

    private function admin(): User
    {
        $admin = User::factory()->create();
        $admin->assignRole('root');

        return $admin;
    }

    private function validShopPayload(): array
    {
        return [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'phone' => '1234567890',
            'gender' => 'male',
            'email' => 'kycadmin@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'shop_name' => 'KYC Admin Shop',
            'address' => '123 Market St',
            'latitude' => 27.005694931660006,
            'longitude' => 75.77754972401056,
            'profile_photo' => UploadedFile::fake()->image('profile.jpg'),
            'shop_logo' => UploadedFile::fake()->image('logo.jpg'),
            'shop_banner' => UploadedFile::fake()->image('banner.jpg'),
            'aadhaar_card' => UploadedFile::fake()->image('aadhaar.jpg'),
            'aadhaar_number' => '123456789012',
            'pan_card' => UploadedFile::fake()->image('pan.jpg'),
            'pan_number' => 'ABCDE1234F',
            'date_of_birth' => '1990-01-15',
            'qualification' => 'B.Com',
            'bank_name' => 'HDFC Bank',
            'ifsc' => 'HDFC0001234',
            'account_number' => '12345678901234',
        ];
    }

    public function test_admin_shop_create_requires_kyc_fields(): void
    {
        $payload = $this->validShopPayload();
        unset(
            $payload['aadhaar_card'], $payload['aadhaar_number'],
            $payload['pan_card'], $payload['pan_number'],
            $payload['date_of_birth'], $payload['qualification'],
            $payload['bank_name'], $payload['ifsc'], $payload['account_number']
        );

        $response = $this->actingAs($this->admin())->post(route('admin.shop.store'), $payload);

        $response->assertSessionHasErrors([
            'aadhaar_card', 'aadhaar_number', 'pan_card', 'pan_number',
            'date_of_birth', 'qualification', 'bank_name', 'ifsc', 'account_number',
        ]);
    }

    public function test_admin_shop_create_persists_kyc(): void
    {
        $response = $this->actingAs($this->admin())->post(route('admin.shop.store'), $this->validShopPayload());

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('admin.shop.index'));

        $shop = Shop::where('name', 'KYC Admin Shop')->first();
        $this->assertNotNull($shop);
        $this->assertDatabaseHas('shop_kyc', [
            'shop_id' => $shop->id,
            'aadhaar_number' => '123456789012',
            'pan_number' => 'ABCDE1234F',
            'bank_name' => 'HDFC Bank',
            'ifsc' => 'HDFC0001234',
            'qualification' => 'B.Com',
        ]);
    }
}
```

- [ ] **Step 2: Run to verify `requires_kyc` fails**

Run: `php artisan test --filter=test_admin_shop_create_requires_kyc --compact`
Expected: FAIL — `assertSessionHasErrors` gets no errors because KYC is currently nullable on `admin.shop.store`.

- [ ] **Step 3: Implement the validation toggle**

In `app/Http/Requests/ShopCreateRequest.php`, change the toggle (currently around line 31):

```php
// KYC / bank fields are required only on the public shop registration and
// admin shop create routes. Shared callers (admin update, API seller
// register, downline create) do not collect KYC, so resolve to nullable.
$kycRequired = $this->routeIs('shop.register.submit') || $this->routeIs('admin.shop.store')
    ? 'required' : 'nullable';
```

- [ ] **Step 4: Keep the existing admin-create test green**

`tests/Feature/AdminShopCreateTest.php::test_admin_can_store_new_shop` (lines 48–65) posts without KYC and will now fail. Add the KYC fields to its POST payload (same values as `validShopPayload`'s KYC block above).

- [ ] **Step 5: Keep the Dusk test green**

`tests/Browser/AdminExtended/AdminUserManagementTest.php::test_create_shop_owner` fills the create form and presses Submit without KYC. Add before `->press('Submit')`:

```php
->attach('aadhaar_card', $this->fakeImage())
->type('input[name="aadhaar_number"]', '123456789012')
->attach('pan_card', $this->fakeImage())
->type('input[name="pan_number"]', 'ABCDE1234F')
->type('input[name="date_of_birth"]', '1990-01-15')
->type('input[name="qualification"]', 'B.Com')
->type('input[name="bank_name"]', 'HDFC Bank')
->type('input[name="ifsc"]', 'HDFC0001234')
->type('input[name="account_number"]', '12345678901234')
```

- [ ] **Step 6: Run the affected tests to verify green**

Run: `php artisan test --filter=AdminShopKycTest --compact` and `php artisan test --filter=AdminShopCreateTest --compact`
Expected: PASS (4 tests, 2 classes). Dusk is verified in Task 6 (requires the MAMP browser stack) — code change only here.

- [ ] **Step 7: Format and commit**

```bash
vendor/bin/pint --dirty --format agent
git add app/Http/Requests/ShopCreateRequest.php tests/Feature/AdminShopKycTest.php tests/Feature/AdminShopCreateTest.php tests/Browser/AdminExtended/AdminUserManagementTest.php
git commit -m "$(printf 'feat: require KYC on admin shop create\n\nCo-Authored-By: Claude <noreply@anthropic.com>')"
```

---

### Task 3: Persist KYC on admin shop update (`updateOrCreate`)

**Files:**
- Modify: `app/Repositories/ShopRepository.php`
- Modify: `tests/Feature/AdminShopKycTest.php`

**Interfaces:**
- Consumes: `ShopRepository::updateByRequest($shop, $request)` (called by `Admin\ShopController@update`), `MediaRepository::storeByRequest` / `MediaRepository::updateByRequest`, `ShopKyc::updateOrCreate`.
- Produces: `storeOrUpdateDocument(?UploadedFile $file, string $path, ?Media $existing): ?Media` — a private static helper on `ShopRepository`.

- [ ] **Step 1: Write the failing test — admin update persists KYC**

Add to `tests/Feature/AdminShopKycTest.php`:

```php
use App\Models\Shop;
use App\Models\User;
use Illuminate\Http\UploadedFile;

public function test_admin_shop_update_persists_kyc(): void
{
    $shopUser = User::factory()->create(['is_active' => true]);
    $shop = Shop::factory()->create(['user_id' => $shopUser->id, 'status' => true]);

    $response = $this->actingAs($this->admin())->put(route('admin.shop.update', $shop), [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'phone' => $shopUser->phone,
        'gender' => 'male',
        'email' => $shopUser->email,
        'shop_name' => 'Updated KYC Shop',
        'address' => '456 New St',
        'aadhaar_card' => UploadedFile::fake()->image('aadhaar.jpg'),
        'aadhaar_number' => '987654321098',
        'pan_card' => UploadedFile::fake()->image('pan.jpg'),
        'pan_number' => 'ZXCVB5678Y',
        'date_of_birth' => '1985-05-20',
        'qualification' => 'M.Com',
        'bank_name' => 'ICICI Bank',
        'ifsc' => 'ICIC0001234',
        'account_number' => '998877665544',
        'other_documents' => UploadedFile::fake()->image('doc.jpg'),
    ]);

    $response->assertSessionHasNoErrors();
    $response->assertRedirect(route('admin.shop.index'));
    $this->assertDatabaseHas('shop_kyc', [
        'shop_id' => $shop->id,
        'aadhaar_number' => '987654321098',
        'pan_number' => 'ZXCVB5678Y',
        'bank_name' => 'ICICI Bank',
        'ifsc' => 'ICIC0001234',
        'qualification' => 'M.Com',
    ]);
}
```

- [ ] **Step 2: Run to verify it fails**

Run: `php artisan test --filter=test_admin_shop_update_persists_kyc --compact`
Expected: FAIL — no `shop_kyc` row exists (update path doesn't touch KYC yet).

- [ ] **Step 3: Implement the repository `updateOrCreate` block + helper**

In `app/Repositories/ShopRepository.php`:

Add imports at the top:

```php
use App\Models\Media;
use Illuminate\Http\UploadedFile;
```

Inside `updateByRequest()`, after the existing `self::update($shop, [...])` call and before `return $shop;`, add:

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
            'aadhaar_card_id' => self::storeOrUpdateDocument($request->file('aadhaar_card'), 'shops/kyc', $kyc?->aadhaarCard)?->id,
            'pan_card_id' => self::storeOrUpdateDocument($request->file('pan_card'), 'shops/kyc', $kyc?->panCard)?->id,
            'other_documents_id' => self::storeOrUpdateDocument($request->file('other_documents'), 'shops/kyc', $kyc?->otherDocuments)?->id,
            'aadhaar_number' => $request->aadhaar_number,
            'pan_number' => $request->pan_number,
            'bank_name' => $request->bank_name,
            'ifsc' => $request->ifsc,
            'account_number' => $request->account_number,
            'qualification' => $request->qualification,
        ]
    );
}
```

Add a private static helper (place it near `updateLogo` / `updateBanner`):

```php
/**
 * Store a new KYC document or update an existing one. Returns null when
 * neither a new file nor an existing document is present.
 */
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

**Why `filled()` not `has()`:** the edit form always renders the KYC inputs, so `has()` is true even when empty — saving an untouched legacy shop (no KYC row) would create an empty row. `filled()` skips the block when no KYC content is present.

- [ ] **Step 4: Run the test to verify it passes**

Run: `php artisan test --filter=test_admin_shop_update_persists_kyc --compact`
Expected: PASS.

- [ ] **Step 5: Format and commit**

```bash
vendor/bin/pint --dirty --format agent
git add app/Repositories/ShopRepository.php tests/Feature/AdminShopKycTest.php
git commit -m "$(printf 'feat: persist KYC on admin shop update\n\nCo-Authored-By: Claude <noreply@anthropic.com>')"
```

---

### Task 4: Admin create form — KYC & Bank Details card

**Files:**
- Modify: `resources/views/admin/shop/create.blade.php`
- Modify: `tests/Feature/AdminShopKycTest.php`

**Interfaces:**
- Consumes: Blade components `<x-file>` and `<x-input>` (no changes to components).
- Produces: the form inputs that `admin.shop.store` now requires (names: `aadhaar_card`, `aadhaar_number`, `pan_card`, `pan_number`, `date_of_birth`, `qualification`, `bank_name`, `ifsc`, `account_number`, `other_documents`).

- [ ] **Step 1: Write the failing view test**

Add to `tests/Feature/AdminShopKycTest.php`:

```php
public function test_admin_shop_create_page_shows_kyc_fields(): void
{
    $response = $this->actingAs($this->admin())->get(route('admin.shop.create'));

    $response->assertStatus(200);
    $response->assertSee('Aadhaar Number');
    $response->assertSee('PAN Number');
    $response->assertSee('Bank IFSC Code');
    $response->assertSee('Other Documents');
}
```

- [ ] **Step 2: Run to verify it fails**

Run: `php artisan test --filter=test_admin_shop_create_page_shows_kyc_fields --compact`
Expected: FAIL — the create page shows no KYC labels yet.

- [ ] **Step 3: Add the KYC card to the create view**

In `resources/views/admin/shop/create.blade.php`, insert after the Shop Information card's closing `</div>` (the one ending just before `</form>`, around line 205) and before `</form>`:

```blade
        <!--######## KYC & Bank Details ##########-->
        <div class="card mt-4 mb-4">
            <div class="card-body">

                <div class="d-flex gap-2 border-bottom pb-2">
                    <i class="fa-solid fa-id-card"></i>
                    <h5>
                        {{ __('KYC &amp; Bank Details') }}
                    </h5>
                </div>
                <div class="row mt-3">
                    <div class="col-md-4">
                        <div class="mt-3">
                            <x-file name="aadhaar_card" label="Aadhaar Card (JPG / PNG / PDF, max 5MB)" required="true" />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mt-3">
                            <x-input type="text" name="aadhaar_number" label="Aadhaar Number" placeholder="12-digit Aadhaar number" required="true" />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mt-3">
                            <x-file name="pan_card" label="PAN Card (JPG / PNG / PDF, max 5MB)" required="true" />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mt-3">
                            <x-input type="text" name="pan_number" label="PAN Number" placeholder="e.g. ABCDE1234F" required="true" />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mt-3">
                            <x-input type="date" name="date_of_birth" label="Date of Birth" required="true" />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mt-3">
                            <x-input type="text" name="qualification" label="Qualification" placeholder="e.g. B.Com, 12th Pass" required="true" />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mt-3">
                            <x-input type="text" name="bank_name" label="Bank Name" placeholder="Enter bank name" required="true" />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mt-3">
                            <x-input type="text" name="ifsc" label="Bank IFSC Code" placeholder="e.g. HDFC0001234" required="true" />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mt-3">
                            <x-input type="text" name="account_number" label="Bank Account Number" placeholder="Enter account number" required="true" />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mt-3">
                            <x-file name="other_documents" label="Other Documents (JPG / PNG / PDF, max 5MB, optional)" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
```

- [ ] **Step 4: Run the view test to verify it passes**

Run: `php artisan test --filter=test_admin_shop_create_page_shows_kyc_fields --compact`
Expected: PASS.

- [ ] **Step 5: Commit**

```bash
git add resources/views/admin/shop/create.blade.php tests/Feature/AdminShopKycTest.php
git commit -m "$(printf 'feat: add KYC card to admin shop create form\n\nCo-Authored-By: Claude <noreply@anthropic.com>')"
```

---

### Task 5: Admin edit form — prefilled KYC card

**Files:**
- Modify: `resources/views/admin/shop/edit.blade.php`
- Modify: `tests/Feature/AdminShopKycTest.php`

**Interfaces:**
- Consumes: `Shop::kyc` relation + `aadhaar_card_url` / `pan_card_url` / `other_documents_url` accessors (from the `ShopKyc` model), `$shop->user->date_of_birth`.
- Produces: prefilled edit inputs; document `<x-file>` inputs are **not** required (replacement only).

- [ ] **Step 1: Write the failing view test**

Add to `tests/Feature/AdminShopKycTest.php`:

```php
public function test_admin_shop_edit_page_shows_kyc_fields(): void
{
    $shopUser = User::factory()->create(['is_active' => true]);
    $shop = Shop::factory()->create(['user_id' => $shopUser->id, 'status' => true]);

    $response = $this->actingAs($this->admin())->get(route('admin.shop.edit', $shop));

    $response->assertStatus(200);
    $response->assertSee('Aadhaar Number');
    $response->assertSee('Bank IFSC Code');
    $response->assertSee('Other Documents');
}
```

- [ ] **Step 2: Run to verify it fails**

Run: `php artisan test --filter=test_admin_shop_edit_page_shows_kyc_fields --compact`
Expected: FAIL — the edit page shows no KYC labels yet.

- [ ] **Step 3: Add the prefilled KYC card to the edit view**

In `resources/views/admin/shop/edit.blade.php`, insert after the Shop Information card (the `</div>` closing the `card mt-4 mb-4` block, just before `</form>`, around line 176) and before `</form>`:

```blade
        <!--######## KYC & Bank Details ##########-->
        <div class="card mt-4 mb-4">
            <div class="card-body">

                <div class="d-flex gap-2 border-bottom pb-2">
                    <i class="fa-solid fa-id-card"></i>
                    <h5>
                        {{ __('KYC &amp; Bank Details') }}
                    </h5>
                </div>
                <div class="row mt-3">
                    <div class="col-md-4">
                        <div class="mt-3">
                            <x-file name="aadhaar_card" label="Replace Aadhaar Card (JPG / PNG / PDF, max 5MB)" />
                            @if ($shop->kyc?->aadhaarCard)
                                <a href="{{ $shop->kyc->aadhaar_card_url }}" target="_blank" class="small text-primary">
                                    <i class="fa fa-file-image me-1"></i>{{ __('Current Aadhaar card') }}
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mt-3">
                            <x-input type="text" name="aadhaar_number" label="Aadhaar Number" placeholder="12-digit Aadhaar number" :value="$shop->kyc?->aadhaar_number" />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mt-3">
                            <x-file name="pan_card" label="Replace PAN Card (JPG / PNG / PDF, max 5MB)" />
                            @if ($shop->kyc?->panCard)
                                <a href="{{ $shop->kyc->pan_card_url }}" target="_blank" class="small text-primary">
                                    <i class="fa fa-file-image me-1"></i>{{ __('Current PAN card') }}
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mt-3">
                            <x-input type="text" name="pan_number" label="PAN Number" placeholder="e.g. ABCDE1234F" :value="$shop->kyc?->pan_number" />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mt-3">
                            <x-input type="date" name="date_of_birth" label="Date of Birth" :value="$shop->user?->date_of_birth" />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mt-3">
                            <x-input type="text" name="qualification" label="Qualification" placeholder="e.g. B.Com, 12th Pass" :value="$shop->kyc?->qualification" />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mt-3">
                            <x-input type="text" name="bank_name" label="Bank Name" placeholder="Enter bank name" :value="$shop->kyc?->bank_name" />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mt-3">
                            <x-input type="text" name="ifsc" label="Bank IFSC Code" placeholder="e.g. HDFC0001234" :value="$shop->kyc?->ifsc" />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mt-3">
                            <x-input type="text" name="account_number" label="Bank Account Number" placeholder="Enter account number" :value="$shop->kyc?->account_number" />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mt-3">
                            <x-file name="other_documents" label="Replace Other Documents (JPG / PNG / PDF, max 5MB)" />
                            @if ($shop->kyc?->otherDocuments)
                                <a href="{{ $shop->kyc->other_documents_url }}" target="_blank" class="small text-primary">
                                    <i class="fa fa-file-image me-1"></i>{{ __('Current other documents') }}
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
```

Note: `<x-input>`/`<x-file>` render `old($name)` with precedence, so a server validation failure on edit preserves the just-submitted values.

- [ ] **Step 4: Run the view test to verify it passes**

Run: `php artisan test --filter=test_admin_shop_edit_page_shows_kyc_fields --compact`
Expected: PASS.

- [ ] **Step 5: Commit**

```bash
git add resources/views/admin/shop/edit.blade.php tests/Feature/AdminShopKycTest.php
git commit -m "$(printf 'feat: add prefilled KYC card to admin shop edit form\n\nCo-Authored-By: Claude <noreply@anthropic.com>')"
```

---

### Task 6: Full verification

**Files:** none (verification only)

- [ ] **Step 1: PHP syntax + formatting**

Run: `php -l app/Http/Requests/ShopCreateRequest.php && php -l app/Repositories/ShopRepository.php && vendor/bin/pint --dirty --format agent`
Expected: no syntax errors; Pint reports passed.

- [ ] **Step 2: Full feature test suite**

Run: `php artisan test --compact`
Expected: all Feature tests PASS — including `ShopRegistrationVerificationTest`, `AdminShopCreateTest`, `AdminShopKycTest`, `MapIntegrationTest`, `DownlineRecruitmentTest`, `PayoutNetworkTest`.

- [ ] **Step 3: Browser smoke (Dusk or manual)**

Run: `php artisan dusk --filter=test_create_shop_owner` (MAMP + ChromeDriver required).
Expected: PASS — the create form now submits with KYC.

Manual fallback: `http://localhost:8888/janmitram-app/admin/shop/create` → fill all fields including KYC → Submit → shop listed; `http://localhost:8888/janmitram-app/admin/shop/{id}/edit` → KYC prefilled, change bank_name → save → persists.

- [ ] **Step 4: Confirm regression on the other `ShopCreateRequest` callers**

Run: `php artisan test --filter=DownlineRecruitmentTest --compact`
Expected: PASS — downline-create posts no KYC fields and still succeeds (KYC nullable on `shop.payout.network.store`; `storeByRequest` guard skips KYC creation).

- [ ] **Step 5: Commit any formatting leftovers**

```bash
git add -A
git commit -m "$(printf 'chore: format admin shop KYC work\n\nCo-Authored-By: Claude <noreply@anthropic.com>')" || echo "nothing to commit"
```

---

## Self-Review

- **Spec coverage:** Validation toggle (Task 2 ✓), repository `updateOrCreate` + helper (Task 3 ✓), create view card (Task 4 ✓), edit view card prefilled (Task 5 ✓), imports + `filled()` guard (Task 3 ✓), no migration/table change (verified — table exists), API/downline unaffected (Task 6 Step 4 ✓), verification steps (Task 6 ✓). DOB handled by existing `UserRepository` paths (no change needed — confirmed).
- **Placeholder scan:** no TBD/TODO; every code step has concrete code.
- **Type consistency:** `storeOrUpdateDocument` signature matches its calls in Task 3; field names consistent across validation (Task 2), repository (Task 3), and views (Tasks 4–5); test payload keys match route field names.
