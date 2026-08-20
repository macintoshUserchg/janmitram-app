# Janmitram E-Commerce & Warehouse Stock Management System
## End-to-End Architecture & Workflow Documentation

---

### 1. Executive System Overview

The **Janmitram E-Commerce System** operates on **Option A: Strict Warehouse-Only Architecture**. 

Under this architecture:
- **Centralized Master Catalog**: All physical products (`is_digital = false`) are created and managed centrally by Admin as Master Products (`master_product_id = null`).
- **Warehouse Centralized Stocking**: Physical inventory is deposited directly into Central or Regional Logistics Hubs (`WarehouseStock`) rather than individual vendor shops.
- **Master-Copy Cloning Pattern (`cloneMasterToShop`)**: Vendor shops do not create physical inventory from scratch. Instead, shops request stock dispatches from their **Linked Warehouse** (`warehouse_id`). Upon Admin fulfillment, physical stock is dispatched and cloned/updated into a **Shop Copy Product** (`master_product_id = masterProduct->id`, `shop_id = shop->id`) carrying full category, subcategory, brand, variant (colors/sizes), media, and translation attributes.
- **Strict Ledger Auditing**: Warehouse-level stock movements (initial addition, warehouse transfer, shop request dispatch, manual adjustment) are immutably logged in `StockLedger`. Sales (online orders and POS) draw from the Shop Copy Product's inventory only — they decrement `products.quantity` and are **not** written to `StockLedger` (warehouse stock is consumed at stocking/dispatch time, not at sale time).
- **Stock Dispatch & Invoice Registry**: Official Janmitram PDF and printable invoices are generated for all completed stock requests and orders.

---

### 2. End-to-End Workflow Diagram

```
┌─────────────────────────────────────────────────────────────────────────────────────────┐
│ PHASE 1: MASTER PRODUCT CREATION & BULK WAREHOUSE SEEDING                               │
│ Admin creates Master Product (master_product_id = null, is_stock_managed = true).       │
│ → Initial stock quantity is allocated directly into Central/Regional Warehouse.         │
│ → StockLedger entry created (reference_type = 'admin_addition').                        │
└─────────────────────────────────────────────────────────────────────────────────────────┘
                                           │
                                           ▼
┌─────────────────────────────────────────────────────────────────────────────────────────┐
│ PHASE 2: ADMIN SHOP & LINKED LOGISTICS HUB BINDING                                      │
│ Admin creates or edits a Vendor Shop (admin/shops).                                     │
│ → Admin binds the shop to a specific Linked Warehouse (warehouse_id).                   │
└─────────────────────────────────────────────────────────────────────────────────────────┘
                                           │
                                           ▼
┌─────────────────────────────────────────────────────────────────────────────────────────┐
│ PHASE 3: VENDOR SHOP STOCK REQUEST                                                     │
│ Shop Vendor opens Shop Panel → Stock Management → Request New Stock.                     │
│ → Selection strictly restricted to vendor's Linked Warehouse stock.                    │
│ → Displays live available warehouse stock levels in option dropdowns & card steppers.  │
│ → Vendor submits multi-item batch request (StockRequest status: 'pending').            │
└─────────────────────────────────────────────────────────────────────────────────────────┘
                                           │
                                           ▼
┌─────────────────────────────────────────────────────────────────────────────────────────┐
│ PHASE 4: ADMIN APPROVAL, STOCK DISPATCH & INVOICE GENERATION                            │
│ Admin reviews request in Admin Panel (admin/stock-request/{id}).                       │
│ → Admin clicks "Approve & Fulfill Request". (Informs admin on inventory shortfall).    │
│ → WarehouseService::fulfillStockRequest() executes in a DB Transaction:               │
│    a) Finds matching WarehouseStock via findStock() (smart color/size fallback).       │
│    b) Deducts available warehouse stock (deductQty = min(requested, available)).        │
│    c) Decrements Central Master Product quantity ($masterProduct->quantity).           │
│    d) Clones/Updates Shop Copy Product (cloneMasterToShop) for target shop_id.         │
│    e) Increments Shop Copy Product by actual dispatched qty ($deductQty).              │
│    f) Logs StockLedger entry with reference_type = 'shop_request' (actual qty).        │
│ → On shortfall: only available stock is dispatched; ledger & shop increment match.
└─────────────────────────────────────────────────────────────────────────────────────────┘
                                           │
                                           ▼
┌─────────────────────────────────────────────────────────────────────────────────────────┐
│ PHASE 5: INTER-WAREHOUSE STOCK TRANSFERS (REGIONAL LOGISTICS)                           │
│ Admin transfers inventory between warehouses (admin/warehouse-transfer).               │
│ → Deducts source warehouse stock and increments target warehouse stock.                  │
│ → Smart variant fallback handles missing color/size stock entries gracefully.          │
│ → Logs StockLedger entry with reference_type = 'warehouse_transfer'.                    │
└─────────────────────────────────────────────────────────────────────────────────────────┘
                                           │
                                           ▼
┌─────────────────────────────────────────────────────────────────────────────────────────┐
│ PHASE 6: CUSTOMER ONLINE ORDER & POS SALES FULFILLMENT                                  │
│ Customer places online order OR Vendor processes POS sale at Shop.                      │
│ → Sale processes against local Shop Copy Product ($shopProduct->id).                   │
│ → OrderRepository / PosCartRepository decrements $shopProduct->quantity.               │
│ → No StockLedger entry is written for the sale (see §1 Strict Ledger Auditing).         │
└─────────────────────────────────────────────────────────────────────────────────────────┘
```

