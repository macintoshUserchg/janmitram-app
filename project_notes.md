# Project Notes — Janmitram App

Laravel 11 multi-vendor ecommerce platform with a Vue 3 customer SPA, two
Blade-based dashboards (admin & seller), dedicated mobile apps
(Flutter/Dart), and an integrated **Option A: Strict Warehouse-Only Stock Management System**.
Comprehensive payment-gateway support, real-time chat, SMS/Firebase notifications, and multi-currency i18n.

> **Style note**: All new code should follow the guidelines in § **Structural
> Guidelines**: strict types, thin controllers, Form Requests, typed
> relationships, eager loading.

_Last verified against the codebase: 2026-08-02. Major findings from a full
deep analysis: the nwidart `purchase`/`report` modules are asset-only stubs
(no PHP); `CheckPermission`/`acl.php` are defined but NOT wired into any route
(admin panel is `role:root`-only); the `warehouse_stock` table is **singular**;
three gateway callback routes (`paypal.payment.success`, `bkash.payment.execute`,
`paytabs.payment.callback`) are referenced but not registered; an MLM payout
engine was added (2026-08-01) and is underdocumented below. See "Known Gaps &
Docs-vs-Reality" near the end._

---

## Stack

| Layer | Technology |
|-------|------------|
| **PHP** | 8.2 |
| **Framework** | Laravel 11 (`laravel/framework` ^11.31 — classic 10-style structure retained: `app/Http/Kernel.php`, `app/Exceptions/Handler.php`, `app/Console/Kernel.php`; `bootstrap/app.php` exists and is used by `public/index.php`) |
| **Auth** | Laravel Sanctum ^4 (token-based API) + web session (admin/seller) |
| **RBAC** | spatie/laravel-permission ^6 |
| **Modules** | nwidart/laravel-modules ^12 — **installed but the `purchase`/`report` modules are asset-only stubs (no PHP); see "Known Gaps"** |
| **Warehouse Architecture** | Option A Strict Warehouse Architecture (central master catalog, warehouse stocks, shop-linked logistics hubs, immutable stock ledger auditing) |
| **Frontend (customer)** | Vue 3 + Vite + Tailwind CSS 3 + Pinia + vue-router + vue-i18n |
| **Admin & seller UI** | Blade templates styled with **Bootstrap 5 + Tailwind CSS** (both loaded; persistent collapsed sidebar) |
| **Mobile apps** | Flutter/Dart (external — API surface in `routes/api.php`) |
| **Payments** | 11 pluggable gateways via `Gateway/{alias}/ProcessController`: AamarPay, Bkash, CashFree, JazzCash, PayPal, PayStack, PayTabs, PayU, QiCard, Razorpay, Stripe. `PaySafeCard` is a composer dep with **no integration** (no controller/seeder/enum). No webhooks — redirect/return-URL verification only. |
| **Real-time** | Pusher (Echo commented out in `bootstrap.js`; raw `pusher-js` used directly; server-side `pusher/pusher-php-server`) |
| **SMS** | Twilio, Vonage (Nexmo), MessageBird, Telesign |
| **Firebase** | Cloud Messaging (push notifications) |
| **AI** | OpenAI API (`openai-php/laravel`), Google API (`google/apiclient`) |
| **Exports & Invoices** | maatwebsite/excel, mpdf (invoice PDF generation), milon/barcode, endroid/qr-code |
| **DB** | MySQL (MAMP local: `janmitram`) via a single `mysql` connection |
| **MLM** | Network-marketing payout engine (`PayoutService`) — referral codes, `parent_shop_id` tree, Phase-1 10% personal + Phase-2 tiered group-sales bonus, 90-day deactivation; added 2026-08-01 |
| **Testing** | PHPUnit ^11 (13 feature test files, 56 test methods, 1 unit) + Laravel Dusk ^8 (21 test classes, 89 test methods) |
| **Code style** | Laravel Pint ^1 |

---

## Architecture Overview

