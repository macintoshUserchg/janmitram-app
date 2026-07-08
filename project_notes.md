# Project Notes — Janmitram App

Laravel 11 ecommerce SPA. This document captures architecture decisions, conventions, and
structural guidelines for contributors.

---

## Stack

| Layer        | Technology |
|--------------|------------|
| PHP          | 8.2 |
| Framework    | Laravel 11 (`laravel/framework` ^11.31) |
| Auth         | Laravel Sanctum ^4, spatie/laravel-permission ^6 |
| Modules      | nwidart/laravel-modules ^12 |
| Frontend     | Vue 3 + Vite + Tailwind CSS 3 + Pinia + vue-router + vue-i18n |
| Payments     | Stripe, PayPal, Razorpay, Paystack, Paysafecard, plus gateways (see `Modules/modules/`) |
| Messaging    | Twilio, Vonage (Nexmo), MessageBird, Telesign, Firebase |
| Exports      | maatwebsite/excel, mpdf, milon/barcode, endroid/qr-code |
| AI          | openai-php/laravel, google/apiclient |

---

## Architecture Overview

```
app/
├── Console/        Artisan commands
├── Enums/          Strongly-typed enums
├── Events/         Event classes
├── Exceptions/     Handler + custom exceptions
├── Exports/        Excel export definitions
├── Http/
│   ├── Controllers/   Admin, API, Gateway, Seller, Shop (thin controllers)
│   ├── Middleware/    Auth, permissions, subscription, localization
│   ├── Requests/      Form Requests (validation lives here)
│   └── Resources/     Eloquent API Resources (API output)
├── Listeners/      Event listeners
├── Mail/           Mailable classes
├── Models/         Eloquent models + Scopes/
├── Providers/      App, Auth, Broadcast, Event, Permission, Route, Sms
├── Repositories/   Data-access abstraction
├── Rules/          Custom validation rules
├── Services/       Business logic (Chat, SMS, Notification)
├── Support/        Helpers & utilities
└── View/           View models / composers

Modules/            nwidart feature modules (purchase, report) + payment gateways
resources/js/       Vue SPA (router, stores, components)
tests/              Feature/ and Unit/ (PHPUnit)
```

> Note: This project keeps the Laravel 10-style structure (classic `app/Providers`,
> `app/Http/Kernel.php`, `RouteServiceProvider.php`). That is intentional and supported by
> Laravel 11 — do not force-migrate to the streamlined Laravel 11 bootstrap structure
> unless explicitly requested.

---

## Structural Guidelines

### 1. Controllers stay thin

Controllers should orchestrate, not compute. Push logic down:

- **Complex queries** → Eloquent scopes (`app/Models/Scopes/`) or `Repository` classes.
- **Business logic** → `app/Services/` (e.g. `SmsGatewayService`, `NotificationServices`).
- **Output shaping** → Eloquent API Resources in `app/Http/Resources/`.

Example controller flow:

```php
declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\CheckoutRequest;
use App\Http\Resources\OrderResource;
use App\Services\Contracts\CheckoutService;
use Illuminate\Http\Resources\Json\JsonResource;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly CheckoutService $checkout,
    ) {}

    public function store(CheckoutRequest $request): JsonResource
    {
        $order = $this->checkout->place($request->validated());

        return new OrderResource($order);
    }
}
```

### 2. Validation via Form Requests

All validation lives in `app/Http/Requests/`. Return typed validated data with
`$request->validated()`.

```php
declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'cart_id' => ['required', 'integer', 'exists:carts,id'],
            'address_id' => ['required', 'integer', 'exists:addresses,id'],
        ];
    }
}
```

### 3. Strict types & typing

- Every PHP file begins with `declare(strict_types=1);`.
- Explicit return types and argument type-hints on all methods.
- Constructor property promotion for dependencies.

```php
declare(strict_types=1);

use App\Models\User;

public function register(User $user, string $token): bool
{
    // ...
}
```

### 4. Database & Eloquent

- **CamelCase** for model properties, **snake_case** for DB columns.
- Always define explicit relationship return types:

```php
use Illuminate\Database\Eloquent\Relations\HasMany;

public function orders(): HasMany
{
    return $this->hasMany(Order::class);
}
```

- **Prevent N+1**: eager load relationships with `with()` in queries, especially in
  controllers and API Resources.
- Use Eloquent scopes for reusable query constraints (see `app/Models/Scopes/`).
- Keep migrations complete: when modifying a column, re-declare all existing attributes
  or they will be dropped.
- Prefer a `casts()` method on models (Laravel 11) over the `$casts` array.

### 5. Styling & standards

- PSR-12 + Laravel Pint conventions.
- After editing PHP, run `vendor/bin/pint --dirty --format agent` to auto-fix style.
- Use PHPDoc blocks over inline comments; only comment non-obvious logic.

---

## Conventions in Use

- **API responses** are wrapped in Eloquent API Resources (`app/Http/Resources/`).
- **Permissions** use spatie/laravel-permission; gate access via `CheckPermission`
  middleware and policies.
- **Localization** handled by `LocalizationManage` middleware + `Language` model; the SPA
  uses vue-i18n. New copy should be translation-aware.
- **Modules** are feature-level (purchase, report) and payment gateways under
  `Modules/modules/`. Respect `config/modules.php` discovery.
- **Services** centralize third-party integrations (SMS via `SmsServiceProvider`, chat,
  notifications).
- **Repositories** abstract data access where used; reuse before adding new queries inline.

---

## Common Workflows

### Frontend not updating?

Run one of:

```bash
npm run dev      # hot reload
npm run build    # production bundle
composer run dev # artisan + vite together
```

### Database

```bash
php artisan migrate
```

`.env` (MAMP): `DB_DATABASE=ready_ecommerce`, `DB_USERNAME=root`, `DB_PASSWORD=root`.

### Tests (PHPUnit)

```bash
php artisan make:test --phpunit Feature/CheckoutTest   # new feature test
php artisan test --compact                             # full suite
php artisan test --compact --filter=testName           # single test
```

- Use factories; do not hand-craft model data in tests unless necessary.
- Do not delete existing tests without approval.

### Administration

```bash
vendor/bin/pint --dirty --format agent   # style fix
php artisan route:list                   # inspect routes
php artisan tinker --execute 'User::count();'
```

---

## Notable Integrations

- **Auth flows**: customer, seller (`ShopAuthenticate`), admin (`CheckHasRootUser`),
  driver/rider (`RiderLoginRequest`). Sanctum tokens for the SPA.
- **Payments**: gateway strategy pattern under `Modules/modules/` + `Gateway` controllers.
- **Notifications**: Pusher (broadcasting), Firebase (`kreait/laravel-firebase`),
  SMS providers behind `SmsGatewayService`.
- **Media**: `Media` model + `mews/purifier` for sanitized HTML; `attachments/`, `images/`,
  `audio/`, `reportImage/` directories.

---

## Security Notes

- Recent fixes addressed SQL injection, Zip Slip, and path traversal — keep user input
  validated and parameterized; never build file paths from raw request data.
- `DemoModeMiddleware` blocks mutating actions in demo environments.
- `VerifyCsrfToken` and `ValidateSignature` middleware are active.

---

_Last updated: 2026-07-08. Generated for contributor onboarding._
