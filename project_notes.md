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

## Card System (10% membership discount)

A membership **card** grants a flat discount on every purchase — online checkout,
buy-now, and POS. Added 2026-08-11.

- **Schema** — `cards` (`card_number` unique, auto-generated 8-digit; `customer_id`
  nullable FK; `is_active`); `orders.card_id` + `orders.card_discount`;
  `pos_carts.card_id`. Global terms live on `generate_settings`:
  `card_discount_percentage` (default **10**) and `card_min_order_amount` (default **500**).
- **Terms** — `card_discount_percentage`% of the subtotal when the order meets the
  minimum. The card discount is **instead-of** any coupon (a card wins, coupons are
  skipped). Online the card must belong to the logged-in customer; at POS any active
  card number applies (the plastic is the credential).
- **One active card per customer** — `CardRepository::createForCustomer` deactivates a
  customer's other cards when a new one is issued.
- **Flow** — `CardRepository` (`resolveForCustomer` / `resolveActive` / `discountFor`);
  applied per-shop in `OrderRepository::getCartWiseAmounts` and via
  `PosCartRepository::applyCard`. Orders persist `card_id` + `card_discount`; checkout
  preview returns `card_discount`/`card_error`.
- **Admin** — "Cards" section (`/admin/cards`): create (auto number), assign to a
  customer, activate/deactivate, and a per-card detail + usage view
  (`/admin/cards/{card}`) listing the orders that used it.
- **Replaces the collected-voucher scheme** — the broken `coupon_collects` mechanism
  (collect button, `getCollectedCoupons` auto-apply) was removed; **typed coupon codes
  still work**.

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
  - **Sales draw from shop inventory only** (2026-08-10): online checkout and POS no longer call `WarehouseService::deductStock()` at sale time — warehouse stock is consumed when the shop's stock request is dispatched (`fulfillStockRequest`), and a sale decrements the Shop Copy Product's `products.quantity` directly inside a `DB::transaction`. No `order_sale`/`pos_sale` `StockLedger` rows are written. (This replaced the old sale-time `deductStock` call, which double-decremented shop inventory and drained an already-dispatched warehouse.)
  - **Order creation is one shared processor** (2026-08-10): `OrderRepository::createOrderForShop()` + `groupLinesByShop()` build every order — cart checkout, POS, and **reorder** — wrapped in `DB::transaction`. A reorder re-prices at current prices (variants, active flash sale, size/color), re-allocates like a fresh checkout, persists VAT, and links the payment. Flash-sale pricing everywhere (orders and cart/product display) uses the `isActive()` scope, so an ended flash sale never leaks its old price.
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

10. **MLM payout engine is real but underdocumented** — `PayoutService`,
    `ShopMonthlyPayout`, `shops.parent_shop_id`, referral codes, 90-day
    deactivation, tiered group-sales bonuses, and the
    `CalculateMonthlyPayouts`/`DeactivateInactiveMembers` commands. Added
    2026-08-01. Documented in the Stack table and Structural Guidelines above.

## Order Management, Tax & Invoice Architecture (Updated 2026-08-17)

### 1. Dynamic Master Product GST & Tax Inheritance
* **Central Catalog Configuration**: The Admin configures VAT/Tax slabs (GST 5%, 12%, 18%, 28%) centrally on Master Products or sets a platform-wide default rate under `Admin -> VAT & Tax` (`/admin/vat-tax`).
* **Real-time Shop Inheritance**: When customer orders or POS transactions are placed, `OrderRepository` and `POSController` dynamically evaluate `$product->vatTaxes()` and automatically fall back to `$product->masterProduct?->vatTaxes()` if the shop copy has no direct tax override.
* **Instant Propagation**: If the Admin changes a Master Product's GST rate (or changes the Platform Default Tax rate), all shop inventories across the platform immediately reflect and charge the updated GST rate on all future orders without manual shop inventory re-configuration.

### 2. Admin Order Management Enhancements (`/admin/order`)
* **GST / Tax Amount Column**: Displays the exact GST / tax amount charged in rupees with sortable header. If multiple tax lines apply (`CGST`, `SGST`, `IGST`), interactive sub-badges display each tax name and rupee amount.
* **Discounts Column**: Displays total discounts in rupees with sortable header, breaking down Coupon deductions with coupon code, Membership Card discounts with card number, and Special promotional discounts.

