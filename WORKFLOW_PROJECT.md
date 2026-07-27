# Janmitram E-Commerce & Warehouse Stock Management System
## End-to-End Architecture & Workflow Documentation

---

### 1. Executive System Overview

The **Janmitram E-Commerce System** operates on **Option A: Strict Warehouse-Only Architecture**. 

Under this architecture:
- **Centralized Master Catalog**: All physical products (`is_digital = false`) are managed centrally by Admin as Master Products (`master_product_id = null`).
- **Warehouse Centralized Stocking**: Physical stock is added directly to Central/Regional Warehouses (`WarehouseStock`) rather than individual vendor shops.
- **Master-Copy Cloning Pattern**: Vendor Shops do not create direct physical inventory from scratch. Instead, shops request stock dispatches from their **Linked Warehouse**. Upon Admin approval, physical inventory is dispatched and cloned/updated into a **Shop Copy Product** (`master_product_id = masterProduct->id`, `shop_id = shop->id`) with full category, brand, variant, media, and translation attributes.
- **Strict Ledger Auditing**: Every stock transaction (initial creation, warehouse dispatch, customer order sale, POS sale) is logged immutably in `StockLedger`.

---

### 2. End-to-End Workflow Diagram

```
┌─────────────────────────────────────────────────────────────────────────────────────────┐
│ PHASE 1: MASTER PRODUCT CREATION & WAREHOUSE SEEDING                                   │
│ Admin creates Physical Master Product (master_product_id = null, is_stock_managed=true). │
│ → Initial stock quantity is allocated directly into Central Warehouse (WarehouseStock)  │
│ → StockLedger entry created ('initial_product_create').                                 │
└─────────────────────────────────────────────────────────────────────────────────────────┘
                                           │
                                           ▼
┌─────────────────────────────────────────────────────────────────────────────────────────┐
│ PHASE 2: ADMIN SHOP & LINKED WAREHOUSE BINDING                                         │
│ Admin creates or edits a Vendor Shop (admin/shops).                                     │
│ → Admin binds the shop to a specific Linked Warehouse (warehouse_id).                   │
└─────────────────────────────────────────────────────────────────────────────────────────┘
                                           │
                                           ▼
┌─────────────────────────────────────────────────────────────────────────────────────────┐
│ PHASE 3: VENDOR SHOP STOCK REQUEST                                                     │
│ Shop Vendor opens Shop Panel → Stock Requests (shop/stock-request/create).             │
│ → System restricts selection strictly to vendor's Linked Warehouse stock.              │
│ → Displays live available warehouse stock levels in option dropdowns.                  │
│ → Vendor submits multi-item batch request. Status: 'pending'.                          │
└─────────────────────────────────────────────────────────────────────────────────────────┘
                                           │
                                           ▼
┌─────────────────────────────────────────────────────────────────────────────────────────┐
│ PHASE 4: ADMIN APPROVAL & STOCK DISPATCH                                               │
│ Admin reviews request in Admin Panel (admin/stock-request/{id}).                       │
│ → Admin clicks "Approve & Fulfill Request".                                            │
│ → WarehouseService::fulfillStockRequest() executes in a DB Transaction:               │
│    a) Decrements Central WarehouseStock quantity.                                       │
│    b) Decrements Central Master Product quantity.                                       │
│    c) Clones/Updates Shop Copy Product (cloneMasterToShop) for shop_id.                 │
│    d) Increments Shop Copy Product sellable quantity ($shopProduct->quantity).          │
│    e) Logs StockLedger entry with reference_type = 'shop_request'.                     │
└─────────────────────────────────────────────────────────────────────────────────────────┘
                                           │
                                           ▼
┌─────────────────────────────────────────────────────────────────────────────────────────┐
│ PHASE 5: CUSTOMER ORDER & POS SALES FULFILLMENT                                        │
│ Customer places online order OR Vendor processes POS sale at Shop.                      │
│ → System processes sale against Shop Copy Product ($shopProduct->id).                   │
│ → OrderRepository / PosCartRepository decrements $shopProduct->quantity.               │
│ → WarehouseService::deductStock() logs ledger entries ('order_sale' / 'pos_sale').    │
└─────────────────────────────────────────────────────────────────────────────────────────┘
```

