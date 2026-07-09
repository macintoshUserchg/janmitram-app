# Project Notes — Janmitram App

Laravel 11 multi-tenant ecommerce SPA with a Vue 3 customer frontend, two Blade-based
dashboards (admin & seller), and dedicated mobile apps (Flutter/Dart). Comprehensive
payment gateway support, real-time chat, SMS/Firebase notifications, and multi-currency
internationalization.

> **Style note**: All new code should follow the guidelines in § **Structural Guidelines**:
> strict types, thin controllers, Form Requests, typed relationships, eager loading.

---

## Stack

| Layer | Technology |
|-------|------------|
| **PHP** | 8.2 |
| **Framework** | Laravel 11 (`laravel/framework` ^11.31 — classic 10-style structure, **not** streamlined) |
| **Auth** | Laravel Sanctum ^4 (token-based API) + web session (admin) |
| **RBAC** | spatie/laravel-permission ^6 |
| **Modules** | nwidart/laravel-modules ^12 |
| **Frontend (customer)** | Vue 3 + Vite + Tailwind CSS 3 + Pinia + vue-router + vue-i18n |
| **Admin & seller UI** | Blade templates + Tailwind CSS |
| **Mobile apps** | Flutter/Dart (external — API surface in `routes/api.php`) |
| **Payments** | Stripe, PayPal, Razorpay, PayStack, PayU, Bkash, AamarPay, CashFree, JazzCash, PayTabs, QiCard |
| **Real-time** | Pusher (Echo configured in JS, server-side `pusher/pusher-php-server`) |
| **SMS** | Twilio, Vonage (Nexmo), MessageBird, Telesign |
| **Firebase** | Cloud Messaging (notifications) |
| **AI** | OpenAI API (`openai-php/laravel`), Google API (`google/apiclient`) |
| **Exports** | maatwebsite/excel, mpdf, milon/barcode, endroid/qr-code |
| **DB** | MySQL (MAMP local: `ready_ecommerce`) |
| **Testing** | PHPUnit ^11 |
| **Code style** | Laravel Pint ^1 |

---

## Architecture Overview

