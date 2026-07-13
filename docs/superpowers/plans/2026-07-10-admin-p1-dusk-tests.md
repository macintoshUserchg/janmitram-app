# Phase 2: Admin P1 Features — Dusk Test Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development to implement task-by-task.

**Goal:** Create 5 Dusk test files covering admin settings, banners/ads, coupons/flash sales, blog/pages, and footer/menu.

**Architecture:** Same patterns as Phase 1 — `Helpers` trait, `$this->admin()`, cleanup via `$cleanupIds`, live MySQL DB.

**Tech Stack:** Laravel Dusk 8.x, ChromeDriver + Google Chrome, PHPUnit 11

## Global Constraints

- All tests use `$this->admin()` from `Helpers` trait for auth
- All tests clean up created data in `tearDown()` via `$cleanupIds`
- ChromeDriver must be running: `vendor/laravel/dusk/bin/chromedriver-mac-arm --port=9515 &`
- Settings forms use `@method('PUT')` — Dusk handles this
- Forms with `enctype="multipart/form-data"` need `attach('field', $this->fakeImage())`
- Quill.js rich text: use `$this->fillQuill()` helper
- Select2: use `$this->selectByValue()` helper

---

### Task 9: AdminSettingsTest

**Files:**
- Create: `tests/Browser/AdminSettingsTest.php`

**Tests:**
1. General setting page loads and has form fields (name, email, address)
2. Business setup page loads with commission/shop settings
3. Verification/OTP settings page loads and has toggles

All read-only — just verify pages render. Settings updates would change live config.

---

### Task 10: AdminBannerAdTest

**Files:**
- Create: `tests/Browser/AdminBannerAdTest.php`

**Tests:**
1. Create a banner (title text + banner image upload)
2. Banner list shows new banner
3. Create an ad (title + image + link)
4. Toggle banner status
5. Cleanup test data

Key: banners need `title`, `banner` (file), ads need `title`, `link`, and `banner` (file).

---

### Task 11: AdminCouponFlashSaleTest

**Files:**
- Create: `tests/Browser/AdminCouponFlashSaleTest.php`

**Tests:**
1. Create a coupon (code, discount, dates)
2. Coupon list shows new coupon
3. Create a flash sale (name, discount %, dates, thumbnail)
4. Flash sale detail page loads
5. Toggle coupon status
6. Cleanup test data

Key: coupons need at minimum `code`, `discount`, `discount_type`, `start_date`, `start_time`, `expired_date`, `expired_time`, `min_order_amount`. Flash sales need `name`, `discount`, `start_date`, `end_date`, `thumbnail`.

---

### Task 12: AdminBlogPageTest

**Files:**
- Create: `tests/Browser/AdminBlogPageTest.php`

**Tests:**
1. Create a blog post (title, category, thumbnail, description via Quill)
2. Blog list shows new post
3. Create a page (name, content via Quill)
4. Page list shows new page
5. Toggle blog status
6. Cleanup

Key: blogs need `title`, `category` (select2), `thumbnail` (file), `description` (Quill.js). Pages need `name` and `content` (Quill.js).

---

### Task 13: AdminFooterMenuTest

**Files:**
- Create: `tests/Browser/AdminFooterMenuTest.php`

**Tests:**
1. Footer page loads with section list
2. Menu page loads with menu items
3. Create a menu item via the create form
4. Cleanup

Key: Footer uses SortableJS drag-and-drop (hard to test via Dusk — just verify page loads). Menu items are standard form CRUD.