```
janmitram-app/
├── WORKFLOW_PROJECT.md             End-to-End Architecture & Option A Warehouse Workflow Docs
├── app/
│   ├── Console/Commands/           Artisan commands (CalculateMonthlyPayouts, DeactivateInactiveMembers, ImportCI3Products, LetsFix, orderProductUpdate, TruncateData)
│   ├── Enums/                      7 backed enums (OrderStatus, PaymentMethod, PaymentStatus, ReturnOderStatus, Roles, SubscriptionStatus, DiscountType)
│   ├── Events/                     AdminProductRequest, SendMessageToUser, RiderLocationUpdated, …
│   ├── Exceptions/                 Handler.php (Custom 413 handler), InsufficientStockException.php
│   ├── Exports/                    Excel export definitions
│   ├── helpers.php                 Global functions (showCurrency, getDistance, userCart, …)
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── API/                Customer API (Auth, Cart, Order, Product, Chat, …) — 40 controllers
│   │   │   ├── API/Auth/           Login, register, forgot-password, OTP
│   │   │   ├── API/Seller/         Seller mobile-app endpoints
│   │   │   ├── API/Rider/          Rider/driver mobile-app endpoints
│   │   │   ├── Admin/              Admin controllers (Dashboard, Warehouse, StockRequest, Invoice, Order, Banner, Brand, …)
│   │   │   ├── Shop/               Vendor/seller dashboard controllers (StockRequest, Inventory, POS, Order, …)
│   │   │   ├── Seller/             Seller mobile chat controller
│   │   │   ├── Gateway/            Payment gateway router + per-gateway ProcessController
│   │   │   └── Controller.php      Base controller
│   │   ├── Kernel.php              Middleware groups, aliases, global stack
│   │   ├── Middleware/             15 middleware classes (auth, permission, demo, …)
│   │   ├── Requests/               73 Form Request classes
│   │   └── Resources/              59 Eloquent API Resource classes
│   ├── Listeners/                  Event listeners (OrderMail, SendOTP, TestMail)
│   ├── Mail/                       Mailable classes (OrderMail, SendOTP, TestMail)
│   ├── Models/
│   │   ├── Scopes/                 3 global scopes (ActiveScope, hasSubscription, PosOrderFalse)
│   │   └── 95 Eloquent models (Product, Order, Shop, Warehouse, WarehouseStock, StockRequest, StockLedger, ShopMonthlyPayout, …)
│   ├── Providers/
│   │   ├── AppServiceProvider.php
│   │   ├── AuthServiceProvider.php
│   │   ├── BroadcastServiceProvider.php
│   │   ├── EventServiceProvider.php
│   │   ├── PermissionServiceProvider.php
│   │   ├── RouteServiceProvider.php    Routes + rate limiting (60/min for API)
│   │   └── SmsServiceProvider.php
│   ├── Repositories/               55 repository classes (data-access abstraction)
│   ├── Rules/                      Custom rules (CaptchaValidate, DecimalRule, …)
│   ├── Services/                   WarehouseService, PayoutService (MLM), Chat, SmsGatewayService, NotificationServices, …
│   └── Support/Repositories/       Additional repository helpers
├── bootstrap/app.php               Laravel 10-style bootstrap (used by public/index.php)
├── config/
│   ├── acl.php                     Permission tree (admin, shop, shopMultiShop) — **defined but NOT wired into routes; see "Known Gaps"**
│   ├── permission.php              spatie/laravel-permission config
│   ├── modules.php                 nwidart/laravel-modules config
│   ├── sanctum.php, services.php, …
│   └── themeColors.php             Theme colour palette config
├── database/                       Migrations (181), seeders (DatabaseSeeder, RoleSeeder, PermissionSeeder, RootAdminShopSeeder, PaymentGatewaySeeder, WarehouseSeeder, …), factories (14)
├── Modules/                        nwidart module folders `purchase`/`report` — **asset-only stubs (css/icons only, no PHP, no modules_statuses.json); `module_exists()` returns false**
├── resources/
│   ├── js/
│   │   ├── app.js                  Vue SPA entry
│   │   ├── bootstrap.js            Axios config, Echo/Pusher stubs
│   │   ├── router/index.js         ~35 route definitions
│   │   ├── stores/                 Pinia stores (Auth, Basket, Chat, GuestAddress, Master)
│   │   ├── layouts/                4 layouts (default, auth, blank, blog)
│   │   ├── components/            ~90 Vue components
│   │   └── pages/                  ~40 Vue page views
│   ├── views/                      Blade views (admin, shop, layouts, mail, PDF invoices, …)
│   └── css/                        Additional CSS
├── routes/
│   ├── api.php                     ~109 `Route::` definitions (customer, seller, rider) — re-scaffolded; some endpoints tagged `// VERIFY`
│   ├── web.php                     651 lines / ~436 `Route::` definitions — SPA entry, admin routes, payment callbacks, shop routes; re-scaffolded (see header note in file)
│   ├── channels.php                Broadcasting channels
│   └── console.php                 Console commands
├── tests/                          Feature (PayoutTest, WarehouseTest, ProductWarehouseSyncTest, DeactivationTest, DownlineRecruitmentTest, ShopRegistrationVerificationTest, MapIntegrationTest, TruncateDataTest, …) + Unit + Browser (Dusk)
├── public/                         Vite build output + htaccess
├── .htaccess                       Root htaccess for MAMP subdirectory deployment (Authorization header pass-through)
└── assets/                         Compiled asset directories (build/, icons/, images/, branding logos)
```

---

## Database

- The application uses a **single `mysql` connection**. No additional named connections are defined in `config/database.php`.
- **Local (dev)**: `DB_DATABASE=janmitram` on MAMP (`127.0.0.1`, `root/root`).
- **Production (Hostinger)**: `DB_DATABASE=u939461333_app_janmitram` on the Hostinger MySQL host. Set via the production `.env` — it is **not** stored in the repo.
- **Migration & Data Utilities**:
  - `php artisan db:truncate-data` resets tables safely during setup or testing.
- The same local MySQL server also hosts `u939461333_janmitra`, `aitradex_db`, and `lifeskills_db`. These schemas **exist locally** but are **not referenced anywhere in the Laravel app code** — models, migrations, and queries all target the configured `DB_DATABASE` (`janmitram` locally, `u939461333_app_janmitram` in production).
- **Migrations** live in `database/migrations/` and run against whatever `DB_DATABASE` is configured. Verify the target schema with the Boost `database-schema` tool before adding/changing columns.

> **Environment caveat**: the local DB name (`janmitram`) differs from production (`u939461333_app_janmitram`). Never hardcode a schema name; always read it from `DB_DATABASE`. Do not assume cross-schema queries work — from the app's perspective there is one schema.

---

## Option A: Multi-Warehouse & Stock Management Architecture

Janmitram operates on **Option A: Strict Warehouse-Only Architecture**, which centralizes physical product inventory control within warehouses and dispatches stock to vendor shops via formal requests.

### Core Principles

1. **Centralized Master Catalog**:
   - Master Products are created centrally by Admin (`master_product_id = null`, `is_digital = false`).
   - Physical stock quantity is deposited directly into Central or Regional Warehouses (`WarehouseStock`) rather than arbitrary vendor shop inventories.
2. **Shop-Linked Logistics Hubs**:
   - Every Vendor Shop (`Shop`) is bound to a specific **Linked Warehouse** (`warehouse_id`).
   - Shops request physical stock dispatches strictly from their linked warehouse.
3. **Master-Copy Cloning Pattern (`WarehouseService::cloneMasterToShop`)**:
   - Vendor shops do not create physical inventory from scratch.
   - When Admin fulfills a shop's stock request, `WarehouseService` automatically clones or updates a **Shop Copy Product** (`master_product_id = masterProduct->id`, `shop_id = shop->id`) with full categories, subcategories, variants, colors, sizes, media, and translations.
   - Physical sellable stock `$shopProduct->quantity` is incremented upon dispatch approval.
4. **Immutable Audit Trail (`StockLedger`)**:
   - Every physical stock movement (initial product create, warehouse transfer, shop request dispatch, online customer order sale, POS sale) is logged immutably in `StockLedger`.
5. **Stock Dispatch Invoices**:
   - Admin and Shop dashboards feature printable & PDF stock dispatch invoices for completed stock requests, featuring Janmitram brand headers, borders, itemized tables, and signatures.

### Warehouse Data Models (`app/Models/`)

| Model | Purpose | Key Attributes |
| :--- | :--- | :--- |
| `Warehouse` | Physical bulk inventory hub | `id`, `name`, `code`, `is_default`, `status` |
| `WarehouseStock` | Physical stock levels inside a warehouse | `id`, `warehouse_id`, `product_id`, `color_id`, `size_id`, `quantity` — **table name is `warehouse_stock` (singular); the model sets `protected $table = 'warehouse_stock'`** |
| `StockRequest` | Stock dispatch request from shop to warehouse | `id`, `shop_id`, `warehouse_id`, `status` (`pending`, `completed`, `rejected`), `notes` |
| `StockRequestItem` | Line items in a stock request | `id`, `stock_request_id`, `product_id`, `color_id`, `size_id`, `quantity` |
| `WarehouseTransfer` | Inter-warehouse stock transfers | `id`, `from_warehouse_id`, `to_warehouse_id`, `status` |
| `StockLedger` | Immutable audit log of all physical stock movements | `id`, `from_warehouse_id`, `to_warehouse_id`, `product_id`, `quantity`, `reference_type` |

---

## Frontends

### 1. Customer SPA (Vue 3 + Vite + Tailwind)

Single-page application served from `resources/views/app.blade.php`, loaded via
`@vite('resources/css/app.css')` / `@vite('resources/js/app.js')`. **Tailwind CSS
only** (no Bootstrap) in this surface.

| Layer | Mechanism |
|-------|-----------|
| **State** | Pinia stores: `AuthStore`, `BasketStore`, `ChatStore`, `GuestAddressStore`, `MasterStore` |
| **Routing** | `vue-router` — lazy-loaded page components with layout meta |
| **API** | Axios (`window.axios`) — base URL from `<meta name="base-url">`, auth via `Authorization: Bearer {token}` header |
| **Localization** | `vue-i18n` + Laravel JSON translation files at `lang/{locale}.json` |
| **Auth persistence** | `pinia-plugin-persistedstate` (localStorage) |
| **Real-time** | Pusher (Echo commented out in `bootstrap.js` — ready to enable) |

### 2. Admin Panel (Blade + Bootstrap 5 + Tailwind)

Full CRUD management dashboard in `resources/views/admin/`. The admin layout
(`resources/views/layouts/app.blade.php`) loads **both** `assets/css/bootstrap.min.css`
and the Vite Tailwind bundle (`resources/css/app.css`).

Key features:
- **Admin Dashboard**: Recreated to feature Central Logistics metrics, total Warehouses count, Master Catalog filtering, and quick navigation.
- **Warehouse Management**: Central warehouse creation, stock deposits, stock clear actions, inter-warehouse transfers.
- **Stock Request Fulfillments**: Fulfill multi-item shop requests, check shortfall stock, generate dispatches.
- **Stock Dispatch Invoice Hub**: Admin Invoice Registry and printable PDF generation for all dispatched requests.
- **Orders & Products**: Master product catalog management, order state updates, payment toggles, rider assignment.
- **Shop Binding**: Assign Linked Warehouses to vendor shops.
- **Settings & AI**: Payment gateways, SMS, mail, Firebase, Pusher, AI content generation via `AiPromptController`.

### 3. Seller Dashboard (Blade + Bootstrap + Tailwind)

Vendor management panel in `resources/views/shop/`. Streamlined specifically for Option A Strict Warehouse Architecture:

- **Stock Management Dropdown**: Grouped **Stock Requests**, **Request New Stock**, and **Shop Inventory** into a unified sidebar menu.
- **Request New Stock**: Rich catalog view displaying vendor's Linked Warehouse live stock, search filter, quantity steppers, and multi-item batch requests.
- **Shop Inventory**: Clear breakdown of *Total Requested*, *Sold via Orders*, and *Current Available* sellable units.
- **POS & Orders**: Point-of-Sale checkout, order listing, status tracking.
- **Withdrawals & Settings**: Earnings withdrawal requests, shop profile configuration.

> **Sidebar Navigation Note**: Product Creation, Category/Variant management, and Employee management have been removed from the seller sidebar as product creation and master definitions are managed strictly by Central Admin. Sidebar collapsed state persists in `localStorage`.

### 4. Mobile Apps (Flutter/Dart)

The API surface at `routes/api.php` is documented for customer, seller, and rider
mobile apps. Auth uses Sanctum personal access tokens. Guest users get a
`X-Guest-Token` header for cart persistence across devices.

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

### Admin / Seller (Web session — Blade)
Standard Laravel session-based auth via `routes/web.php`. Middleware actually applied in the current (re-scaffolded) routes: `auth` + `role:root` (admin), `authShop` (seller). `checkPermission` is **defined but not applied to any route** — see "Known Gaps". Default root email is `root@janmitram.com`.

---

## Payment Gateway Architecture (Pluggable Strategy)

Polymorphic per-gateway processor pattern under `app/Http/Controllers/Gateway/`.
Eleven per-gateway `ProcessController` classes are present:

```
Gateway/
├── PaymentGatewayController.php    Router: resolves gateway by name/alias → delegates to ProcessController::process()
├── AamarPay/ProcessController.php
├── Bkash/ProcessController.php
├── CashFree/ProcessController.php
├── JazzCash/ProcessController.php
├── PayPal/ProcessController.php
├── PayStack/ProcessController.php
├── PayTabs/ProcessController.php
├── PayU/ProcessController.php
├── QiCard/ProcessController.php
├── Razorpay/ProcessController.php
└── Stripe/ProcessController.php
```

`PaySafeCard` (`paysafecard/paysafecard-rest_api-php`) is in `composer.json` but
has **no `Gateway/PaySafeCard/` controller, no seeder entry, and no `PaymentMethod`
enum case** — it is an unused dependency. All gateways verify via redirect/return-URL
(`payment.success` / `payment.cancel` / `order.payment.success|cancel`); there are
**no webhook controllers**. **Known gap:** `paypal.payment.success`,
`bkash.payment.execute`, and `paytabs.payment.callback` are referenced by their
ProcessControllers but not registered as routes — see "Known Gaps".

---

## Roles & Permissions (spatie/laravel-permission)

Defined in `config/acl.php` — a structured permission tree exists for `admin`,
`adminMultiShop`, `shop`, `shopMultiShop` (resource → allowed actions) plus
`customerReadableNames`. Roles are seeded by `RoleSeeder` (`app/Enums/Roles.php`:
`root, admin, shop, customer, visitor, driver, supplier`).

| Role | Scope | Key permissions |
|------|-------|-----------------|
| **root** | Super-admin | Everything (default root user `root@janmitram.com`) |
| **admin** | Admin panel | ~45 resource groups (order, product, warehouse, stock-request, banner, rider, …) — **per `acl.php`, but see caveat below** |
| **adminMultiShop** | Multi-shop admin | shop management, subscription plans, withdraw approvals — **per `acl.php`, but see caveat below** |
| **shop** | Vendor | stock requests, inventory, POS, orders, withdraws |
| **shopMultiShop** | Multi-shop vendor | subscription, withdraw, dashboard |
| **customer** | API consumer | Own profile, addresses, orders, cart |
| **driver/rider** | Delivery | Assigned orders, location updates |
| **visitor** | Unauthenticated | Public read endpoints |

> **⚠️ Caveat — granular permissions are currently DORMANT.** `CheckPermission`
> middleware (`app/Http/Middleware/CheckPermission.php`) implements the bridge
> (root bypasses; resolves role+user permissions, subtracts the
> `UserNonPermission` blocklist, 403s if the route name isn't permitted), but it
> is **not applied to any route** in the current `routes/web.php`. Admin routes
> use `['auth','role:root']` only; shop routes use `['authShop']` only. As a
> result the admin panel is reachable **only by the `root` role**, and the
> `admin`/`adminMultiShop` rows above describe what `acl.php` *intends*, not what
> the current routing *enforces*. The Spatie permission UIs
> (`RolePermissionController`, `EmployeeManageController`) write to Spatie tables
> but do not gate route access. Re-wire `checkPermission` (or officially retire
> `acl.php`) before relying on per-role admin permissions.

---

## API Surface (`routes/api.php`)

~109 `Route::` definitions organized by actor (no `/v1` versioning prefix; Sanctum `auth:sanctum` for protected; rate-limited 60/min). Some endpoints are tagged `// VERIFY` in the re-scaffolded file:

### Public (no auth required)
- `GET /master` — app settings, theme, currencies, languages
- `GET /home` — homepage data (featured products, banners, categories)
- `GET /categories`, `/sub-categories`, `/products`, `/product-details`
- `GET /shops`, `/shop`, `/shops/{shop}`, `/shop-categories`, `/top-shops`, `/popular-products`
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

---

## Structural Guidelines

### 1. Controllers stay thin

Controllers should orchestrate, not compute. Push logic down:
- **Warehouse & Inventory operations** → `App\Services\WarehouseService`.
  - Helper methods: `findStock()` (smart color/size fallback), `resolveWarehouseShopId()`, `createWarehouse()`.
  - `fulfillStockRequest()` uses `findStock()` and caps the shop increment to the actual dispatched quantity (no phantom stock on shortfall).
  - **⚠️ Online-sale caveat:** in `OrderRepository::storeByRequestFromCart`, the `WarehouseService::deductStock(..., 'order_sale', ...)` call is wrapped in `catch (\Throwable $th) {}` — an `InsufficientStockException` is **silently swallowed** and the order still completes, so an online sale can succeed even when warehouse stock is short. This contradicts the strict-ledger philosophy above; decide whether to enforce strict stock on online sales or keep the current permissive behaviour intentionally.
- **MLM / network payouts** → `App\Services\PayoutService` (referral tree, monthly payouts, 90-day deactivation) + the `CalculateMonthlyPayouts` / `DeactivateInactiveMembers` commands.
- **Complex queries** → Eloquent scopes (`app/Models/Scopes/`) or `Repository` classes.
- **Business logic** → `app/Services/` (e.g. `SmsGatewayService`, `NotificationServices`).
- **Output shaping** → Eloquent API Resources in `app/Http/Resources/`.