---

### 3. Database Schema & Entity Relationships

| Model / Table | Key Attributes | Purpose & Architectural Role |
| :--- | :--- | :--- |
| `Product` (`products`) | `id`, `name`, `shop_id`, `master_product_id`, `is_digital`, `is_stock_managed`, `quantity`, `price` | **Master Products** (`master_product_id = null`): Central catalog items.<br>**Shop Copy Products** (`master_product_id = X`, `shop_id = Y`): Local shop sellable stock items. |
| `Warehouse` (`warehouses`) | `id`, `name`, `code`, `is_default`, `status` | Physical Central or Regional Warehouse hubs storing bulk stock. |
| `WarehouseStock` (`warehouse_stock` — **singular table name**; model sets `protected $table = 'warehouse_stock'`) | `id`, `warehouse_id`, `product_id`, `color_id`, `size_id`, `quantity` | Tracks exact physical stock quantities of products inside a specific warehouse. |
| `StockRequest` (`stock_requests`) | `id`, `shop_id`, `warehouse_id`, `status` (`pending`, `completed`, `rejected`), `notes` | Order request submitted by vendor shop to receive physical inventory from warehouse. |
| `StockRequestItem` (`stock_request_items`) | `id`, `stock_request_id`, `product_id`, `color_id`, `size_id`, `quantity` | Line items attached to a StockRequest. |
| `WarehouseTransfer` (`warehouse_transfers`) | `id`, `transfer_no`, `from_warehouse_id`, `to_warehouse_id`, `status`, `notes` | Tracks inventory transfers between two central/regional warehouses. |
| `WarehouseTransferItem` (`warehouse_transfer_items`) | `id`, `warehouse_transfer_id`, `product_id`, `color_id`, `size_id`, `quantity` | Line items attached to an inter-warehouse transfer. |
| `StockLedger` (`stock_ledgers`) | `id`, `from_warehouse_id`, `to_warehouse_id`, `product_id`, `quantity`, `reference_type` | Immutable audit log of warehouse-level movements only (`admin_addition`, `warehouse_transfer`, `shop_request`, `manual_adjustment`). Sales are **not** ledgered — they decrement Shop Copy Product inventory. |
| `Shop` (`shops`) | `id`, `name`, `user_id`, `warehouse_id` | Vendor shop profile bound to a specific Linked Warehouse. |

---

### 4. Comprehensive Operational Workflows

#### Phase 1: Creating Master Products & Central Stock Deposit
1. Admin opens **Admin Panel** → **Product Management** → **Create Product**.
2. Setting `is_digital = false` automatically marks the product as stock-managed (`is_stock_managed = true`).
3. Initial stock quantity is entered into the **Central Warehouse Stock Quantity** field.
4. Upon saving, `ProductRepository` stores the master product (`master_product_id = null`) and calls `WarehouseService::addStock()`.
5. Stock is deposited into the selected Central Warehouse (`WarehouseStock`) and logged in `StockLedger` with `reference_type = 'admin_addition'`.

