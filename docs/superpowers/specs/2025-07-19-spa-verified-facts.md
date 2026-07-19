# Verified SPA Facts for Test Generation (July 2025)

> **Source**: Live exploration via Chrome DevTools against `http://localhost:8888/janmitram-app/`

---

## Customer SPA Architecture

**The customer shop is a Vue 3 SPA served from `/`**. All customer interactions happen inside the SPA shell:
- **Root `/`** → loads SPA (`resources/views/app.blade.php` → Vue `#app`)
- **Client-side routing** (Vue Router) handles: `/products`, `/products/:id/details`, `/cart`, `/checkout`, `/dashboard`, `/wishlist`, `/address`, `/login` (modal), `/register` (modal)
- **Server routes at `web.php` line 87 and inside `shop/*`** → serve **Blade views** (shop-owner/admin pages) — NOT the SPA
- **All cart/checkout logic** → `api/cart/*` (CartController API)

**⚠️ Critical**: Direct URL visits to `/products/{id}/details`, `/cart`, `/checkout`, `/login`, `/register` hit server routes first and render Blade pages. The SPA routes are **only reachable via client-side navigation** after loading `/`.

---

## Customer Auth Flow (Verified)

| Action | What happens |
|---|---|
| **Guest visits `/`** | SPA loads, "Login" button in header, "Buy Now" on product cards |
| **Guest clicks "Buy Now"** | Alert toast: "Please login first!" + **Login Modal opens** |
| **Login Modal fields** | "Email / Phone Number" (textbox), "Password" (textbox), "Log in" button |
| **Seeded credentials** | `user@readyecommerce.com` / `secret` (customer role) |
| **After login** | Modal closes, "Buy Now" likely navigates to checkout or SPA product detail |

**No server routes**: `/login`, `/register`, `/password/reset` — customer auth is 100% SPA + API (`POST /api/login`, `POST /api/register`).

---

## Product Detail Page

| Path | What renders |
|---|---|
| **Direct visit `/products/{id}/details`** | Server Blade page `shop.product.show` (shop-owner preview) — disabled size/color radios, "View Live" link, NO "Add to Cart" |
| **SPA client-side navigation** (after loading `/`) | Vue `ProductDetails` page (`resources/js/pages/ProductDetails.vue`) — has working "Add to Cart", "Buy Now" |
| **Reaching SPA product detail** | 1. Visit `/` → 2. Click product card → Vue Router navigates client-side → 3. SPA renders `ProductDetails.vue` |

---

## Cart / Checkout Flow (Verified)

| Step | API / Action |
|---|---|
| **Add to Cart** | `POST /api/cart/store` → body: `{ product_id, quantity, is_buy_now }` → response: `"product added to cart"` |
| **View Cart** | `GET /api/cart` → returns `{ total, cart_items: shop_wise_products, info }` |
| **Increment/Decrement** | `POST /api/cart/increment` / `POST /api/cart/decrement` |
| **Remove** | `DELETE /api/cart/destroy` |
| **Checkout** | `POST /api/cart/checkout` → body: `{ shop_ids[], coupon_code?, is_buy_now }` → returns checkout data |
| **Payment** | `GET /payment/{payment}/pay` → redirects to gateway |

**Cart persistence**: 
- Guest → uses `cartAccessToken` (UUID stored in `CartAccessToken` table)
- Authenticated customer → linked by `customer_id`

---

## Key Selectors / Text for Dusk Tests

### Home Page (`/`)
- **Login button**: `button:contains("Login")` → opens login modal
- **Product cards**: contain "Buy Now" button
- **Categories link**: `/products` (SPA route)
- **Cart count badge**: button with text "0" / "N"

### Login Modal (opens after clicking "Buy Now" as guest, or clicking header "Login")
- **Email field**: `input[name="email"]` / placeholder "Enter email or phone number"
- **Password field**: `input[name="password"]` / placeholder "Enter Password"
- **Submit**: button "Log in"
- **Default creds button**: "Use default credentials Email: user@readyecommerce.com Password: secret"
- **Toast on success/failure**: assertive live region

### Product Detail (SPA, reached via client-side nav from home)
- **Add to Cart**: button "Add to Cart"
- **Buy Now**: button "Buy Now" 
- **Quantity**: +/- buttons, input
- **Size/Color**: radio buttons (enabled, not disabled like server page)

### Cart Page (SPA route `/cart`)
- **Items**: grouped by shop
- **Quantity +/-**: per item
- **Proceed to Checkout**: button
- **Coupon input**: field + "Apply"