### 2. Validation via Form Requests

All validation lives in `app/Http/Requests/`. Return typed validated data with `$request->validated()`.

### 3. Strict types & typing

- Every PHP file begins with `declare(strict_types=1);`.
- Explicit return types and argument type-hints on all methods.
- Constructor property promotion for dependencies.

### 4. Database & Eloquent

- **CamelCase** for model properties, **snake_case** for DB columns.
- Always define explicit relationship return types (`HasMany`, `BelongsTo`, `BelongsToMany`).
- **Prevent N+1**: eager load relationships with `with()` in queries.
- Use Eloquent scopes for reusable query constraints.
- Prefer a `casts()` method on models (Laravel 11 style) over the `$casts` property.

---

## Common Workflows & Artisan Commands

### Frontend Build

```bash
npm run dev        # hot reload on changes
npm run build      # production bundle
composer run dev   # artisan + vite together
```

### Database & Seeders

```bash
php artisan migrate                                     # run migrations
php artisan db:truncate-data                            # truncate tables for reseeding
php artisan db:seed --class=DatabaseSeeder              # seed default root admin & settings
```

### Automated Testing

```bash
php artisan test --compact                              # full PHPUnit suite (13 feature files / 56 methods)
php artisan test --compact --filter=PayoutTest          # MLM payout engine (15 tests)
php artisan test --compact --filter=WarehouseTest       # warehouse & stock fulfillment tests
php artisan test --compact --filter=ProductWarehouseSyncTest # stock sync tests
php artisan test --compact --filter=DeactivationTest    # 90-day MLM deactivation
php artisan test --compact --filter=DownlineRecruitmentTest # referral/downline linkage

php artisan dusk                                        # full Dusk browser suite (21 classes / 89 methods)
```

