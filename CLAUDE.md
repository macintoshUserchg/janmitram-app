# Janmitram — Project notes (Claude Code)

> **⚠️ OBSOLETE**: This CLAUDE.md is legacy and no longer updated. The authoritative architecture &
> coding-standard references are **WORKFLOW_PROJECT.md** (warehouse architecture & workflows) and
> **project_notes.md** (full project map, structural guidelines, stack). Start there for all
> architectural decisions and code conventions.

Below are the project-specific gotchas that the docs above don't fully restate, kept because they
are not derivable from a fresh read of the code.

## Database
- Single **`DB_DATABASE`** connection from `.env` — never hardcode a schema. Local dev = `janmitram`
  (MAMP `root/root`); production (Hostinger) = `u939461333_app_janmitram`. The local server also hosts
  `u939461333_janmitra`, `aitradex_db`, `lifeskills_db` — those are **not** referenced by the Laravel code.

## Admin & SPA hybrid
- Admin panel = Blade + Bootstrap (`resources/views/admin/`). Customer shop = Vue 3 SPA
  (`resources/js/`) served via Vite. Vite uses `base: ""` (relative paths) for subdirectory deploy
  under `/janmitram-app/`.

## Modules (`nwidart/laravel-modules`)
- Feature modules `Modules/` (`purchase`, `report`) are **asset-only stubs** — only `css/`/`icon.*`,
  **zero PHP**. `module_exists('Purchase')` therefore returns **false**, so every `module_exists()`
  path (stock-out on order cancel, the barcode scanner in `admin/order/show`) is **dormant**. Treat
  `purchase`/`report` as server-withheld/aspirational.

## Custom middleware & permissions
- Active middleware: `CheckSubscription`, `CheckHasRootUser`, `ShopAuthenticate` (alias `authShop`),
  `DemoModeMiddleware`, `LocalizationManage`. `User` uses `Spatie\Permission\Traits\HasRoles`.
- `CheckPermission` middleware and `config/acl.php` define a full permission tree, but **`checkPermission`
  is not wired into any route** — admin routes gate on `['auth','role:root']` only, shop routes on
  `['authShop']`. Practical consequence: **only the `root` role reaches the admin panel**, and the
  `admin`/`adminMultiShop` roles cannot reach admin routes. Re-wire `checkPermission` (or retire
  `acl.php`) before relying on per-role permissions.

## Payment gateways
- 11 pluggable gateways via `Gateway/PaymentGatewayController` + per-gateway `ProcessController.php`:
  AamarPay, Bkash, CashFree, JazzCash, PayPal, PayStack, PayTabs, PayU, QiCard, Razorpay, Stripe.
- **Known gap:** `Gateway/PayPal|Bkash|PayTabs/ProcessController.php` reference `paypal.payment.success`,
  `bkash.payment.execute`, `paytabs.payment.callback` named routes, but **those routes are not
  registered** — PayPal capture / Bkash execute / PayTabs callback will throw "Route not defined".

## Repository pattern
- `app/Repositories/*` extend `App\Support\Repositories\Repository` (static `model()`,
  `query/create/find/update`), using `storeByRequest()`-style factories. Follow that pattern.

## Conventions
- Match sibling-file conventions (structure, naming, style). Reuse existing components/helpers before
  writing new ones. No unrequested new base folders or dependency changes without approval.

## Frontend / verification
- If a frontend change isn't reflected, run `npm run build`, `npm run dev`, or `composer run dev`.
- Dusk browser tests: `tests/Browser/` — run with `php artisan dusk --filter=testName`.
- After PHP edits, run `vendor/bin/pint --dirty --format agent`.