### Checkout Page (SPA route `/checkout`)
- **Address form**: fields or existing addresses
- **Payment methods**: Razorpay, Stripe, PayPal, etc.
- **Place Order**: button

### Dashboard (SPA route `/dashboard`, requires auth)
- **Tabs**: My Orders, Wishlist, Addresses, Profile, Change Password
- **Orders**: list with status badges
- **Wishlist**: heart icons, "Move to Cart"

---

## Seeded Test Data

| Entity | Credentials / Notes |
|---|---|
| **Customer (seeded)** | `user@readyecommerce.com` / `secret` — has `customer` role, linked `Customer` record |
| **Admin (seeded)** | `root@readyecommerce.com` / `secret` — has `root` role |
| **Product #1** | Exists, active, approved — "et" (Nike), ₹65.61, quantity 79 |
| **Shops** | "Demo Shop", "My Shop" — seeded |

---

## Dusk Test Patterns (Corrected)

### ❌ Don't do this (what agent generated)
```php
$browser->visit("/products/{$product->id}/details")
    ->waitForText('Add to Cart')
    ->press('Add to Cart');
```

### ✅ Do this instead (real SPA flow)
```php
// Option A: Navigate through SPA (click product card on home)
$browser->visit('/')
    ->waitForText('Buy Now')        // home page loaded
    ->click('button:contains("Buy Now")') // first product card
    ->waitForText('Please login first!')  // modal opens
    ->type('input[name="email"]', 'user@readyecommerce.com')
    ->type('input[name="password"]', 'secret')
    ->press('Log in')
    ->waitForText('Add to Cart')    // SPA product detail loaded after login
    ->press('Add to Cart')
    ->waitForText('product added to cart');

// Option B: Pre-login then visit product
$browser->loginAs($customer)
    ->visit("/products/{$product->id}/details"); // Still hits server page!

// Option C: API-first (most reliable for cart/checkout)
$browser->loginAs($customer)
    ->script(['fetch("/api/cart/store", {method:"POST", body: JSON.stringify({product_id: 1, quantity: 1}), headers: {"Content-Type": "application/json"}})'])
    ->visit('/cart')
    ->waitForText($product->name);
```

---

## Shop Owner Flow (Verified)

| Route | What renders |
|---|---|
| `/shop/login` | Blade login page |
| `/shop/dashboard` | Blade dashboard (stats, recent orders) |
| `/shop/product` | Blade product list |
| `/shop/product/create` | Blade create form |
| `/shop/order` | Blade order list |

Uses `ShopAuthenticate` middleware. Test pattern: `Shop::factory()->create()`, `User::factory()->create(['shop_id' => $shop->id])->assignRole('shop')`, then `$browser->loginAs($user)->visit('/shop/dashboard')`.

---

## Admin Flow (Existing, Verified)

| Route | What renders |
|---|---|
| `/admin/login` | Blade login page |
| `/admin/*` | Blade admin pages |

---

## Summary of Required Test Changes

### Customer Tests (7 files, 34 tests)
| File | Key Fixes |
|---|---|
| `CartCheckoutTest` | Use SPA navigation: visit `/` → click "Buy Now" → login modal → "Add to Cart" → `/cart` → `/checkout` |
| `AuthFlowTest` | Login via modal (not `/login` page), register via modal, seeded creds |
| `UserDashboardTest` | Login via modal, then visit `/dashboard` (SPA route) |
| `WishlistTest` | Login via modal, click heart icon on product card |
| `AddressManagementTest` | Login, visit `/manage-address` (SPA) |
| `MultiVendorCartTest` | Login, add products from different shops on `/products` or home, verify grouping |
| `CouponFlashSaleTest` | Login, add to cart, apply coupon at `/checkout` |

### Shop Owner Tests (3 files, 15 tests) — mostly correct
- Keep current approach (Blade pages), but verify selectors match current views

### Admin Extended Tests (2 files) — mostly correct  
- Keep current approach

---

## Wait Times

- **SPA initial load** (`/`): up to **15s** (Vue bundle + API fetches)
- **Client-side navigation**: up to **8s** (API call + Vue render)
- **Login modal**: appears **immediately** after "Buy Now" click
- **Toast/alerts**: wait **3-5s**
- **API responses**: `waitForText('product added to cart')` works well

**Recommendation**: Use `waitForText` with 10-15s timeout for first page loads; 5-8s for subsequent client-side nav.