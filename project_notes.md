# Project Notes — Janmitram App

Laravel 11 multi-vendor ecommerce platform with a Vue 3 customer SPA, two
Blade-based dashboards (admin & seller), dedicated mobile apps
(Flutter/Dart), and an integrated **Option A: Strict Warehouse-Only Stock Management System**.
Comprehensive payment-gateway support, real-time chat, SMS/Firebase notifications, and multi-currency i18n.

> **Style note**: All new code should follow the guidelines in § **Structural
> Guidelines**: strict types, thin controllers, Form Requests, typed
> relationships, eager loading.

_Last verified against the codebase: 2026-07-29._

---

## Stack

| Layer | Technology |
|-------|------------|
| **PHP** | 8.2 |
| **Framework** | Laravel 11 (`laravel/framework` ^11.31 — classic 10-style structure retained: `app/Http/Kernel.php`, `app/Exceptions/Handler.php`, `app/Console/Kernel.php`; `bootstrap/app.php` exists and is used by `public/index.php`) |
| **Auth** | Laravel Sanctum ^4 (token-based API) + web session (admin/seller) |
| **RBAC** | spatie/laravel-permission ^6 |
| **Modules** | nwidart/laravel-modules ^12 |
| **Warehouse Architecture** | Option A Strict Warehouse Architecture (central master catalog, warehouse stocks, shop-linked logistics hubs, immutable stock ledger auditing) |
| **Frontend (customer)** | Vue 3 + Vite + Tailwind CSS 3 + Pinia + vue-router + vue-i18n |
| **Admin & seller UI** | Blade templates styled with **Bootstrap 5 + Tailwind CSS** (both loaded; persistent collapsed sidebar) |
| **Mobile apps** | Flutter/Dart (external — API surface in `routes/api.php`) |
| **Payments** | Razorpay, Stripe, PayPal, PayStack, PaySafeCard (SDK packages) + AamarPay, Bkash, CashFree, JazzCash, PayTabs, PayU, QiCard (HTTP/gateway-driven, no SDK) |
| **Real-time** | Pusher (Echo configured in JS, server-side `pusher/pusher-php-server`) |
| **SMS** | Twilio, Vonage (Nexmo), MessageBird, Telesign |
| **Firebase** | Cloud Messaging (push notifications) |
| **AI** | OpenAI API (`openai-php/laravel`), Google API (`google/apiclient`) |
| **Exports & Invoices** | maatwebsite/excel, mpdf (invoice PDF generation), milon/barcode, endroid/qr-code |
| **DB** | MySQL (MAMP local: `ready_ecommerce`) via a single `mysql` connection |
| **Testing** | PHPUnit ^11 (14 automated feature/unit tests) + Laravel Dusk ^8 (21 browser tests) |
| **Code style** | Laravel Pint ^1 |

---

## Architecture Overview

```
janmitram-app/
├── WORKFLOW_PROJECT.md             End-to-End Architecture & Option A Warehouse Workflow Docs
├── app/
│   ├── Console/Commands/           Artisan commands (LetsFix, TruncateData, orderProductUpdate)
│   ├── Enums/                      10 backed enums (OrderStatus, PaymentMethod, Roles, …)
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
│   │   ├── Middleware/             14 middleware classes (auth, permission, demo, …)
│   │   ├── Requests/               ~70 Form Request classes
│   │   └── Resources/              ~59 Eloquent API Resource classes
│   ├── Listeners/                  Event listeners (OrderMail, SendOTP, TestMail)
│   ├── Mail/                       Mailable classes (OrderMail, SendOTP, TestMail)
│   ├── Models/
│   │   ├── Scopes/                 3 global scopes (ActiveScope, hasSubscription, PosOrderFalse)
│   │   └── 94 Eloquent models (Product, Order, Shop, Warehouse, WarehouseStock, StockRequest, StockLedger, …)
│   ├── Providers/
│   │   ├── AppServiceProvider.php
│   │   ├── AuthServiceProvider.php
│   │   ├── BroadcastServiceProvider.php
│   │   ├── EventServiceProvider.php
│   │   ├── PermissionServiceProvider.php
│   │   ├── RouteServiceProvider.php    Routes + rate limiting (60/min for API)
│   │   └── SmsServiceProvider.php
│   ├── Repositories/               ~53 repository classes (data-access abstraction)
│   ├── Rules/                      4 custom rules (CaptchaValidate, EmailRule, EnumValue, …)
│   ├── Services/                   WarehouseService, Chat, SmsGatewayService, NotificationServices, …
│   └── Support/Repositories/       Additional repository helpers
├── bootstrap/app.php               Laravel 10-style bootstrap (used by public/index.php)
├── config/
│   ├── acl.php                     Complete permission tree (admin, shop, shopMultiShop)
│   ├── permission.php              spatie/laravel-permission config
│   ├── modules.php                 nwidart/laravel-modules config
│   ├── sanctum.php, services.php, …
│   └── themeColors.php             Theme colour palette config
├── database/                       Migrations, seeders (DatabaseSeeder, RootAdminShopSeeder), factories
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
│   ├── views/                      Blade views (admin, shop, layouts, mail, PDF invoices, …)
│   └── css/                        Additional CSS
├── routes/
│   ├── api.php                     106 API endpoints (customer, seller, rider)
│   ├── web.php                     396 routes — SPA entry, admin routes, payment callbacks, shop routes
│   ├── channels.php                Broadcasting channels
│   └── console.php                 Console commands
├── tests/                          Feature (WarehouseTest, ProductWarehouseSyncTest, TruncateDataTest) + Unit + Browser (Dusk)
├── public/                         Vite build output + htaccess
├── .htaccess                       Root htaccess for MAMP subdirectory deployment (Authorization header pass-through)
└── assets/                         Compiled asset directories (build/, icons/, images/, branding logos)
```