---

### 3. Database Schema & Entity Relationships

| Model / Table | Key Attributes | Purpose & Architectural Role |
| :--- | :--- | :--- |
| `Product` (`products`) | `id`, `name`, `shop_id`, `master_product_id`, `is_digital`, `is_stock_managed`, `quantity`, `price` | **Master Products** (`master_product_id = null`): Central catalog item.<br>**Shop Copy Products** (`master_product_id = X`, `shop_id = Y`): Local shop sellable stock item. |
| `Warehouse` (`warehouses`) | `id`, `name`, `code`, `is_default`, `status` | Physical Central or Regional Warehouse hub storing bulk stock. |
| `WarehouseStock` (`warehouse_stocks`) | `id`, `warehouse_id`, `product_id`, `color_id`, `size_id`, `quantity` | Tracks exact physical stock quantities of products inside a specific warehouse. |
| `StockRequest` (`stock_requests`) | `id`, `shop_id`, `warehouse_id`, `status` (`pending`, `completed`, `rejected`), `notes` | Order request submitted by vendor shop to receive physical inventory from warehouse. |
| `StockRequestItem` (`stock_request_items`) | `id`, `stock_request_id`, `product_id`, `quantity` | Individual line items attached to a StockRequest. |
| `StockLedger` (`stock_ledgers`) | `id`, `warehouse_id`, `shop_id`, `product_id`, `type` (`in`/`out`), `quantity`, `reference_type` | Immutable audit trail for stock movements (`initial_product_create`, `shop_request`, `order_sale`, `pos_sale`). |
| `Shop` (`shops`) | `id`, `name`, `user_id`, `warehouse_id` | Vendor shop profile bound to a specific Linked Warehouse. |

---

### 4. Comprehensive Operational Workflows

#### Phase 1: Creating Physical Master Products & Central Stock
1. Admin opens **Admin Panel** → **Product Management** → **Create Product**.
2. Setting `is_digital = false` automatically forces `is_stock_managed = true`.
3. Initial stock quantity is entered into the **Initial Warehouse Stock Quantity** field.
4. Upon saving, `ProductRepository` stores the master product (`master_product_id = null`) and calls `WarehouseService::addStock()`.
5. Physical stock is deposited into Central Warehouse (`warehouse_id = 1`) and logged in `StockLedger`.

#### Phase 2: Admin Linking Shop to Warehouse
1. Admin opens **Admin Panel** → **Shop Management** → **Add / Edit Shop**.
2. Under *Shop Information*, Admin selects the **Linked Warehouse** from the dropdown menu.
3. If no specific sub-warehouse is selected, the shop defaults to the **Central Warehouse**.

#### Phase 3: Vendor Shop Submitting Stock Requests
1. Vendor logs into **Shop Panel** (`/shop/login`) → Navigates to **Stock Requests** (`/shop/stock-request`).
2. Vendor clicks **"+ New Stock Request"** (`/shop/stock-request/create`).
3. The interface displays the vendor's **Linked Warehouse** banner.
4. Product dropdown options display live available warehouse stock:
   > `[Product Name] — Available in Central Warehouse: 100 units`
5. Vendor selects products, specifies quantities, optionally adds notes, and clicks **Submit Stock Request**.
6. A `StockRequest` is saved with status `pending`.

#### Phase 4: Admin Fulfilling Stock Requests
1. Admin opens **Admin Panel** → **Stock Requests** (`/admin/stock-request`).
2. Admin reviews request details, requested items, and target shop.
3. Admin clicks **Approve & Fulfill**.
4. `WarehouseService::fulfillStockRequest()` runs inside a database transaction:
   - Validates available warehouse stock for each requested item.
   - Decrements stock in `WarehouseStock` and `Product` (Master).
   - Calls `cloneMasterToShop()`, creating or updating the shop's local copy (`master_product_id = master->id`, `shop_id = shop->id`).
   - Increments local shop stock `$shopProduct->quantity`.
   - Creates an entry in `StockLedger` with `type = 'out'` and `reference_type = 'shop_request'`.
   - Updates `StockRequest` status to `completed`.

