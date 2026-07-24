<?php

use App\Http\Controllers\API\AddressController;
// Customer storefront API controllers
use App\Http\Controllers\API\Auth\AuthController;
use App\Http\Controllers\API\Auth\ForgotPasswordController;
use App\Http\Controllers\API\BannerController;
use App\Http\Controllers\API\BlogController;
use App\Http\Controllers\API\CartController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\ChatController;
use App\Http\Controllers\API\CountryController;
use App\Http\Controllers\API\CouponController;
use App\Http\Controllers\API\FlashSaleController;
use App\Http\Controllers\API\HomeController;
use App\Http\Controllers\API\LegalPageController;
use App\Http\Controllers\API\MasterController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\ReturnOrderController;
use App\Http\Controllers\API\ReviewController;
use App\Http\Controllers\API\Rider\LoginController as RiderLoginController;
use App\Http\Controllers\API\Rider\OrderController as RiderOrderController;
use App\Http\Controllers\API\Rider\UserController as RiderUserController;
use App\Http\Controllers\API\Seller\DashboardController as SellerDashboardController;
use App\Http\Controllers\API\Seller\LoginController as SellerLoginController;
use App\Http\Controllers\API\Seller\OrderController as SellerOrderController;
// Seller (vendor) app controllers
use App\Http\Controllers\API\Seller\ProductController as SellerProductController;
use App\Http\Controllers\API\Seller\UserController as SellerUserController;
use App\Http\Controllers\API\ShopController;
use App\Http\Controllers\API\SocialAuthController;
use App\Http\Controllers\API\SubCategoryController;
// Rider app controllers
use App\Http\Controllers\API\SupportController;
use App\Http\Controllers\API\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes  (security-cleanup re-scaffold)
|--------------------------------------------------------------------------
|
| Reconstructed because the original `routes/api.php` was not present in the
| distributed source. Paths below come from the customer Flutter app's
| documented endpoint contract (lib/config/app_constants.dart); handlers are
| bound to the API controllers that DO exist under app/Http/Controllers/API.
|
| VERIFY BEFORE PRODUCTION:
|   - HTTP verbs: kept GET for reads / POST for writes per the app's usage;
|     confirm each against the controller and the Flutter `dio` calls.
|   - Auth guard: protected routes use 'auth:sanctum' (laravel/sanctum is a
|     dependency). If the project issues tokens via a different guard, adjust.
|   - A few mappings are best-effort and tagged `// VERIFY`.
|
*/

/* ===================== CUSTOMER APP ===================== */

// ---- Public ----
Route::get('master', [MasterController::class, 'index']);            // app settings / master data
Route::get('home', [HomeController::class, 'index']);
Route::get('categories', [CategoryController::class, 'index']);
Route::get('sub-categories', [SubCategoryController::class, 'index']);
Route::get('products', [ProductController::class, 'index']);
Route::get('category-products', [ProductController::class, 'index']);  // VERIFY: category-filtered list
Route::get('product-details', [ProductController::class, 'show']);
Route::get('shops', [ShopController::class, 'index']);
Route::get('shop', [ShopController::class, 'show']);
Route::get('shops/{shop}', [ShopController::class, 'show']);
Route::get('shop-categories', [ShopController::class, 'shopCategory']);
Route::get('top-shops', [ShopController::class, 'topShops']);
Route::get('popular-products', [ShopController::class, 'popularProducts']);
Route::get('reviews', [ReviewController::class, 'index']);
Route::get('banners', [BannerController::class, 'index']);
Route::get('flash-sales', [FlashSaleController::class, 'index']);
Route::get('flash-sale', [FlashSaleController::class, 'show']);
Route::get('blogs', [BlogController::class, 'index']);
Route::get('blog', [BlogController::class, 'show']);
Route::get('legal-pages/{slug}', [LegalPageController::class, 'index']);
Route::get('contact-us', [LegalPageController::class, 'contactUs']);
Route::get('countries', [CountryController::class, 'index']);
Route::get('areas', [CountryController::class, 'indexAreas']);
Route::get('get-vouchers', [CouponController::class, 'index']);
Route::post('support', [SupportController::class, 'store']);
Route::post('contact-us', [SupportController::class, 'store']);        // VERIFY

// ---- Auth (public) ----
Route::post('login', [AuthController::class, 'login']);
Route::post('registration', [AuthController::class, 'register']);
Route::post('send-otp', [ForgotPasswordController::class, 'resendOTP']);
Route::post('verify-otp', [ForgotPasswordController::class, 'verifyOtp']);
Route::post('reset-password', [ForgotPasswordController::class, 'resetPassword']);
Route::post('social-login', [SocialAuthController::class, 'login']);
Route::post('social/token-exchange', [SocialAuthController::class, 'handleTokenExchange']);
Route::get('auth/callback', [AuthController::class, 'callback']);      // VERIFY: social callback