### 3. Order Details & PDF Invoice Generation (`resources/views/PDF/invoice.blade.php`)
* **Comprehensive Financial Breakdown**: Renders Sub Total, Coupon Discount (with promo code), Membership Card Discount (with card number), Special Discounts, Delivery Charge, Itemized GST percentages (with dynamic labels like `GST (5%)`), and Grand Total Payable.
* **Bold Product Units**: Renders product measurement units (e.g., `200GM`, `1 KG`, `1 Ltr`, `10kg`) in bold badges across frontend Vue components (listings, cards, details, cart drawer, checkout) and printable PDF invoice line items.

### 4. MLM Downline Network Capacity Architecture (Updated 2026-08-17)
* **Direct Downline Capacity Rule**:
  * **Main Janmitram Shop (ID: 1)**: Primary root node with **unlimited direct downlines**.
  * **Standard Partner Shops (ID != 1)**: Restricted to a maximum of **10 direct downline shops** (1 self + 10 direct downlines = 11 direct frontline members).
* **Multi-Level Depth Preservation**: There is no depth restriction on subsequent generations. Once a shop owner fills their 10 direct slots, new partners register under their existing downline partners, growing deep multi-level branches that roll up for Phase 2 Group Sales bonuses.
* **Multi-Layer Enforcement**:
  * `Shop::canAcceptDirectDownline()` and `Shop::MAX_DIRECT_DOWNLINES = 10`.
  * Form Request & live AJAX verification reject registrations using a saturated sponsor code.
  * Shop Partner Creation Form disables new partner creation and directs leaders to promote their team members' referral codes.
  * Admin Shop Create/Edit screens visually display direct capacity tags (`[X/10 downlines]` or `[Full: 10/10 capacity]`).

### 5. First Stock Transfer Minimum Threshold (₹3,000) (Updated 2026-08-18)
* **Minimum Initial Capital Rule**: To guarantee franchise readiness, the first physical stock transfer/dispatch to any newly registered or newly approved franchise shop must have a minimum aggregate value of **₹3,000.00**.
* **Engine & Logic**:
  * Defined `Shop::MIN_FIRST_STOCK_TRANSFER_AMOUNT = 3000.0`.
  * Helper methods `Shop::hasReceivedStock(): bool` and `Shop::isFirstStockTransfer(): bool` check whether the shop has ever received inventory before.
  * Enforced in both `StockAssignmentRequest` (Admin warehouse assignment) and `StockRequestRequest` (Vendor shop stock request).
  * Subsequent reorders/restocks have no minimum threshold restriction.
  * Admin & Shop creation UIs feature real-time subtotal calculators, shortfall alerts, and first-transfer badges.
  * Covered by `FirstStockTransferThresholdTest` (6 assertions).

### 6. Customer-Controlled Fulfillment Mode (Updated 2026-08-18)
* **Customer Choice at Checkout**: Customers can toggle between **"Auto-deliver from nearest shop"** (intelligent geolocation dispatching) and **"Force delivery strictly from the shop selected in cart"** (strict shop locking).
* **Backend Order Routing (`OrderRepository.php`)**:
  * **Auto-Nearest Mode (`fulfill_from_nearest_shop = true`, default)**: Reallocates order lines using Haversine distance within a 50 km radius to the closest shop holding stock.
  * **Strict Mode (`fulfill_from_nearest_shop = false`)**: Locks lines directly to `$cart->shop_id`. If cart items belong to multiple shops, it splits sub-orders matching the selected shops and fails gracefully if local stock is deficient.
  * Covered by `OrderShopFulfillmentModeTest` (4 assertions).

### 7. Customer Review System & Admin Approval Workflow (Updated 2026-08-19)
* **Admin Approval Workflow**: All new customer reviews default to `is_active = 0` (Pending Approval) and are guarded by `ActiveScope`. Reviews do not appear publicly on product/shop pages until approved by an Admin.
* **Dual Attribution**: Reviews link simultaneously to `product_id`, `shop_id`, `customer_id`, and `order_id` (verified purchase link).
* **Multi-Photo Attachments**: Customers can upload up to 5 unboxing/product photos (`photos` JSON column) with full-screen lightbox modal previews.
* **Official Store / Admin Replies**: Admins and shop managers can submit or edit an official response (`reply`, `replied_at`) displayed below customer reviews.
* **Master Catalog Review Aggregation**: Public queries on master products aggregate approved reviews across all cloned franchise shop copies.
* **Dynamic Shop Star Ratings**: A franchise shop's rating is automatically calculated as the live average of all approved product reviews fulfilled by that shop.
* **Admin Moderation Dashboard (`/admin/review`)**: Features KPI summary cards, status tabs (All, Pending Approval, Approved & Live), shop/star/text filters, quick action buttons (Approve, Reject, Reply, Delete), and a live pending badge in the admin sidebar.
* **Shop Profile Reviews Tab**: Dedicated reviews inspection inside each shop profile at `/admin/shop/{id}/reviews`.
* Covered by `ReviewApprovalAndModerationTest` (6 assertions).

