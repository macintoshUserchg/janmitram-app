# Janmitram Core Architecture & Strategic Analysis (`prime_analysis.md`)

This document captures the strategic architecture, domain models, and key milestones implemented in the Janmitram multi-vendor e-commerce platform.

---

## 1. Core Architecture Pillars

### A. Strict Option A Warehouse Stock Management
* **Central Master Catalog**: All physical merchandise is created centrally by Admins as Master Products (`master_product_id = null`).
* **Warehouse Stock Ledgers**: Inventory deposits enter physical Central/Regional warehouses (`WarehouseStock`). All movements are immutably audited in `StockLedger`.
* **Shop Copy Replication**: Franchise shops (`Shop`) do not create physical products from scratch. Products are cloned (`master_product_id = X`, `shop_id = Y`) upon stock transfer/request fulfillment.
* **₹3,000 Minimum First Dispatch**: Newly registered or approved franchise shops must receive an initial inventory assignment of at least ₹3,000 aggregate value.

### B. Automated Zero-Permission IP Geolocation
* **Zero-Permission Detection**: `LocationController` inspects client IP headers (`CF-Connecting-IP`, `X-Real-IP`, `X-Forwarded-For`) to resolve the customer's City, State, and PIN code on startup.
* **Non-Indian / International Fallback**: International visitors (`countryCode !== 'IN'`) gracefully default to the Central Hub in Jaipur (`302013`).
* **Seamless Override**: Customers can switch cities or type a 6-digit PIN code via `LocationPickerModal.vue`.

### C. Multi-Shop Round-Robin Product Discovery
* **Fair Branch Exposure**: In multi-branch cities, the **Popular Products** section picks 1 top-rated product from each local branch shop in turn (Shop A ➔ item 1, Shop B ➔ item 2, etc.).
* **Zero Redundancy**: Automatic deduplication ensures every product name appears only once across the section.
* **Strict Central Shop Exclusion**: Products from **Main Janmitram Shop (Shop ID: 1)** are strictly excluded from homepage discovery, driving retail orders directly to local franchise branches.

### D. Dual-Phase MLM Network Marketing Engine
* **Genealogy Structure**: Franchise shops maintain parent-child links (`shops.parent_shop_id`) via referral codes.
* **Frontline Capacity**: Standard partner shops can sponsor a maximum of 10 direct downline shops (unlimited for Shop #1).
* **Compensation Phases**:
  * **Phase 1**: 10% direct commission on personal shop sales.
  * **Phase 2**: Tiered group sales bonuses based on active downline team performance.
* **Monthly Batch Execution**: Executed on or after the 1st of each month via `php artisan payout:monthly`.

### E. Google Maps Platform & Doorstep Geocoding
* **Google Maps JavaScript SDK & Google Places API (New)**: Multi-tier coordinate lookup, doorstep reverse geocoding, and live driver GPS tracking.

### F. Enterprise Corporate Invoicing & Razorpay Streamlining
* **Unified PDF Architecture**: Standardized 12mm page margin boundary in mPDF with itemized GST percentages, product units in bold, and dynamic QR codes.
* **Razorpay Optimization**: Auto-selects Razorpay when sole active online gateway and synchronizes popup window lifecycle via `window.postMessage` and `localStorage`.

---

_Last updated: 2026-08-20. Architecture verified against the codebase and live production environment._