// ---- Protected (token required) ----
Route::middleware('auth:sanctum')->group(function () {

    Route::post('logout', [AuthController::class, 'logout']);

    // Profile
    Route::get('profile', [UserController::class, 'index']);
    Route::post('update-profile', [UserController::class, 'update']);
    Route::post('change-password', [UserController::class, 'changePassword']);
    Route::post('update-last-seen', [UserController::class, 'updateLastSeen']);
    Route::get('recently-views', [HomeController::class, 'recentlyViews']);

    // Favourites & reviews
    Route::post('favorite-add-or-remove', [ProductController::class, 'addFavorite']);
    Route::get('favorite-products', [ProductController::class, 'favoriteProducts']);
    Route::post('product-review', [ProductController::class, 'storeReview']);

    // Addresses
    Route::get('addresses', [AddressController::class, 'index']);
    Route::post('address/store', [AddressController::class, 'store']);
    Route::post('address/update', [AddressController::class, 'update']);     // VERIFY verb (PUT?)
    Route::delete('address', [AddressController::class, 'destroy']);

    // Cart
    Route::get('carts', [CartController::class, 'index']);
    Route::post('cart/store', [CartController::class, 'store']);
    Route::post('cart/increment', [CartController::class, 'increment']);
    Route::post('cart/decrement', [CartController::class, 'decrement']);
    Route::post('cart/checkout', [CartController::class, 'checkout']);
    Route::delete('cart', [CartController::class, 'destroy']);

    // Orders
    Route::get('orders', [OrderController::class, 'index']);
    Route::get('order-details', [OrderController::class, 'show']);
    Route::post('place-order', [OrderController::class, 'store']);
    Route::post('place-order/again', [OrderController::class, 'reOrder']);
    Route::post('orders/cancel', [OrderController::class, 'cancel']);
    Route::post('order-payment', [OrderController::class, 'payment']);

    // Vouchers
    Route::post('vouchers-collect', [CouponController::class, 'collectedVouchers']);
    Route::post('apply-voucher', [CouponController::class, 'applyVoucher']);
    Route::post('voucher/discount', [CouponController::class, 'getDiscount']);  // VERIFY

    // Chat
    Route::post('store-message', [ChatController::class, 'storeMessage']);
    Route::get('get-message', [ChatController::class, 'getMessage']);
    Route::post('send-message', [ChatController::class, 'sendMessage']);
    Route::get('get-shops', [ChatController::class, 'getShops']);
    Route::get('unread-messages', [ChatController::class, 'unreadMessages']);

    // Returns
    Route::post('return-order', [ReturnOrderController::class, 'store']);
    Route::get('return-history', [ReturnOrderController::class, 'index']);
    Route::get('return-orders', [ReturnOrderController::class, 'index']);
    Route::get('return-order-details', [ReturnOrderController::class, 'show']);
});

/* ===================== SELLER APP ===================== */
Route::prefix('seller')->group(function () {
    // Public auth
    Route::post('login', [SellerLoginController::class, 'login']);
    Route::post('register', [SellerLoginController::class, 'register']);
    Route::post('check-user-status', [SellerLoginController::class, 'checkUserStatus']);
    Route::post('send-otp', [SellerLoginController::class, 'sendOTP']);
    Route::post('verify-otp', [SellerLoginController::class, 'verifyOtp']);
    Route::post('forgot-password', [SellerLoginController::class, 'forgotPassword']);
    Route::post('check-email-phone', [SellerLoginController::class, 'checkEmailPhone']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [SellerLoginController::class, 'logout']);
        Route::post('change-password', [SellerLoginController::class, 'changePassword']);
        Route::delete('account', [SellerLoginController::class, 'deleteAccountSeller']);

        Route::get('dashboard', [SellerDashboardController::class, 'index']);
        Route::get('profile', [SellerUserController::class, 'show']);
        Route::post('update-profile', [SellerUserController::class, 'updateProfile']);
        Route::post('shop/update', [SellerUserController::class, 'shopUpdate']);
        Route::post('shop/setting-update', [SellerUserController::class, 'shopSettingUpdate']);

        Route::get('orders', [SellerOrderController::class, 'index']);            // VERIFY methods
        Route::get('products', [SellerProductController::class, 'index']);        // VERIFY methods
    });
});

/* ===================== RIDER APP ===================== */
Route::prefix('rider')->group(function () {
    // Public auth
    Route::post('login', [RiderLoginController::class, 'login']);
    Route::post('register', [RiderLoginController::class, 'register']);
    Route::post('check-user-status', [RiderLoginController::class, 'checkUserStatus']);
    Route::post('send-otp', [RiderLoginController::class, 'sendOTP']);
    Route::post('verify-otp', [RiderLoginController::class, 'verifyOtp']);
    Route::post('create-password', [RiderLoginController::class, 'createPassword']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [RiderLoginController::class, 'logout']);
        Route::post('change-password', [RiderLoginController::class, 'changePassword']);
        Route::delete('account', [RiderLoginController::class, 'deleteAccountRider']);

        Route::get('profile', [RiderUserController::class, 'show']);
        Route::post('update-profile', [RiderUserController::class, 'update']);
        Route::post('location-update', [RiderUserController::class, 'locationUpdate']);

        Route::get('orders', [RiderOrderController::class, 'index']);
        Route::get('order-details', [RiderOrderController::class, 'show']);
        Route::post('order/status-update', [RiderOrderController::class, 'statusUpdate']);
        Route::get('status-wise-orders', [RiderOrderController::class, 'statusWiseOrders']);
    });
});