### 8. Monthly Shop Payout Execution Frequency
* **Monthly Cycle**: Admin calculates monthly payouts once per month (on or after the 1st of the month for the prior calendar month) using `php artisan payout:monthly` or the Admin Payout dashboard (`/admin/payout`).
* **Manual Withdrawals**: In addition to monthly payout calculations, franchise shop owners can submit withdrawal requests for their approved earnings at any time via `/shop/withdraw`.

### 9. Google Maps JavaScript SDK & Google Places API (New) Integration (Updated 2026-08-19)
* **Google Cloud Maps Platform Transition**:
  * Fully transitioned mapping and geocoding stack to **Google Maps JavaScript SDK & Google Places API (New)**.
  * Configured `GOOGLE_MAPS_API_KEY` in `config/services.php` and `.env`.
  * `GoogleMapsService` provides Places Autocomplete (`/places/v1/autocomplete`), Details lookup, Doorstep Reverse Geocoding (`/geocode/json`), and turn-by-turn Directions.
  * Built-in in-memory query caching, request cancellation, and multi-tier coordinate resolution guarantee zero map disruption.
* **Frontend Vue 3 SPA Components**:
  * `MapDisplay.vue`: Luxury vector map with interactive drag pin, satellite hybrid toggle, rich doorstep address banner, HTML5 GPS auto-detection, and modal ResizeObserver.
  * `RealTimeMapDisplay.vue`: Live order tracking with customer home pin, driver pin, dynamic route polyline, and Pusher WebSocket updates.
* **Blade Dashboards**:
  * Upgraded Admin and Shop order location modals to the Google Maps SDK (`admin/order/{id}`, `shop/order/{id}`).
* Covered by `GoogleMapsIntegrationTest` (5 tests, 24 assertions).

### 10. Automated Zero-Permission IP Geolocation & City Vicinity Resolution (Updated 2026-08-20)
* **1st Priority: Automated IP Geolocation (Zero Permission Popups)**:
  * Automatically detects customer IP (`CF-Connecting-IP`, `X-Real-IP`, `X-Forwarded-For`, `REMOTE_ADDR`) on page load via `LocationController@resolve` (`/api/location/resolve`).
  * Resolves visitor's City, State, and Postal PIN without prompt-blocking browser permission dialogs.
  * Matched shops in the customer's city/vicinity are automatically bound to the active session.
* **Non-Indian / Overseas Visitor Handling**:
  * When international visitors access the platform (`countryCode !== 'IN'`), the engine gracefully defaults their browsing session to Janmitram's Central Hub (`Jaipur, Rajasthan - 302013`).
* **Frontend State & Components**:
  * Managed by Pinia `LocationStore.js` with `localStorage` caching (`janmitram_user_location`).
  * `LocationPickerModal.vue`: HeadlessUI modal for instant city switching, 6-digit postal PIN search, and branch store selection.
  * `NavbarMiddle.vue`: Displays `📍 Delivering to: [City (Pincode)] ▾` with quick modal trigger.
* Covered by `LocationResolutionTest` (4 tests, 15 assertions).

### 11. Homepage Multi-Branch Shop Round-Robin Product Distribution & Main Shop Exclusion (Updated 2026-08-20)
* **Fair Multi-Shop Round-Robin Distribution**:
  * In `HomeController@index` (`/api/home`), products displayed in the **Popular Products** section are selected in a round-robin sequence across all active local branch shops in the user's city (Shop A ➔ item 1, Shop B ➔ item 2, Shop C ➔ item 3, etc.).
  * **Zero Product Redundancy**: Strict automated deduplication guarantees that each product name appears only once across the entire section.
* **Strict Exclusion of Main Janmitram Shop (Shop ID: 1 / Central Warehouse)**:
  * Products belonging to `Shop ID: 1` (`rootShop`) are strictly excluded from both the homepage **Popular Products** and **Just For You** sections, focusing customer attention entirely on local franchise branches.