#### Phase 2: Admin Linking Vendor Shop to Warehouse
1. Admin opens **Admin Panel** → **Shop Management** → **Add / Edit Shop**.
2. Under *Shop Information*, Admin selects the **Linked Warehouse** from the dropdown menu.
3. The shop's `warehouse_id` foreign key is updated in the database. All future stock requests from this shop will default strictly to this warehouse hub.

#### Phase 3: Vendor Shop Submitting Stock Requests
1. Vendor logs into **Shop Panel** (`/shop/login`) → Navigates to **Stock Management** → **Request New Stock** (`/shop/stock-request/create`).
2. The interface displays the vendor's **Linked Warehouse** banner card.
3. The Product Catalog grid displays live available warehouse stock levels, quantity steppers, and checkboxes.
4. Vendor selects products, specifies quantities for each item, and clicks **Submit Stock Request**.
5. A `StockRequest` is saved with status `pending` alongside its `StockRequestItem` entries.

#### Phase 4: Admin Fulfilling Stock Requests & Invoicing
1. Admin opens **Admin Panel** → **Stock Requests** (`/admin/stock-request`).
2. Admin reviews request details, target shop, requested items, and available warehouse inventory.
3. If warehouse inventory is insufficient for any item, an **Inventory Shortfall Warning** is displayed to the admin.
4. Admin clicks **Approve & Fulfill Request**.
5. `WarehouseService::fulfillStockRequest()` runs inside a database transaction:
   - Finds matching `WarehouseStock` via `findStock()` with smart color/size fallback.
   - Deducts available stock: `deductQty = min(requested, available)`.
   - Decrements `WarehouseStock` and `$masterProduct->quantity` by `$deductQty`.
   - Executes `cloneMasterToShop()`, creating or updating the shop's local copy (`master_product_id = master->id`, `shop_id = shop->id`).
   - Increments local sellable stock `$shopProduct->quantity` by `$deductQty` (actual dispatched qty).
   - Logs an entry in `StockLedger` (`reference_type = 'shop_request'`) with actual dispatched quantity.
   - Updates `StockRequest` status to `completed`.
6. Admin or Shop can view and print the official **Janmitram Stock Dispatch Invoice** (`/admin/stock-request/{id}/invoice`).

> **Shortfall Behavior**: If warehouse stock < requested qty, only available stock is dispatched. The shop receives `$deductQty`, the ledger records `$deductQty`, and the admin sees the shortfall warning. No phantom stock is created.

#### Phase 5: Inter-Warehouse Stock Transfers
1. Admin opens **Admin Panel** → **Warehouse Transfers** (`/admin/warehouse-transfer`).
2. Admin selects *Source Warehouse*, *Target Warehouse*, items, and quantities.
3. Upon approval (`WarehouseTransferController@complete`), `WarehouseService::transfer()` executes:
   - Decrements stock from source warehouse (`WarehouseStock`).
   - Increments stock in target warehouse (`WarehouseStock`).
   - Applies smart variant fallback logic if specific color/size entries are absent.
   - Logs entry in `StockLedger` (`reference_type = 'warehouse_transfer'`).

#### Phase 6: Customer Online Orders & Vendor POS Sales
1. **Online Customer Order**:
   - Customer orders product from Shop via Vue SPA or Mobile App.
   - `OrderRepository` decrements `$shopProduct->quantity` (the Shop Copy Product) inside a `DB::transaction`; any mid-way failure rolls back all writes.
   - No `StockLedger` entry is written for the sale — sales draw from shop inventory only; warehouse stock was already consumed when the shop's stock request was dispatched (Phase 4).
   - **Reorder** (`OrderRepository::reOrder`) runs through the same `createOrderForShop()`/`groupLinesByShop()` processor as checkout — it re-prices at current prices (variants, active flash sale, size/color), re-allocates like a fresh checkout, persists VAT, and links the payment. Flash-sale pricing uses the `isActive()` scope, so an ended flash sale never applies its old price.
   - **Membership card** — a customer with an active card enters its number at checkout for a flat `card_discount_percentage`% off the subtotal (minimum `card_min_order_amount`), **instead-of** any coupon. (See `project_notes.md` § Card System.)