```
janmitram-app/
├── app/
│   ├── Console/Commands/           Artisan commands (LetsFix, orderProductUpdate)
│   ├── Enums/                      10 backed enums (OrderStatus, PaymentMethod, Roles, …)
│   ├── Events/                     AdminProductRequest, SendMessageToUser, RiderLocationUpdated, …
│   ├── Exceptions/Handler.php      Custom 413 handler; register/report stubs
│   ├── Exports/                    Excel export definitions
│   ├── helpers.php                 Global functions (showCurrency, getDistance, userCart, …)
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── API/                Customer API (Auth, Cart, Order, Product, Chat, …)
│   │   │   ├── API/Auth/           Login, register, forgot-password, OTP
│   │   │   ├── API/Seller/         Seller mobile-app endpoints
│   │   │   ├── API/Rider/          Rider/driver mobile-app endpoints
│   │   │   ├── Admin/              ~45 web controllers (Dashboard, Order, Banner, Brand, …)
│   │   │   ├── Shop/               Vendor/seller web dashboard controllers
│   │   │   ├── Seller/             Seller mobile chat controller
│   │   │   ├── Gateway/            Payment gateway router + per-gateway ProcessController
│   │   │   └── Controller.php      Base controller
│   │   ├── Kernel.php              Middleware groups, aliases, global stack
│   │   ├── Middleware/             14 middleware classes (auth, permission, demo, …)
│   │   ├── Requests/               ~80 Form Request classes
│   │   └── Resources/              ~50 Eloquent API Resource classes
│   ├── Listeners/                  Event listeners (OrderMail, SendOTP, TestMail)
│   ├── Mail/                       Mailable classes (OrderMail, SendOTP, TestMail)
│   ├── Models/
│   │   ├── Scopes/                 3 global scopes (ActiveScope, hasSubscription, PosOrderFalse)
│   │   └── 80+ Eloquent models (Product, Order, Shop, User, Cart, …)
│   ├── Providers/
│   │   ├── AppServiceProvider.php
│   │   ├── AuthServiceProvider.php
│   │   ├── BroadcastServiceProvider.php
│   │   ├── EventServiceProvider.php
│   │   ├── PermissionServiceProvider.php
│   │   ├── RouteServiceProvider.php    Routes + rate limiting (60/min for API)
│   │   └── SmsServiceProvider.php
│   ├── Repositories/               ~55 repository classes (data-access abstraction)
│   ├── Rules/                      4 custom rules (CaptchaValidate, EmailRule, EnumValue, …)
│   ├── Services/                   Chat, SmsGatewayService, NotificationServices, …
│   └── Support/Repositories/       Additional repository helpers
├── bootstrap/app.php               Classic Laravel 10-style bootstrap
├── config/
│   ├── acl.php                     Complete permission tree (admin, shop, shopMultiShop)
│   ├── permission.php              spatie/laravel-permission config
│   ├── modules.php                 nwidart/laravel-modules config
│   ├── sanctum.php, services.php, …
│   └── themeColors.php             Theme colour palette config
├── database/                       Migrations, seeders, factories
├── Modules/                        nwidart modules (purchase, report)
├── resources/
│   ├── js/
│   │   ├── app.js                  Vue SPA entry
│   │   ├── bootstrap.js            Axios config, Echo/Pusher stubs
│   │   ├── router/index.js         ~35 route definitions
│   │   ├── stores/                 Pinia stores (Auth, Basket, Chat, GuestAddress, Master)
│   │   ├── layouts/                4 layouts (default, auth, blank, blog)
│   │   ├── components/            ~90 Vue components
│   │   └── pages/                  ~40 Vue page views
│   ├── views/                      Blade views (admin, shop, layouts, mail, PDF, …)
│   └── css/                        Additional CSS
├── routes/
│   ├── api.php                     ~90 API endpoints (customer, seller, rider)
│   ├── web.php                     SPA entry, admin routes, payment callbacks, shop routes
│   ├── channels.php                Broadcasting channels
│   └── console.php                 Console commands
├── tests/                          Feature + Unit (PHPUnit)
├── public/                         Vite build output + htaccess
├── .htaccess                       Root htaccess for MAMP subdirectory deployment
└── assets/                         Compiled asset directories (build/, icons/, images/, …)
```

---

## Frontends

### 1. Customer SPA (Vue 3 + Vite)

Single-page application at `/` served via `resources/views/app.blade.php`.

| Layer | Mechanism |
|-------|-----------|
| **State** | Pinia stores: `AuthStore`, `BasketStore`, `ChatStore`, `GuestAddressStore`, `MasterStore` |
| **Routing** | `vue-router` — lazy-loaded page components with layout meta |
| **API** | Axios (`window.axios`) — base URL from `<meta name="base-url">`, auth via `Authorization: Bearer {token}` header |
| **Localization** | `vue-i18n` + Laravel JSON translation files at `/lang/{locale}.json` |
| **Auth persistence** | `pinia-plugin-persistedstate` (localStorage) |
| **Real-time** | Pusher (Echo commented out in `bootstrap.js` — ready to enable) |

Key stores:
- **AuthStore**: token, user, addresses, favorites, login/logout/register modals
- **BasketStore**: cart items, address, guest token, checkout
- **ChatStore**: messages, active shop
- **MasterStore**: app settings, theme, currencies, languages (fetched once)

### 2. Admin Panel (Blade + Tailwind)

Full CRUD management dashboard in `resources/views/admin/`. ~45 controllers handle:

- **Orders**: listing, status changes, payment toggle, rider assignment
- **Products**: approve/reject seller products, categories, brands, units, sizes, colors
- **Users**: customers, employees, riders, roles & permissions
- **Settings**: general, business, payment gateways, SMS, mail, Firebase, Pusher, reCAPTCHA
- **Content**: banners, blogs, pages, menus, social links, coupons, flash sales
- **AI features**: AI content generation for pages and blogs via `AiPromptController`
- **Finance**: withdrawals, VAT/tax, subscription plans, transaction history

### 3. Seller Dashboard (Blade + Tailwind)

Vendor management panel in `resources/views/shop/`. Controllers under `app/Http/Controllers/Shop/`:

- Products, orders, flash sales, vouchers, POS, bulk import/export
- Supplier management, purchase orders, purchase returns
- Employee management with granular permissions
- Gallery management
- Returns processing

### 4. Mobile Apps (Flutter/Dart)

The API surface at `routes/api.php` is documented for customer, seller, and rider mobile
apps. Auth uses Sanctum personal access tokens. Guest users get a `X-Guest-Token` header
for cart persistence across devices.

---

## Authentication Flows

### Customer (Sanctum tokens — mobile app / SPA)
1. **Register** → OTP sent via SMS/email → OTP verify → token issued
2. **Login** → email/phone + password → token returned
3. **Social login** → Google (OAuth) token exchange
4. **Guest cart** → `X-Guest-Token` header → `CartAccessToken` model → merges on login
5. **Logout** → token revoked

### Seller (Sanctum tokens — mobile app)
Separate login controller (`API/Seller/LoginController`):
- Register with shop creation, email/phone check, OTP verification
- Shop authentication middleware (`authShop`)

### Rider (Sanctum tokens — mobile app)
Separate login controller (`API/Rider/LoginController`):
- Register, OTP, password creation, location updates
- Status-specific order views

### Admin (Web session — Blade)
Standard Laravel session-based auth via `routes/web.php`. Middleware: `auth`, `checkPermission`.

---

## Payment Gateway Architecture (Pluggable Strategy)

Polymorphic per-gateway processor pattern under `app/Http/Controllers/Gateway/`:

```
Gateway/
├── PaymentGatewayController.php    Router: resolves gateway → delegates to ProcessController
├── Stripe/ProcessController.php
├── PayPal/ProcessController.php
├── Razorpay/ProcessController.php
├── PayStack/ProcessController.php
├── PayU/ProcessController.php
├── Bkash/ProcessController.php
├── AamarPay/ProcessController.php
├── CashFree/ProcessController.php
├── JazzCash/ProcessController.php
├── PayTabs/ProcessController.php
└── QiCard/ProcessController.php
```

**Flow**:
1. `PaymentGatewayController::payment()` looks up gateway by name → gets alias → builds FQCN
2. Calls `{Gateway}\ProcessController::process($gateway, $payment)` statically
3. Gateway returns a redirect URL; success/cancel handlers update order state
4. `config/acl.php` controls gateway enable/disable per permissions

The `PaymentMethod` enum lists all supported methods plus `cash` and generic `online`.

---

## Roles & Permissions (spatie/laravel-permission)

Defined in `config/acl.php` — structured permission tree:

| Role | Scope | Key permissions |
|------|-------|-----------------|
| **root** | Super-admin | Everything |
| **admin** | Admin panel | ~45 resource groups (order, product, banner, blog, rider, …) |
| **adminMultiShop** | Multi-shop admin | shop management, subscription plans, withdraw approvals |
| **shop** | Vendor | order/product management, POS, employees, gallery |
| **shopMultiShop** | Multi-shop vendor | subscription, withdraw, dashboard |
| **customer** | API consumer | Own profile, addresses, orders, cart |
| **driver/rider** | Delivery | Assigned orders, location updates |
| **visitor** | Unauthenticated | Public read endpoints |

Custom middleware layer:
- `CheckPermission.php` — checks user roles against ACL config
- `CheckSubscription.php` — verifies active subscription for shops (web group)
- `CheckHasRootUser.php` — redirects to setup if root user missing