* **Out-of-Area Fallback**:
  * Visitors from cities without a dedicated branch shop receive a fair, randomized product assortment across active regional branch shops nationwide (excluding Shop 1).
* **Catalog Synchronization**:
  * `Products.vue` and `CategoryProduct.vue` dynamically pass `nearest_shop_id` or city to prioritize local branch inventory.

### 12. Razorpay Payment Gateway Streamlining & Window Lifecycle Sync (Updated 2026-08-19)
* **Single Gateway Auto-Selection**: When Razorpay is the sole active online gateway configured in the admin dashboard, the checkout flow automatically pre-selects Razorpay and hides redundant gateway selection cards.
* **Popup Lifecycle & Auto-Close**: Integrated `window.postMessage` and `localStorage` listener synchronization in `Razorpay/ProcessController.php` so payment popup windows automatically close and return customers seamlessly to the order confirmation page.
* **Route & Data Hardening**: Registered `order.payment` named route, sanitized customer phone payloads, and secured callback token capture.

### 13. Shop Business Models: Direct / Zero-Fee Support (Updated 2026-08-19)
* **Flexible Commission Schemes**: Under `Admin -> Business Settings -> Shop Settings`, admins can configure either the traditional **Commission-Based Model** or the new **Direct / None Zero-Fee Model**.
* **Wallet Debit Safeguards**: For zero-fee shops, commission deductions are automatically bypassed on completed orders while maintaining comprehensive transaction logs.
* Covered by `ShopBusinessSettingTest` (comprehensive assertions).

### 14. Enterprise Corporate PDF Architecture (Updated 2026-08-19)
* **Unified Printable Margin Boundary**: Standardized a consistent **12mm page margin boundary** in mPDF across all system PDFs (Tax Invoices, POS Receipts, Stock Transfer Invoices, and Payment Slips).
* **Itemized GST & Discounts Breakdown**: PDFs feature clean tables with itemized GST percentages (`GST (5%)`, `GST (12%)`, etc.), product measurement units in bold, coupon/card discount breakdowns, and dynamic QR verification codes.

### 15. Frontend UI/UX Modernization & Brand Unification (Updated 2026-08-19)
* **Product Card In-Card Steppers**: Integrated quantity steppers directly inside `ProductCard.vue` allowing customers to add/adjust items with zero page jumps.
* **Debounced Live Search Popup**: Rich search dropdown in `NavbarMiddle.vue` with category badges, instant product previews, and keyboard navigation.
* **Mobile Sticky Action Bar**: Sticky floating add-to-cart and buy-now bar on mobile screens in `ProductDetails.vue`.
* **System-wide Brand Favicon**: Replaced generic icons with the official Janmitram brand logo across all browser tabs, customer SPA, Blade layouts, and payment callback screens.

---

### Test coverage summary

**Strong Test Coverage (157 Test Classes / 574 Assertions):**
* **Location & Catalog Resolution**: `LocationResolutionTest` (4 tests, 15 assertions)
* **Google Maps Integration**: `GoogleMapsIntegrationTest` (5 tests, 24 assertions)
* **MLM & Network Payouts**: `PayoutTest` (15 tests), `PayoutNetworkTest`, `PayoutSlipTest`, `ShopPayoutTest`, `ShopWithdrawalPayoutIntegrationTest`
* **Shop Business Models**: `ShopBusinessSettingTest` (comprehensive commission & zero-fee assertions)
* **Downline Capacity & Recruitment**: `DownlineRecruitmentTest`, `AdminShopCreateTest`
* **Shop KYC & Onboarding**: `AdminShopKycTest`, `ShopRegistrationVerificationTest`
* **Stock & Warehouse Management**: `WarehouseTest`, `ProductWarehouseSyncTest`, `ShopInventoryAssignmentTest`, `FirstStockTransferThresholdTest` (6 tests)
* **Order Placement & Shop Allocation**: `ShopAllocationTest` (20 tests), `OrderShopFulfillmentModeTest` (4 tests)
* **Customer Reviews & Moderation**: `ReviewApprovalAndModerationTest` (6 tests)
* **Cards & Tax/Discounts**: `CardTest`, `ProductVatTaxTest`
* **Catalog & Maintenance**: `ProductCatalogDeduplicationTest`, `ProductImportExportTest`, `TruncateDataTest`
* **Dusk Browser Suites**: Full Admin CRUD smoke suites + Shop vendor portal + Customer Vue 3 SPA checkout/browse flows.

---

_Last updated: 2026-08-20. Fully verified against the codebase and live production environment._

