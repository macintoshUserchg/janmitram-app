# Janmitram System Roles & Permissions Matrix (`roles.md`)

This document outlines the complete role-based access control (RBAC), hierarchy, differences between administrative roles, and the granular Spatie permissions structure across the Janmitram application.

---

## 1. System Roles Overview

| Role | Role Key (`name`) | Default Portal | Primary Scope & Responsibility |
|---|---|---|---|
| **Super Admin** | `root` | `/admin/*` | Full platform owner. Unrestricted master access, infrastructure & payment gateways, MLM payout engine batch execution, master warehouse transfers, and system governance. |
| **System Admin** | `admin` | `/admin/*` | Operational administrator. Daily management of customer orders, stock approvals, customer and rider verifications, and support tickets. |
| **Franchise Shop** | `shop` | `/shop/*` | Store partner / Kendra owner. Walk-in **POS counter billing**, fulfilling online local deliveries, requesting inventory from Central Logistics, managing downline MLM shop affiliate tree, and submitting wallet payout withdrawals. |
| **Supplier** | `supplier` | Supplier Catalog / Inwarding | Bulk merchandise vendor supplying raw inventory and packaged goods into Central Logistics Warehouses. |
| **Delivery Rider** | `driver` | Rider Mobile App / API | Dispatch rider assigned to deliver packed orders from local shops/hubs to customer doorsteps with live GPS tracking. |
| **Customer** | `customer` | Customer Storefront / App | Registered shopper browsing catalog, placing orders, using Janmitram Health Cards, and tracking shipments. |
| **Visitor** | `visitor` | Public Storefront | Unregistered guest browsing public products and landing pages. |

---

## 2. Key Differences: `root` vs. `admin`

```
                  ┌─────────────────────────────────────┐
                  │          ROOT (Super Admin)         │
                  │  Full Access + Infrastructure & MLM │
                  └──────────────────┬──────────────────┘
                                     │ creates & manages
                                     ▼
                  ┌─────────────────────────────────────┐
                  │         ADMIN (Sub-Admin)           │
                  │    Operational & Day-to-Day Tasks   │
                  └─────────────────────────────────────┘
```