### Code Style & Administration

```bash
vendor/bin/pint --dirty --format agent                  # auto-fix PHP code style
php artisan route:list                                  # inspect all routes
php artisan config:show app.name                        # read config value
php artisan tinker --execute 'User::count();'
```

---

## Security Notes & Branding

- **Janmitram Branding**: Official Janmitram logos (`logo.png`, `logoWhite.png`, `favicon.png`), clean card borders, and platform footer integrated into all invoice PDFs and web interfaces.
- **Spatie permissions**: `config/acl.php` + `CheckPermission` middleware are **defined but not wired into any route** — admin/shop actions are currently gated only by `role:root` / `authShop`. See the Roles & Permissions caveat above.
- **Demo mode**: `DemoModeMiddleware` blocks mutating actions (POST/PUT/DELETE) in demo environments.
- **MAMP FastCGI Pass-through**: Root `.htaccess` configured to pass `Authorization` header through to PHP for Sanctum bearer tokens.
- **CSRF & Sanitization**: `VerifyCsrfToken` protects web routes; `mews/purifier` sanitizes HTML content.

---

## Known Gaps & Docs-vs-Reality

A full deep analysis (2026-08-02) found several places where the running code
differs from earlier documentation. The most important, all verified against
source:

1. **`purchase`/`report` modules are stubs.** `Modules/{purchase,report}/`
   contain only `css/`/`icon.*` — **zero PHP** (no controllers/views/routes/
   providers), no `modules_statuses.json`. `module_exists()` (in
   `app/helpers.php`) returns **false** for both, so every
   `module_exists('Purchase')` path (`Product::productStockOuts()`/
   `buyingPrice()`, the stock-out-on-cancel + barcode-scanner code in
   `OrderRepository`/`admin/order/show`) is **dormant**. Treat as
   server-withheld/aspirational.