---

## API Surface (`routes/api.php`)

~90 endpoints organized by actor:

### Public (no auth required)
- `GET /master` — app settings, theme, currencies, languages
- `GET /home` — homepage data (featured products, banners, categories)
- `GET /categories`, `/sub-categories`, `/products`, `/product-details`
- `GET /shops`, `/shop`, `/shop-categories`, `/top-shops`, `/popular-products`
- `GET /reviews`, `/banners`, `/flash-sales`, `/blogs`, `/legal-pages/{slug}`
- `GET /countries`, `/areas`, `/get-vouchers`
- `POST /support`, `/contact-us`
- **Auth**: login, registration, social login, OTP, password reset

### Protected (`auth:sanctum`)
- Profile CRUD, addresses, change password
- **Cart**: list, add, increment/decrement, checkout, clear
- **Orders**: list, details, place, re-order, cancel, payment
- **Favorites**: add/remove, list
- **Reviews**: submit
- **Vouchers**: collect, apply
- **Chat**: send/receive messages, unread count, shops list
- **Returns**: create, history, details

### Seller prefix (`/seller/*`)
- Auth (register, OTP, forgot password)
- Dashboard stats, profile, shop settings
- Orders, products

### Rider prefix (`/rider/*`)
- Auth (register, OTP, password creation)
- Profile, location updates
- Orders with status-based filtering

---

## Eloquent Models — Key Relationships

80+ models. Notable patterns:

| Model | Key Traits | Notable Relations |
|-------|-----------|-------------------|
| **User** | `HasRoles` (Spatie), `HasApiTokens` (Sanctum) | `customer`, `shop`, `addresses` |
| **Shop** | — | `products`, `orders`, `categories`, `user` |
| **Product** | — | `categories`, `shop`, `reviews`, `variants` (color/size) |
| **Order** | — | `products`, `payment`, `customer`, `shop` |
| **Cart** | — | `products`, `customer` (or `access_token` for guest) |
| **Customer** | — | `user`, `addresses`, `orders`, `cart` |
| **GeneraleSetting** | Singleton | App-wide configuration model |
| **PaymentGateway** | — | `name`, `alias`, `is_active` — drives gateway routing |

### Global Scopes (`app/Models/Scopes/`)
- **ActiveScope**: filters active records
- **hasSubscription**: limits to shops with active subscription
- **PosOrderFalse**: excludes POS orders from main order queries

---

## Repository Pattern

55+ repository classes in `app/Repositories/` abstract data access. Controllers
inject repositories via the constructor. Repositories typically wrap Eloquent queries
and return collections, paginated results, or single models.

Example: `ProductRepository`, `OrderRepository`, `CartRepository`, `ShopRepository`.

---

## Globalization & Localization

- **Languages**: managed via `Language` model + `Language` controller (CRUD + import/export)
- **Translation files**: JSON files in `lang/{locale}.json`
- **Frontend**: `vue-i18n` reads from `/lang/{locale}` route
- **Middleware**: `LocalizationManage` sets app locale from session/header
- **Currencies**: `Currency` model with default flag, position (prefix/suffix), and `showCurrency()` helper
- **Theme colors**: `ThemeColor` model with primary and variant shades

---

## Guest Cart Flow

1. Unauthenticated users hit API with `X-Guest-Token` header
2. `helpers.php:cartAccessToken()` resolves guest token → `CartAccessToken` → `customer_id`
3. `userCart()` scopes `Cart` queries by token (or customer ID if authenticated)
4. On login, guest cart merges into authenticated user's cart

---

## Enums (`App\Enums`)