2. **In-Person Vendor POS Sale**:
   - Vendor processes POS transaction in Shop Panel (`/shop/pos`).
   - `PosCartRepository` decrements `$shopProduct->quantity` (the Shop Copy Product) inside a `DB::transaction`.
   - No `StockLedger` entry is written for the sale — same semantics as the online order above.
   - A customer's membership card number can be entered at the counter for the same card discount (instead-of coupon).

---

### 5. Shop Panel Navigation & Sidebar Structure

The **Shop Vendor Sidebar** (`resources/views/layouts/partials/shop-menu.blade.php`) is streamlined for Option A Strict Warehouse Operation:

- 📊 **Dashboard** (`/shop/dashboard`)
- 📦 **Stock Management** (Dropdown)
  - 📋 **Stock Requests** (`/shop/stock-request`)
  - ➕ **Request New Stock** (`/shop/stock-request/create`)
  - 🏬 **My Shop Inventory** (`/shop/shop-inventory`)
- 🛒 **Order Management** (`/shop/orders`)
- 💳 **POS Management** (`/shop/pos`)
- 💰 **Withdrawals / Earnings** (`/shop/withdraws`)
- 🏷️ **Promotion & Flash Deals** (`/shop/promotions`)
- 💬 **Customer Chat / Messages** (`/shop/messages`)
- ⚙️ **Shop Settings** (`/shop/profile`)

> [!NOTE]
> Category Management, Product Creation/Variant Management, and Employee Management have been removed from the Shop Sidebar as product creation and master definitions are strictly managed by Central Admin. The sidebar's collapsed/expanded state persists across reloads via `localStorage`.

---

### 6. Complete API & Web Routes Reference

> **Note:** this table covers the warehouse/stock-request surface only — it is a
> **subset** of the real route layer. `routes/web.php` is 651 lines (~436
> `Route::` definitions: SPA entry, full admin panel, shop panel, payment
> callbacks) and `routes/api.php` has ~109 definitions (customer/seller/rider
> mobile APIs). Both files are re-scaffolded reconstructions (see the
> "RECONSTRUCTION NOTE" header in each). Inspect the full set with
> `php artisan route:list`.

| Section | Method | URI Path | Route Name | Action / Controller |
| :--- | :--- | :--- | :--- | :--- |
| **Admin Warehouse** | `GET` | `/admin/warehouse` | `admin.warehouse.index` | `WarehouseController@index` |
| **Admin Warehouse** | `GET` | `/admin/warehouse/{id}` | `admin.warehouse.show` | `WarehouseController@show` |
| **Admin Warehouse Stock** | `POST` | `/admin/warehouse/{id}/stock/add` | `admin.warehouse.stock.add` | `WarehouseController@addStock` |
| **Admin Warehouse Stock** | `DELETE` | `/admin/warehouse/{id}/stock/clear` | `admin.warehouse.stock.clear` | `WarehouseController@clearStock` |
| **Admin Warehouse Transfer** | `GET` | `/admin/warehouse-transfer` | `admin.warehouse-transfer.index` | `WarehouseTransferController@index` |
| **Admin Warehouse Transfer** | `POST` | `/admin/warehouse-transfer/{id}/complete` | `admin.warehouse-transfer.complete` | `WarehouseTransferController@complete` |
| **Admin Stock Request** | `GET` | `/admin/stock-request` | `admin.stock-request.index` | `StockRequestController@index` |
| **Admin Stock Request** | `POST` | `/admin/stock-request/{id}/approve` | `admin.stock-request.approve` | `StockRequestController@approve` |
| **Admin Invoices** | `GET` | `/admin/invoices` | `admin.invoice.index` | `InvoiceController@index` |
| **Admin Invoice Download** | `GET` | `/admin/stock-request/{id}/invoice` | `admin.stock-request.invoice` | `StockRequestController@invoice` |
| **Shop Stock Request** | `GET` | `/shop/stock-request` | `shop.stock-request.index` | `Shop\StockRequestController@index` |
| **Shop Stock Request** | `GET` | `/shop/stock-request/create` | `shop.stock-request.create` | `Shop\StockRequestController@create` |
| **Shop Stock Request** | `POST` | `/shop/stock-request` | `shop.stock-request.store` | `Shop\StockRequestController@store` |
| **Shop Invoice View** | `GET` | `/shop/stock-request/{id}/invoice` | `shop.stock-request.invoice` | `Shop\StockRequestController@invoice` |
| **Shop Inventory** | `GET` | `/shop/shop-inventory` | `shop.shop-inventory.index` | `Shop\StockRequestController@inventory` |
| **Shop Dashboard** | `GET` | `/shop/dashboard` | `shop.dashboard` | `Shop\DashboardController@index` |