| Dimension | `root` (Super Admin) | `admin` (System Administrator) |
|---|---|---|
| **Hierarchy** | Absolute highest system authority (`root@janmitram.com`). | Subordinate role created and delegated by `root`. |
| **Role ID** | Role ID `#1` (protected from deletion/modification). | Role ID `#2` or custom admin roles. |
| **Platform Anchor** | Owns the **Main Janmitram Shop** (`JAN-00001` / Shop #1), serving as the apex parent node in the MLM genealogy. | Does not own the platform apex node; acts as operational staff. |
| **Infrastructure Controls** | Exclusive access to Payment Gateway API keys, SMS Gateways, Mail SMTP, Firebase Push, PWA, and system upgrades. | Restricted from core infrastructure settings unless explicitly granted permissions. |
| **MLM Compensation** | Full authority to run, preview, and rollback monthly Dual-Phase Payout runs (`/admin/payout/run`). | Audits ledgers and downline trees without engine execution authority. |
| **Financial Disbursals** | Final approval and execution of bank payout withdrawals (`/admin/withdraw`). | Reviews request receipts and KYC verification. |

---

## 3. Granular Permissions Architecture (`config/acl.php`)

The application contains **330 granular permission nodes** managed via Spatie Laravel-Permission:

### **A. Admin Scope Permissions (Platform Level)**

| Module Group | Available Permission Nodes |
|---|---|
| **Dashboard** | `admin.dashboard.index`, `admin.dashboard.notification` |
| **Shop Management** | `admin.shop.index`, `admin.shop.create`, `admin.shop.edit`, `admin.shop.show`, `admin.shop.status.toggle`, `admin.shop.orders`, `admin.shop.products`, `admin.shop.reset.password` |
| **Central Warehouses** | `admin.warehouse.index`, `admin.warehouse.create`, `admin.warehouse.edit`, `admin.warehouse.destroy`, `admin.warehouse.show`, `admin.warehouse.stock`, `admin.warehouse.stock.add` |
| **Warehouse Transfers** | `admin.warehouse-transfer.index`, `admin.warehouse-transfer.create`, `admin.warehouse-transfer.store`, `admin.warehouse-transfer.show`, `admin.warehouse-transfer.complete`, `admin.warehouse-transfer.cancel` |
| **Stock Requests** | `admin.stock-request.index`, `admin.stock-request.show`, `admin.stock-request.approve`, `admin.stock-request.reject` |
| **MLM Payout Engine** | `admin.payout.index`, `admin.payout.run`, `admin.payout.network`, `admin.payout.guide`, `admin.payout.slip` |
| **Withdrawal Disbursals** | `admin.withdraw.index`, `admin.withdraw.update`, `admin.withdraw.show` |
| **Order Processing** | `admin.order.index`, `admin.order.show`, `admin.order.status.change`, `admin.order.payment.status.toggle`, `admin.order.assign.rider` |
| **Customer Reviews** | `admin.review.index`, `admin.review.approve`, `admin.review.reject`, `admin.review.reply`, `admin.review.destroy` |
| **Product & Catalog** | `admin.product.index`, `admin.product.approve`, `admin.product.show`, `admin.product.destroy`, `admin.category.*`, `admin.subcategory.*`, `admin.brand.*`, `admin.unit.*`, `admin.size.*`, `admin.color.*` |
| **Customer & Riders** | `admin.customer.index`, `admin.customer.create`, `admin.customer.show`, `admin.customer.edit`, `admin.customer.destroy`, `admin.customer.toggle`, `admin.customer.reset.password`, `admin.rider.*` |
| **Health Cards & Coupons** | `admin.coupon.index`, `admin.coupon.create`, `admin.coupon.edit`, `admin.coupon.destroy` |
| **System Settings** | `admin.generale-setting.*`, `admin.business-setting.*`, `admin.paymentGateway.*`, `admin.sms-gateway.*`, `admin.mailConfig.*`, `admin.firebase.*`, `admin.vatTax.*`, `admin.deliveryCharge.*` |
| **RBAC Governance** | `admin.role.index`, `admin.role.create`, `admin.role.edit`, `admin.role.destroy`, `admin.role.permission`, `admin.employee.*` |
| **Support & Tickets** | `admin.supportTicket.index`, `admin.supportTicket.show`, `admin.supportTicket.sendMessage`, `admin.supportTicket.updateStatus`, `admin.supportTicket.pinMessage` |

---

### **B. Shop Scope Permissions (Franchise Level)**

For custom shop staff and store manager roles (`is_shop = true`):

* **POS Billing**: `shop.pos.index`, `shop.pos.sales`, `shop.pos.draft`
* **Local Products**: `shop.product.index`, `shop.product.create`, `shop.product.show`, `shop.product.edit`, `shop.product.toggle`, `shop.product.destroy`, `shop.product.barcode`
* **Stock Requests**: `shop.stock-request.index`, `shop.stock-request.create`, `shop.stock-request.store`, `shop.stock-request.show`
* **MLM Downline Network**: `shop.payout.index`, `shop.payout.network`, `shop.payout.network.create`, `shop.payout.slip`
* **Wallet Withdrawals**: `shop.withdraw.index`, `shop.withdraw.store`, `shop.withdraw.show`
* **Store Orders**: `shop.order.index`, `shop.order.show`, `shop.order.status.change`
* **Shop Profile & KYC**: `shop.profile.index`, `shop.profile.edit`, `shop.profile.change-password`

---

## 4. Role & Permission Management Route

* **Interface URL**: `https://janmitram.com/admin/role/{role}/permission`
* **Controller**: `App\Http\Controllers\Admin\RolePermissionController`
* **Update Method**: `syncPermissions($request->permissions)` with automatic cache clearance (`role_permissions_{id}`).

---

## 5. Recent System Policy & Business Rule Updates (2026-08-20)

* **Shop Commission Models**: Direct / Zero-Fee models bypass commission wallet debits while retaining full transaction records.
* **Review Moderation**: Customer product reviews start in `pending` (`is_active = 0`) guarded by `ActiveScope` until reviewed under `admin.review.approve`.
* **Direct Downline Capacity**: Enforced programmatically (10 direct downline partners maximum per standard franchise shop).
* **First Stock Dispatch Minimum Threshold**: Enforced at ₹3,000 minimum aggregate value for initial shop stock transfer.