#### Phase 5: Sales & Order Stock Deductions
1. **Online Customer Order**:
   - Customer orders product from Shop.
   - `OrderRepository` decrements `$shopProduct->quantity`.
   - `WarehouseService::deductStock()` logs `StockLedger` entry (`reference_type = 'order_sale'`).
2. **In-Person POS Sale**:
   - Vendor processes POS transaction in Shop Panel (`/shop/pos`).
   - `PosCartRepository` decrements `$shopProduct->quantity`.
   - `WarehouseService::deductStock()` logs `StockLedger` entry (`reference_type = 'pos_sale'`).

---

### 5. Shop Panel Navigation & Sidebar Structure

The **Shop Vendor Sidebar** (`resources/views/layouts/partials/shop-menu.blade.php`) has been streamlined for Strict Warehouse Operation:

- 📊 **Dashboard** (`/shop/dashboard`)
- 📦 **Stock Requests** (`/shop/stock-request`)
- 🏬 **My Shop Inventory** (`/shop/shop-inventory`)
- 🛒 **Order Management** (`/shop/orders`)
- 💳 **POS Management** (`/shop/pos`)
- 💰 **Withdrawals / Earnings** (`/shop/withdraws`)
- 🏷️ **Promotion & Flash Deals** (`/shop/promotions`)
- 💬 **Customer Chat / Messages** (`/shop/messages`)
- ⚙️ **Shop Settings** (`/shop/profile`)

> [!NOTE]
> Category Management, Product Creation/Variant Management, and Employee Management have been removed from the Shop Sidebar as product creation and master definitions are strictly managed by Central Admin.

---

### 6. Key API & Web Routes Reference

| Section | Method | URI Path | Route Name | Action / Controller |
| :--- | :--- | :--- | :--- | :--- |
| **Admin Warehouse** | `GET` | `/admin/warehouse` | `admin.warehouse.index` | `WarehouseController@index` |
| **Admin Warehouse** | `GET` | `/admin/warehouse/{id}` | `admin.warehouse.show` | `WarehouseController@show` |
| **Admin Warehouse** | `POST` | `/admin/warehouse/{id}/clear-stock` | `admin.warehouse.stock.clear` | `WarehouseController@clearStock` |
| **Admin Warehouse** | `DELETE` | `/admin/warehouse-stock/{id}` | `admin.warehouse-stock.destroy` | `WarehouseController@destroyStock` |
| **Admin Stock Request** | `GET` | `/admin/stock-request` | `admin.stock-request.index` | `StockRequestController@index` |
| **Admin Stock Request** | `POST` | `/admin/stock-request/{id}/fulfill` | `admin.stock-request.fulfill` | `StockRequestController@fulfill` |
| **Shop Stock Request** | `GET` | `/shop/stock-request` | `shop.stock-request.index` | `Shop\StockRequestController@index` |
| **Shop Stock Request** | `GET` | `/shop/stock-request/create` | `shop.stock-request.create` | `Shop\StockRequestController@create` |
| **Shop Stock Request** | `POST` | `/shop/stock-request` | `shop.stock-request.store` | `Shop\StockRequestController@store` |
| **Shop Inventory** | `GET` | `/shop/shop-inventory` | `shop.shop-inventory.index` | `Shop\StockRequestController@inventory` |
| **Shop Dashboard** | `GET` | `/shop/dashboard` | `shop.dashboard` | `Shop\DashboardController@index` |

---

### 7. Automated Testing & Code Verification

To ensure system integrity across all warehouse transactions and stock calculations, run the test suites:

```bash
# Run all automated tests compact
php artisan test --compact

# Filter specific warehouse tests
php artisan test --compact --filter=WarehouseTest

# Apply Laravel Pint code style formatting
vendor/bin/pint --dirty --format agent
```
