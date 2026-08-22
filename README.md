# Janmitram App

Modern multi-vendor e-commerce platform with strict Option A warehouse inventory management, zero-permission IP geolocation, Google Maps integration, dual-phase MLM network marketing engine, Razorpay payments, and a high-performance Vue 3 customer SPA.

---

## Key System Features

* **Normalized Single Catalog Architecture**: Single canonical global product catalog with centralized warehouse inventory deposits, immutable `StockLedger` auditing, and dedicated branch shop inventory tracking (`shop_inventories`).
* **Real-Time Global Price & Catalog Updates**: Instant reflection of product prices, offer discounts, gallery media, descriptions, and variant extra pricing across all franchise branches in 1 single database update.
* **Automated IP-First Geolocation**: Zero-permission customer City/PIN detection via IP geolocation, with graceful fallback to Central Hub Jaipur (`302013`) for overseas/non-Indian visitors.
* **Smart Nearest-Shop Order Routing**: Smart distance-based order allocation directly routing to candidate branch shops holding active stock in `shop_inventories`.
* **Google Maps Platform**: Google Maps JavaScript SDK & Google Places API (New) with interactive draggable pins, doorstep reverse geocoding, and live driver GPS tracking.
* **Flexible Shop Business Models**: Support for both Commission-Based and Direct / None Zero-Fee models.
* **Streamlined Razorpay Integration**: Pre-selection of Razorpay when sole active online gateway; automatic popup window lifecycle synchronization.
* **Dual-Phase MLM Payout Engine**: Automated monthly payout calculations based strictly on Net Product Sales volume, with direct downline capacity enforcement (10 direct downlines max per standard partner shop).
* **Enterprise Corporate Invoicing & Statutory Tax Compliance**: GST calculated accurately on Net Taxable Base post-discount across checkout, POS, order management, and printable mPDF invoices with dynamic QR codes.

---

## Local Development Setup (MAMP)

### 1. Document Root Configuration
* **Document Root**: `/Applications/MAMP/htdocs/janmitram-app/public`
* **Local App URL**: `http://localhost:8888/` or `http://localhost:8888/janmitram-app/`

### 2. Environment Configuration
Copy `.env.example` to `.env` and verify database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=janmitram
DB_USERNAME=root
DB_PASSWORD=root
```

### 3. Database Migration & Caches
```bash
php artisan migrate
php artisan optimize:clear
php artisan view:cache
```

### 4. Frontend Asset Compilation
```bash
# Development hot-reload
npm run dev

# Production build
npm run build
```

---

## Testing & Code Formatting

```bash
# Run full automated PHPUnit test suite (174 tests / 646 assertions)
php artisan test --compact

# Run specific feature test suites
php artisan test --compact --filter=StockAssignmentFeatureTest
php artisan test --compact --filter=ShopAllocationTest
php artisan test --compact --filter=WarehouseTest
php artisan test --compact --filter=ProductCatalogDeduplicationTest
php artisan test --compact --filter=PayoutTest

# Format PHP code style
vendor/bin/pint --dirty --format agent
```

---

## Production Deployment

* **Live Domain**: [https://janmitram.com](https://janmitram.com)
* **Production Database**: `u939461333_app_janmitram`
* _Last updated: 2026-08-22._