---

### 7. Order Management, Tax & Invoice Breakdown

#### Dynamic Master Product GST & Tax Inheritance
- **Central Slabs**: Taxes (GST 5%, 12%, 18%, 28%) are attached to Master Catalog items or configured as the Platform Default Tax under `Admin -> VAT & Tax`.
- **Shop Inventory Propagation**: When orders are placed (Web checkout or Shop POS), `OrderRepository` and `POSController` check `$product->vatTaxes()` and fall back to `$product->masterProduct?->vatTaxes()`.
- **Dynamic Updates**: Modifying a Master Product's tax rate or changing the Platform Default Tax rate instantly takes effect across all shop inventories on all future orders.

#### Admin Orders Index & Details
- **GST / Tax Column (`/admin/order`)**: Displays exact tax amount in rupees with sortable headers and multi-tax badges (`CGST`, `SGST`, `IGST`).
- **Discounts Column (`/admin/order`)**: Displays Coupon discounts with code, Membership Card discounts with card number, and Special discounts.
- **Order Details (`/admin/order/{id}`, `/shop/order/{id}`)**: Full financial breakdown including Sub Total, Coupon Discount, Card Discount, Delivery Charge, Itemized VAT/GST percentages, and Grand Total.
- **Invoice PDF (`PDF/invoice.blade.php`)**: Generates compliant invoices with item units, dynamic QR codes, discounts breakdown, and tax percentages.

### 8. MLM Downline Network Capacity Architecture
- **Direct Downline Capacity Limits**:
  - **Main Janmitram Shop (ID: 1)**: Unlimited direct downline capacity.
  - **Standard Partner Shops (ID != 1)**: Maximum of **10 direct downline shops** ($1\text{ self} + 10\text{ downlines} = 11\text{ direct frontline members}$).
- **Generational Multi-Level Depth**: No limit on downstream generation depth. Once a partner's 10 frontline slots are filled, recruits register under existing team members, building deep legs that roll up for Phase 2 Group Sales bonuses.
- **Enforcement Mechanisms**:
  - Validation in `ShopCreateRequest` / `verifySponsor` prevents registrations under saturated sponsors.
  - Shop Panel partner creation shows direct capacity counters (`X / 10 slots filled`).
  - Admin Panel shop creation/edit highlights direct downline capacity in sponsor dropdowns.

### 9. First Stock Transfer Minimum Threshold (₹3,000)
- **Franchise Readiness Rule**: Any newly created or newly approved franchise shop must receive an initial physical inventory assignment or stock request dispatch of at least **₹3,000.00** total aggregate product value.
- **Implementation**:
  - `Shop::MIN_FIRST_STOCK_TRANSFER_AMOUNT = 3000.0`.
  * `Shop::hasReceivedStock(): bool` and `Shop::isFirstStockTransfer(): bool` determine if the shop is on its initial stocking cycle.
  * Enforced in both `StockAssignmentRequest` (Admin warehouse-to-shop assignment) and `StockRequestRequest` (Vendor shop stock request).
  * Subsequent reorders/replenishments have no minimum transfer restrictions.
  * Real-time UI calculators in Admin Assignment and Shop Stock Request views provide live total value computations and shortfall alerts.