| Enum | Values | Used In |
|------|--------|---------|
| `DeductionType` | inclusive, exclusive | VAT/tax |
| `DeliveryChargeType` | Per Order, Per Product | Delivery config |
| `DiscountType` | Amount, Percentage | Coupons, flash sales |
| `LegalPages` | Privacy Policy, Terms, Refund, Shipping, About Us | Legal pages |
| `OrderStatus` | Pending → Confirm → Processing → Pickup → On The Way → Delivered → Cancelled | Order lifecycle |
| `PaymentMethod` | Cash, Online, Stripe, PayPal, Razorpay, PayStack, … | Payment processing |
| `PaymentStatus` | Pending, Paid | Order payments |
| `ReturnOrderStatus` | Pending, Approved, Damaged, Mismatch | Returns |
| `Roles` | root, admin, shop, customer, visitor, driver, supplier | RBAC |
| `SubscriptionStatus` | pending, active, cancelled, expired | Shop subscriptions |

---

## Web Routes (`routes/web.php`)

Key groups:
1. **/** — Vue SPA entry point (returns `app.blade.php`)
2. **/lang/{locale}** — JSON translation files
3. **/payment/*** — Payment gateway callbacks (success, cancel, process)
4. **/admin/** — Admin dashboard (auth + checkPermission middleware)
5. **/shop/** — Seller/vendor dashboard (authShop + checkPermission middleware)
6. **/gateway/purchase/** — Module-based purchase gateway routes

---

## Structural Guidelines

### 1. Controllers stay thin

Controllers should orchestrate, not compute. Push logic down:
- **Complex queries** → Eloquent scopes (`app/Models/Scopes/`) or `Repository` classes.
- **Business logic** → `app/Services/` (e.g. `SmsGatewayService`, `NotificationServices`).
- **Output shaping** → Eloquent API Resources in `app/Http/Resources/`.

```php
declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\CheckoutRequest;
use App\Http\Resources\OrderResource;
use App\Repositories\OrderRepository;

class OrderController extends Controller
{
    public function __construct(
        private readonly OrderRepository $orders,
    ) {}

    public function store(CheckoutRequest $request): OrderResource
    {
        $order = $this->orders->place(
            $request->user(),
            $request->validated(),
        );

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
    // …
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
- Keep migrations complete: when modifying a column, re-declare all attributes or they
  will be dropped.
- Prefer a `casts()` method on models (Laravel 11 style) over the `$casts` property.

### 5. Styling & standards

- PSR-12 + Laravel Pint conventions.
- After editing PHP, run `vendor/bin/pint --dirty --format agent` to auto-fix style.
- Use PHPDoc blocks over inline comments; only comment non-obvious logic.

---

## Common Workflows

### Frontend not updating?

```bash
npm run dev        # hot reload on changes
npm run build      # production bundle
composer run dev   # artisan + vite together
```

### Database (MAMP)

```bash
php artisan migrate
```

`.env`: `DB_DATABASE=ready_ecommerce`, `DB_USERNAME=root`, `DB_PASSWORD=root`.

### Tests (PHPUnit)

```bash
php artisan make:test --phpunit Feature/CheckoutTest   # new feature test
php artisan test --compact                              # full suite
php artisan test --compact --filter=testName            # single test
```

- Use factories; do not hand-craft model data in tests.
- Do not delete existing tests without approval.

### Administration

```bash
vendor/bin/pint --dirty --format agent   # auto-fix code style
php artisan route:list                   # inspect all routes
php artisan route:list --path=api        # filter API routes
php artisan config:show app.name         # read config value
php artisan tinker --execute 'User::count();'
```

---

## Development Conventions

- **New API endpoints**: add to existing controller or create one → write Form Request
  → add API Resource → register in `routes/api.php` under correct auth group.
- **New payment gateway**: add enum case → create `ProcessController` in
  `app/Http/Controllers/Gateway/{Name}/` → register in `PaymentGateway` model via admin.
- **New admin feature**: controller in `app/Http/Controllers/Admin/` → permissions in
  `config/acl.php` → views in `resources/views/admin/` → routes in `routes/web.php`
  admin group.
- **Role/permission changes**: update `config/acl.php`, run seeder, then assign via
  admin panel.
- **Scheduled tasks**: define in `app/Console/Kernel.php::schedule()` — currently empty.
- **Module features**: use `php artisan module:make` for nwidart modules under
  `Modules/` directory.
- **PHP code style**: always run `vendor/bin/pint --dirty --format agent` before
  committing PHP changes.

---

## Notable Integrations

| Integration | Package | Usage |
|-------------|---------|-------|
| **Pusher** | `pusher/pusher-php-server`, `pusher-js` | Real-time notifications & chat (server wired, JS ready but commented) |
| **Firebase** | `kreait/laravel-firebase` | Push notifications to mobile devices |
| **Firebase FCM** | `kreait/laravel-firebase` | Admin panel config → sends to `DeviceKey` tokens |
| **OpenAI** | `openai-php/laravel` | AI content generation for pages and blogs |
| **Google APIs** | `google/apiclient` | Social login, Google Maps, reCAPTCHA |
| **Twilio** | `twilio/sdk` | SMS OTP & notifications |
| **Vonage** | `vonage/client` | SMS via `NexmoService` |
| **MessageBird** | `messagebird/php-rest-api` | SMS via `MessageBirdService` |
| **Telesign** | `telesign/telesign` | SMS via `TelesignService` |
| **HTML Purifier** | `mews/purifier` | Sanitized rich text content |
| **QR Code** | `endroid/qr-code` | Order/product QR generation |
| **Barcode** | `milon/barcode` | Product barcode generation |
| **HTML→PDF** | `mpdf/mpdf` | Invoice PDF generation |
| **Excel** | `maatwebsite/excel` | Bulk product import/export |
| **LightGallery** | `lightgallery` | Product image gallery (frontend) |
| **Swiper** | `swiper` | Product carousels (frontend) |
| **vue-toastification** | `vue-toastification` | Frontend toast notifications |
| **vue-select** | `vue-select` | Frontend select components |
| **vue3-star-ratings** | `vue3-star-ratings` | Product ratings display |
| **vue3-google-map** | `vue3-google-map` | Map display on frontend |

---

## Security Notes

- **Recent fixes resolved**: SQL injection vectors, Zip Slip vulnerability (file upload
  path traversal), general path traversal — keep user input validated and parameterized;
  never build file paths from raw request data.
- **Spatie permissions** gate all admin/shop actions via `CheckPermission` middleware,
  backed by `config/acl.php` permission tree.
- **Demo mode**: `DemoModeMiddleware` blocks mutating actions (POST/PUT/DELETE) in
  demo environments.
- **CSRF**: `VerifyCsrfToken` protects web routes; API uses Sanctum tokens.
- **HTTPS**: `TrustProxies` middleware handles reverse-proxy SSL termination.
- **.env / composer.json**: blocked from direct access via root `.htaccess`.
- **HTML Purifier**: `mews/purifier` sanitizes rich content before storage.
- **File uploads**: always validate MIME types, use Storage facade, never trust
  extension alone.

---

## Tips & Gotchas

- **MAMP subdirectory deployment**: root `.htaccess` routes everything through
  `index.php` while preserving asset paths. Requires `DirectorySlash Off`.
- **Vite manifest errors**: run `npm run build` if you see
  `Illuminate\Foundation\ViteException`.
- **Guest token header**: all cart-related API calls from unauthenticated clients
  must include `X-Guest-Token`. The SPA manages this transparently via BasketStore.
- **Cache**: `generaleSetting()` is cached for 30 days — clear with
  `Cache::forget('generale_setting')` after changes.
- **Admin user setup**: first run needs a root user — hit
  `/admin/create-root` or the dedicated artisan command.
- **Payment gateway errors**: gateways that aren't configured return a JSON
  `{error: "…"}` response, not a redirect.

---

_Last updated: 2026-07-09. Generated for contributor onboarding._