---

## Database

- The application uses a **single `mysql` connection**. No additional named connections are defined in `config/database.php`.
- **Local (dev)**: `DB_DATABASE=ready_ecommerce` on MAMP (`127.0.0.1`, `root/root`).
- **Production (Hostinger)**: `DB_DATABASE=u939461333_app_janmitram` on the Hostinger MySQL host. Set via the production `.env` — it is **not** stored in the repo.
- **Migration & Data Utilities**:
  - `php artisan db:truncate-data` resets tables safely during setup or testing.
- The same local MySQL server also hosts `u939461333_janmitra`, `aitradex_db`, and `lifeskills_db`. These schemas **exist locally** but are **not referenced anywhere in the Laravel app code** — models, migrations, and queries all target the configured `DB_DATABASE` (`ready_ecommerce` locally, `u939461333_app_janmitram` in production).
- **Migrations** live in `database/migrations/` and run against whatever `DB_DATABASE` is configured. Verify the target schema with the Boost `database-schema` tool before adding/changing columns.

> **Environment caveat**: the local DB name (`ready_ecommerce`) differs from production (`u939461333_app_janmitram`). Never hardcode a schema name; always read it from `DB_DATABASE`. Do not assume cross-schema queries work — from the app's perspective there is one schema.

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
| `WarehouseStock` | Physical stock levels inside a warehouse | `id`, `warehouse_id`, `product_id`, `color_id`, `size_id`, `quantity` |
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
Standard Laravel session-based auth via `routes/web.php`. Middleware: `auth`,
`checkPermission` (admin), `authShop` + `checkPermission` (seller). Default root email is `root@janmitram.com`.

---

## Payment Gateway Architecture (Pluggable Strategy)

Polymorphic per-gateway processor pattern under `app/Http/Controllers/Gateway/`.
Twelve per-gateway `ProcessController` classes are present:

```
Gateway/
├── PaymentGatewayController.php    Router: resolves gateway → delegates to ProcessController
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
├── Stripe/ProcessController.php
└── (PaySafeCard handled via config-driven gateway model)
```

---

## Roles & Permissions (spatie/laravel-permission)

Defined in `config/acl.php` — structured permission tree:

| Role | Scope | Key permissions |
|------|-------|-----------------|
| **root** | Super-admin | Everything (default root user `root@janmitram.com`) |
| **admin** | Admin panel | ~45 resource groups (order, product, warehouse, stock-request, banner, rider, …) |
| **adminMultiShop** | Multi-shop admin | shop management, subscription plans, withdraw approvals |
| **shop** | Vendor | stock requests, inventory, POS, orders, withdraws |
| **shopMultiShop** | Multi-shop vendor | subscription, withdraw, dashboard |
| **customer** | API consumer | Own profile, addresses, orders, cart |
| **driver/rider** | Delivery | Assigned orders, location updates |
| **visitor** | Unauthenticated | Public read endpoints |

---

## API Surface (`routes/api.php`)

106 endpoints organized by actor:

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
php artisan test --compact                              # full PHPUnit suite (14 tests passed)
php artisan test --compact --filter=WarehouseTest       # warehouse & stock fulfillment tests
php artisan test --compact --filter=ProductWarehouseSyncTest # stock sync tests

php artisan dusk                                        # full Dusk browser suite (21 tests)
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
- **Spatie permissions**: Gates all admin/shop actions via `CheckPermission` middleware, backed by `config/acl.php`.
- **Demo mode**: `DemoModeMiddleware` blocks mutating actions (POST/PUT/DELETE) in demo environments.
- **MAMP FastCGI Pass-through**: Root `.htaccess` configured to pass `Authorization` header through to PHP for Sanctum bearer tokens.
- **CSRF & Sanitization**: `VerifyCsrfToken` protects web routes; `mews/purifier` sanitizes HTML content.

---

_Last updated: 2026-07-29. Generated for contributor onboarding._