### 10. Customer-Controlled Order Fulfillment Mode
- **Dual Fulfillment Options at Checkout**:
  - **Auto-Deliver from Nearest Shop (Default)**: Dynamically evaluates candidate shops with available inventory within 50 km using Haversine distance, routing to the closest franchise shop.
  - **Force Delivery Strictly from Selected Shop**: Bypasses dynamic reallocation, locking order lines strictly to the selected shop in the user's cart (`$cart->shop_id`). Defensively checks local stock and rejects overselling.
- **Frontend & Backend Integration**:
  - Validated in `OrderRequest` (`'fulfill_from_nearest_shop' => 'nullable|boolean'`).
  - Implemented in `OrderRepository::groupLinesByShop`.
  - Interactive Fulfillment Mode toggle card in `CheckoutOrderSummary.vue` and `BuyNowCheckoutOrderSummary.vue`.

### 11. Customer Reviews & Franchise Shop Reputation Engine
- **Admin Moderation & Approval Workflow**:
  - All new customer reviews start with `is_active = 0` (Pending Approval) and are hidden from the public store via `ActiveScope` on the `Review` model until approved by an Admin.
  - Reviews simultaneously bind `product_id`, `shop_id`, `customer_id`, and `order_id` (verified purchase link).
- **Multi-Photo Attachments & Official Replies**:
  - Customers can upload up to 5 unboxing/product photos (`photos` JSON column) with a full-screen lightbox preview.
  - Admins and shop managers can publish official responses (`reply`, `replied_at`).
- **Master Product Review Aggregation**:
  - Master catalog queries in `ReviewController@index` aggregate approved reviews across all cloned franchise shop copies.
- **Dynamic Real-Time Shop Star Ratings**:
  - Shop star ratings (`Shop::averageRating`) are calculated dynamically as the live average of all approved product reviews fulfilled by that shop ($1.0 - 5.0\star$, defaulting to $5.0\star$ baseline for brand new shops).
- **Admin Moderation Center (`/admin/review`)**:
  - KPI summary metrics (Total Reviews, Pending Approval, Approved & Live, Average Rating).
  - Status tabs, franchise shop dropdown, star rating dropdown, customer/product search, quick action toolbar (**Approve**, **Reject**, **Reply Modal**, **Delete**), and live pending badge in the admin sidebar.

### 12. Monthly Shop Payout Execution Schedule
- **Monthly Payout Run**: Root Administrator executes monthly payout calculations on or after the 1st of each month for the preceding calendar month via `php artisan payout:monthly` or `/admin/payout`.
- **Manual Withdrawals**: Franchise shops can submit withdrawal requests for approved balances at any time via `/shop/withdraw`.

---

### 13. Automated Zero-Permission IP Geolocation & City Vicinity Engine
- **IP-First Location Resolution**:
  - Automatically resolves visitor IP on page load via `LocationController@resolve` (`/api/location/resolve`) into City, State, and PIN code with zero permission popups.
  - Active franchise shops in the customer's city are matched and bound to the session.
  - Non-Indian / international IPs (`countryCode !== 'IN'`) gracefully default to Central Hub (`Jaipur, Rajasthan - 302013`).
- **Storefront & Delivery UI**:
  - Pinia `LocationStore.js` with `localStorage` caching (`janmitram_user_location`).
  - `LocationPickerModal.vue`: Allows switching destination city, entering 6-digit postal PIN codes, or picking from local branches.
  - `NavbarMiddle.vue`: Prominent `📍 Delivering to: [City (Pincode)] ▾` indicator.

### 14. Homepage Multi-Branch Shop Round-Robin Product Distribution
- **Fair Round-Robin Catalog Sequence**:
  - In `HomeController@index`, items in the **Popular Products** section are picked in a round-robin sequence across all active branch shops in the user's city (Shop A ➔ item 1, Shop B ➔ item 2, Shop C ➔ item 3, etc.).
  - **Zero Duplicate Product Names**: Automated deduplication guarantees that each product name appears only once across the entire section.
- **Strict Main Shop Exclusion**:
  - Products from **Main Janmitram Shop (Shop ID: 1 / Central Warehouse)** are strictly excluded from both the homepage **Popular Products** and **Just For You** sections, driving customer orders directly to local franchise branches.