2. **Granular permissions are dormant.** `config/acl.php` defines a full tree
   and `CheckPermission` implements the bridge, but **no route uses
   `checkPermission`**. Admin = `['auth','role:root']` only; shop = `authShop`
   only. The admin panel is therefore **`root`-role-only**; the
   `admin`/`adminMultiShop` roles cannot reach admin routes under current
   routing. (See the Roles & Permissions caveat.)

3. **Route files are reconstructions.** Both `routes/web.php` and
   `routes/api.php` open with a "RECONSTRUCTION NOTE" — the originals were
   withheld by the author's license server and a `DestroyTrait::remove()`
   kill-switch deleted local copies. Several `api.php` endpoints are tagged
   `// VERIFY`.

4. **`warehouse_stock` is SINGULAR.** Live DB + migration + model
   (`protected $table = 'warehouse_stock'`) all use the singular table name.

5. **Missing gateway callback routes.** `Gateway/PayPal|Bkash|PayTabs/
   ProcessController.php` reference `paypal.payment.success`,
   `bkash.payment.execute`, and `paytabs.payment.callback`, but **none are
   registered** in `routes/web.php`/`api.php`. PayPal capture / Bkash execute /
   PayTabs callback will throw "Route not defined" at the return-URL step.

6. **PaySafeCard is an unused dependency** — no `Gateway/PaySafeCard/`
   controller, no seeder entry, no `PaymentMethod` enum case.

