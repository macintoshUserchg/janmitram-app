# Phase 3: Admin P2 Features — Dusk Test Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development to implement task-by-task.

**Goal:** Create 4 Dusk test files covering admin P2 features — support tickets, subscriptions, employee/role, delivery/area/country.

**Architecture:** Same patterns as Phases 1-2 — `Helpers` trait, `$this->admin()`, cleanup via `$cleanupIds`, live MySQL DB.

## Global Constraints

- All tests use `$this->admin()` from `Helpers` trait for auth
- All tests clean up created data in `tearDown()` via `$cleanupIds`
- ChromeDriver must be running on port 9515
- Toggle routes use GET with `back()` — visit index first to set referrer
- Forms with `enctype="multipart/form-data"` need `attach('field', $this->fakeImage())`

---

### Task 14: AdminDeliveryAreaTest

**Files:**
- Create: `tests/Browser/AdminDeliveryAreaTest.php`

**Tests:**
1. Delivery charge list page loads (`/admin/delivery-charge`)
2. Delivery charge create (min/max qty + charge)
3. Area list page loads (`/admin/area`)
4. Area create (name + delivery_amount)
5. Country list page loads (`/admin/country`)
6. Country create (name + code + symbol)
7. Cleanup

Key: all three are simple standard forms. Country may need `name`, `code`, `symbol`.

---

### Task 15: AdminEmployeeRoleTest

**Files:**
- Create: `tests/Browser/AdminEmployeeRoleTest.php`

**Tests:**
1. Role list page loads (`/admin/role`)
2. Employee list page loads (`/admin/employee`)
3. Create an employee (name, email, phone, password, role select)
4. Cleanup

Key: employees are admin staff users. The create form has `name`, `email`, `phone`, `password`, `password_confirmation`, `role` (select with Spatie roles).

---

### Task 16: AdminSubscriptionTest

**Files:**
- Create: `tests/Browser/AdminSubscriptionTest.php`

**Tests:**
1. Subscription plan list page loads (`/admin/subscription-plan`)
2. Create a subscription plan (name, price, duration, sale_limit)
3. Subscription list page loads (shows shops with subscriptions)
4. Toggle plan status
5. Cleanup

Key: Subscription plan form has `name`, `price`, `duration` (days), `sale_limit` (number of sales allowed).

---

### Task 17: AdminSupportTicketTest

**Files:**
- Create: `tests/Browser/AdminSupportTicketTest.php`

**Tests:**
1. Support ticket list page loads (`/admin/support-ticket`)
2. Ticket issue type list page loads (`/admin/ticket-issue-type`)
3. Create a ticket issue type
4. Cleanup

Key: Support tickets are customer-facing (usually created via customer form), not admin-created. Admin view is read-only. Ticket issue types are simple CRUD.

---

### Task 18: AdminVatTaxTest

**Files:**
- Create: `tests/Browser/AdminVatTaxTest.php`

**Tests:**
1. VAT/Tax list page loads (`/admin/vat-tax`)
2. Create a VAT/Tax entry (name + percentage)
3. Toggle status
4. Cleanup

Key: VAT/Tax uses modal-based CRUD (like Brand/Color/Size). Simple form with `name` and `percentage` fields.