- **Fallback for Non-Branch Cities**:
  - Visitors from cities without a local branch receive a balanced, randomized assortment across all active regional branch shops nationwide.

### 15. Google Maps Platform & Doorstep Geocoding SDK
- **Google Maps JavaScript SDK & Google Places API (New)**:
  - Transitioned mapping and geocoding stack to Google Maps JavaScript SDK and Places API (New) with `GOOGLE_MAPS_API_KEY`.
  - `GoogleMapsService` provides Places Autocomplete, place details lookup, and doorstep reverse geocoding with in-memory query caching and multi-tier coordinate resolution.
  - Upgraded `MapDisplay.vue` (interactive draggable pin, satellite hybrid layer, rich doorstep address banner) and `RealTimeMapDisplay.vue` (live driver GPS tracking).

### 16. Razorpay Payment Gateway Optimization & Window Lifecycle Sync
- **Single Gateway Pre-Selection**: Automatically pre-selects Razorpay and hides redundant gateway selection cards when Razorpay is the only active online gateway configured.
- **Popup Lifecycle Management**: Integrated `window.postMessage` and `localStorage` listener synchronization in `Razorpay/ProcessController.php` so payment popup tabs automatically close upon transaction completion and smoothly return customers to the order confirmation screen.

### 17. Shop Business Models: Direct / Zero-Fee Support
- **Dual Business Model Support**: Under `Admin -> Business Settings -> Shop Settings`, admins can configure either the traditional **Commission-Based Model** or the **Direct / None Zero-Fee Model**.
- **Wallet Debit Protection**: Commission deductions are automatically bypassed on zero-fee shop orders, while full audit logging is preserved.

### 18. Real-Time Master-to-Shop Price, Discount & Variant Synchronization
- **Automated Price & Discount Cascade (`syncShopCopies`)**:
  - Whenever Root Admin edits a Master Product (`master_product_id == null`) in `ProductRepository::updateByRequest()` or performs bulk spreadsheet updates via `ProductRepository::importRows()`, the system atomically synchronizes all child shop copies (`Product::where('master_product_id', $master->id)`).
  - Updates `price` (Regular / MRP), `discount_price` (Offer Price), `buy_price` (Wholesale Cost), `unit_id`, `brand_id`, and `min_order_quantity`.
  - Re-syncs variant extra price pivots (`product_colors.price` and `product_sizes.price`) so size/color price additions remain uniform across all franchise branches.
- **Stock Fulfillment Price Refresh**:
  - In `WarehouseService::cloneMasterToShop()`, fulfilling inventory dispatches refreshes existing shop copy prices, metadata, and variant pivot prices before returning.

---

### 19. Automated Testing & Code Verification

To ensure system integrity across all warehouse transactions, stock request fulfillments, price synchronization, review moderation, geolocation, and order allocation:

```bash
# Run all automated feature/unit tests compact (162 test classes / 618 assertions)
php artisan test --compact

# Filter specific feature test suites
php artisan test --compact --filter=ProductPriceSynchronizationTest
php artisan test --compact --filter=LocationResolutionTest
php artisan test --compact --filter=GoogleMapsIntegrationTest
php artisan test --compact --filter=ShopBusinessSettingTest
php artisan test --compact --filter=FirstStockTransferThresholdTest
php artisan test --compact --filter=OrderShopFulfillmentModeTest
php artisan test --compact --filter=ReviewApprovalAndModerationTest
php artisan test --compact --filter=ShopAllocationTest
php artisan test --compact --filter=WarehouseTest
php artisan test --compact --filter=PayoutTest

# Run Laravel Dusk browser automation tests
php artisan dusk

# Apply Laravel Pint code style formatting
vendor/bin/pint --dirty --format agent
```

---

_Last updated: 2026-08-20. Option A Warehouse, Real-Time Master-to-Shop Price Synchronization, IP Geolocation Engine, Round-Robin Multi-Shop Catalog, Google Maps SDK, First Stock Threshold (₹3,000), Fulfillment Mode Toggle, Review Approval & Moderation, and MLM Downline Capacity architecture fully verified._


