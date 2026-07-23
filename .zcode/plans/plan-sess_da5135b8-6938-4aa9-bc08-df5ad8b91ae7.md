## Plan: `app:truncate-data` Artisan Command

### Goal
Clear all business/transactional data from the database while preserving root + admin users and all reference/configuration data.

### What will be preserved
- **System**: `migrations`, `failed_jobs`
- **RBAC**: `roles`, `permissions`, `role_has_permissions`, `model_has_roles`, `model_has_permissions`
- **Reference/Config**: `currencies`, `generate_settings`, `legal_pages`, `payment_gateways`, `social_links`, `theme_colors`, `social_auths`, `verify_manages`, `pages`, `menus`, `countries`, `footers`, `footer_items`, `languages`, `contact_uses`, `module_settings`, `google_re_captchas`, `s_m_s_configs`, `delivery_charges`, `ticket_issue_types`, `vat_taxes`, `areas`
- **Media**: `media` (images referenced by settings, gateways, etc.)
- **Users**: root + admin only (others deleted)

### What will be truncated (all business/transactional data)
`customers`, `shops`, `shop_categories`, `categories`, `category_subcategories`, `sub_categories`, `brands`, `products`, `product_categories`, `product_colors`, `product_sizes`, `product_units`, `product_thumbnails`, `product_attachments`, `product_translations`, `product_vat_taxes`, `product_licenses`, `product_subcategories`, `colors`, `sizes`, `units`, `orders`, `order_products`, `order_vat_taxes`, `return_orders`, `return_order_details`, `carts`, `pos_carts`, `pos_cart_products`, `reviews`, `favorites`, `coupons`, `coupon_collects`, `admin_coupons`, `banners`, `wallets`, `transactions`, `withdraws`, `drivers`, `driver_orders`, `driver_locations`, `device_keys`, `notifications`, `recent_views`, `flash_sales`, `flash_sale_products`, `ads`, `galleries`, `support_tickets`, `support_ticket_messages`, `support_ticket_attachments`, `blogs`, `blog_tags`, `blog_views`, `payments`, `paypal_payments`, `order_payments`, `translate_utilities`, `user_non_permissions`, `personal_access_tokens`, `verify_otps`, `shop_subscriptions`, `shop_user`, `shop_user_chats`, `addresses`, `cart_access_tokens`

### Implementation

**File: `app/Console/Commands/TruncateData.php`**

The command (`app:truncate-data`) will:

1. **Display a prominent warning** and ask for confirmation (yes/no)
2. **Get all table names** from `SHOW TABLES`
3. **Disable foreign key checks** (`SET FOREIGN_KEY_CHECKS = 0`)
4. **Truncate all business tables** using the explicit list above
5. **Delete extra users**: `User::whereNotIn('email', ['root@readyecommerce.com', 'admin@readyecommerce.com'])->delete()` — Spatie cascades will clean `model_has_roles` for these users via Eloquent events
6. **Clean orphaned model_has_roles**: Delete any `model_has_roles` entries where the `model_id` doesn't exist in `users.id` (belt-and-suspenders)
7. **Re-enable foreign key checks**
8. **Report summary**: tables truncated, users kept vs deleted

### Why this approach

- **Simple and verifiable** — one file, one command, no migrations
- **Fast** — uses raw SQL truncation (DDL, not row-by-row DELETE)
- **Safe** — FK checks disabled only during the truncation block, re-enabled immediately
- **Explicit** — preserve list is small, truncate list is comprehensive
- **No seeding needed afterward** — all reference data stays intact
- **Self-contained** — no changes to seeders, migrations, or models

### Files to create
- `app/Console/Commands/TruncateData.php`

### Files to modify
- None (the command is standalone)

### Verification
1. `php artisan app:truncate-data` runs and reports success
2. Login with root@readyecommerce.com / secret works
3. Business tables (orders, products, etc.) are empty
4. Reference tables (roles, currencies, settings, etc.) still have their data

### Edge cases
- If root/admin users don't exist yet (fresh DB), the command warns and exits
- Spatie `model_has_roles` cleanup via Eloquent deletion events (Spatie `HasRoles` trait handles this)