7. **No webhooks anywhere** — all payment verification is redirect/return-URL
   based (no signed-webhook verification).

8. **No enforced order-status transition graph** —
   `Admin\OrderController::statusChange` accepts any status → any status
   server-side; only the UI guards `Delivered`/`Cancelled`.

9. **`RiderController::riderLocation` null-deref** (line ~142):
   `if (! $driver && ! $driver->driverLocation)` short-circuits incorrectly —
   if `$driver` is null it dereferences null. (Line 127 has a separate *correct*
   guard, `! $driver->driverLocation()->exists()`.) Reachable via the AJAX
   location endpoint with a bad id.

10. **Online-sale stock-shortfall is silently swallowed** —
    `OrderRepository::storeByRequestFromCart` wraps
    `WarehouseService::deductStock(..., 'order_sale', ...)` in
    `catch (\Throwable $th) {}`, so an `InsufficientStockException` is swallowed
    and the order completes even when warehouse stock is short. (See Structural
    Guidelines §1.)

11. **MLM payout engine is real but underdocumented** — `PayoutService`,
    `ShopMonthlyPayout`, `shops.parent_shop_id`, referral codes, 90-day
    deactivation, tiered group-sales bonuses, and the
    `CalculateMonthlyPayouts`/`DeactivateInactiveMembers` commands. Added
    2026-08-01. Documented in the Stack table and Structural Guidelines above.

### Test coverage summary

**Strong:** MLM/payout (`PayoutTest` 15 tests, `PayoutNetworkTest`,
`PayoutSlipTest`, `ShopPayoutTest`), deactivation (`DeactivationTest`),
recruitment (`DownlineRecruitmentTest`), shop registration wizard
(`ShopRegistrationVerificationTest`), map integration (`MapIntegrationTest`),
warehouse (`WarehouseTest`, `ProductWarehouseSyncTest`), data truncation
(`TruncateDataTest`). Dusk: admin CRUD smoke across all areas + shop-owner +
customer SPA flows.

**Gaps:** order status-change workflow (transitions, cancellation restock),
rider `assignOrder` logic, payment gateway flows (and the missing callback
routes above), `CheckSubscription` middleware, the `riderLocation` null-deref.

---

_Last updated: 2026-08-02. Verified against the codebase via a full deep analysis; see "Known Gaps & Docs-vs-Reality" for the drift from earlier docs._
