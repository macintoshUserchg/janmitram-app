<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Gateway\PaymentGatewayController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| RECONSTRUCTION NOTE (security cleanup):
| The original `routes/web.php` was NOT present in the distributed source
| (it was withheld/served by the author's license server, and the malicious
| `DestroyTrait::remove()` kill-switch deleted any local copy). This file is
| a clean re-scaffold.
|
| The mobile/API surface is fully wired in `routes/api.php`.
|
*/

/* ===================== Customer-facing Vue SPA ===================== */
Route::get('/', function () {
    return view('app');
})->name('home');

/* ===================== Frontend translation files ===================== */
Route::get('lang/{locale}', function (string $locale) {
    $path = lang_path("{$locale}.json");
    if (! file_exists($path)) {
        abort(404);
    }
    return response()->file($path, [
        'Content-Type' => 'application/json',
    ]);
})->where('locale', '[a-z]{2}(_[A-Z]{2})?');

/* ===================== Payment gateway callbacks ===================== */
Route::controller(PaymentGatewayController::class)->group(function () {
    Route::match(['get', 'post'], 'payment/{payment}/pay', 'payment')->name('payment');
    Route::get('payment/success/{payment?}', 'success')->name('payment.success');
    Route::post('payment/success', 'success')->name('payment.success.post');
    Route::get('payment/cancel/{payment}', 'cancel')->name('payment.cancel');
    Route::get('order/payment/success/{payment}', 'paymentSuccess')->name('order.payment.success');
    Route::get('order/payment/cancel/{payment}', 'paymentCancel')->name('order.payment.cancel');
});

/* ===================== Admin auth (guest) ===================== */
Route::get('admin/login', [\App\Http\Controllers\Admin\Auth\LoginController::class, 'index'])->name('admin.login');
Route::post('admin/login', [\App\Http\Controllers\Admin\Auth\LoginController::class, 'login'])->name('admin.login.submit');

/* ===================== Standalone public stubs ===================== */
Route::get('shop/login', \App\Http\Controllers\Shop\Auth\LoginController::class . '@index')->name('shop.login');
Route::post('shop/login', \App\Http\Controllers\Shop\Auth\LoginController::class . '@login')->name('shop.login.submit');
Route::get('marketplace/addons', function () { return back()->with('warning', 'Marketplace removed'); })->name('marketplace.addons');
Route::get('marketplace', function () { return back()->with('warning', 'Marketplace removed'); })->name('marketplace.index');
Route::get('marketplace/upgrade', function () { return back()->with('warning', 'Marketplace removed'); })->name('marketplace.upgrade');
Route::get('storage/install', function () { return redirect('/'); })->name('storage.install.index');

/* ===================== Shop (seller panel) ===================== */
Route::prefix('shop')->name('shop.')->middleware(['web'])->group(function () {

    /* Guest */
    Route::get('login', [\App\Http\Controllers\Shop\Auth\LoginController::class, 'index'])->name('login');
    Route::post('login', [\App\Http\Controllers\Shop\Auth\LoginController::class, 'login'])->name('login.submit');
    Route::get('create', [\App\Http\Controllers\Shop\Auth\LoginController::class, 'create'])->name('create');
    Route::post('store', [\App\Http\Controllers\Shop\Auth\LoginController::class, 'store'])->name('store');
    Route::get('register', [\App\Http\Controllers\Shop\Auth\LoginController::class, 'create'])->name('register');
    Route::post('register-submit', [\App\Http\Controllers\Shop\Auth\LoginController::class, 'store'])->name('register.submit');

    /* Protected */
    Route::middleware(['authShop'])->group(function () {
        Route::post('logout', [\App\Http\Controllers\Shop\Auth\LoginController::class, 'logout'])->name('logout');

        Route::get('dashboard', [\App\Http\Controllers\Shop\DashboardController::class, 'index'])->name('dashboard.index');
        Route::get('dashboard/notification', [\App\Http\Controllers\Shop\NotificationController::class, 'index'])->name('dashboard.notification');
        Route::get('dashboard/statistics', [\App\Http\Controllers\Shop\DashboardController::class, 'orderStatistics'])->name('dashboard.statistics');

        Route::get('profile', [\App\Http\Controllers\Shop\ProfileController::class, 'index'])->name('profile.index');
        Route::get('profile/edit', [\App\Http\Controllers\Shop\ProfileController::class, 'edit'])->name('profile.edit');
        Route::post('profile/update', [\App\Http\Controllers\Shop\ProfileController::class, 'update'])->name('profile.update');
        Route::get('profile/change-password', [\App\Http\Controllers\Shop\ProfileController::class, 'changePassword'])->name('profile.change-password');
        Route::post('profile/update-password', [\App\Http\Controllers\Shop\ProfileController::class, 'updatePassword'])->name('profile.change-password.update');

        Route::get('product', [\App\Http\Controllers\Shop\ProductController::class, 'index'])->name('product.index');
        Route::get('product/create', [\App\Http\Controllers\Shop\ProductController::class, 'create'])->name('product.create');
        Route::post('product', [\App\Http\Controllers\Shop\ProductController::class, 'store'])->name('product.store');
        Route::get('product/{product}', [\App\Http\Controllers\Shop\ProductController::class, 'show'])->name('product.show');
        Route::get('product/{product}/edit', [\App\Http\Controllers\Shop\ProductController::class, 'edit'])->name('product.edit');
        Route::put('product/{product}', [\App\Http\Controllers\Shop\ProductController::class, 'update'])->name('product.update');
        Route::delete('product/{product}', [\App\Http\Controllers\Shop\ProductController::class, 'destroy'])->name('product.destroy');
        Route::get('product/{product}/toggle', [\App\Http\Controllers\Shop\ProductController::class, 'statusToggle'])->name('product.toggle');
        Route::get('product/barcode', [\App\Http\Controllers\Shop\ProductController::class, 'generateBarcode'])->name('product.barcode');
        Route::post('product/generate-ai', [\App\Http\Controllers\Shop\ProductController::class, 'generateAIData'])->name('product.generate.AI.data');
        Route::delete('product/{product}/thumbnail', [\App\Http\Controllers\Shop\ProductController::class, 'thumbnailDestroy'])->name('product.remove.thumbnail');
        Route::delete('product/{product}/attachment/{media}', [\App\Http\Controllers\Shop\ProductController::class, 'attachmentDestroy'])->name('product.remove.attachment');
        Route::delete('product/{product}/license/{productLicense}', [\App\Http\Controllers\Shop\ProductController::class, 'licenseDestroy'])->name('product.remove.license');
        Route::get('product/digital/create', [\App\Http\Controllers\Shop\ProductController::class, 'digitalProductCreate'])->name('digital.product.create');

        Route::get('voucher', [\App\Http\Controllers\Shop\VoucherController::class, 'index'])->name('voucher.index');
        Route::get('voucher/create', [\App\Http\Controllers\Shop\VoucherController::class, 'create'])->name('voucher.create');
        Route::post('voucher', [\App\Http\Controllers\Shop\VoucherController::class, 'store'])->name('voucher.store');
        Route::get('voucher/{voucher}/edit', [\App\Http\Controllers\Shop\VoucherController::class, 'edit'])->name('voucher.edit');
        Route::put('voucher/{voucher}', [\App\Http\Controllers\Shop\VoucherController::class, 'update'])->name('voucher.update');
        Route::delete('voucher/{voucher}', [\App\Http\Controllers\Shop\VoucherController::class, 'destroy'])->name('voucher.destroy');
        Route::get('voucher/{voucher}/toggle', [\App\Http\Controllers\Shop\VoucherController::class, 'statusToggle'])->name('voucher.toggle');

        Route::get('banner', [\App\Http\Controllers\Shop\BannerController::class, 'index'])->name('banner.index');
        Route::get('banner/create', [\App\Http\Controllers\Shop\BannerController::class, 'create'])->name('banner.create');
        Route::post('banner', [\App\Http\Controllers\Shop\BannerController::class, 'store'])->name('banner.store');
        Route::get('banner/{banner}/edit', [\App\Http\Controllers\Shop\BannerController::class, 'edit'])->name('banner.edit');
        Route::put('banner/{banner}', [\App\Http\Controllers\Shop\BannerController::class, 'update'])->name('banner.update');
        Route::get('banner/{banner}/toggle', [\App\Http\Controllers\Shop\BannerController::class, 'statusToggle'])->name('banner.toggle');
        Route::delete('banner/{banner}', [\App\Http\Controllers\Shop\BannerController::class, 'destroy'])->name('banner.destroy');

        Route::get('employee', [\App\Http\Controllers\Shop\EmployeeController::class, 'index'])->name('employee.index');
        Route::get('employee/create', [\App\Http\Controllers\Shop\EmployeeController::class, 'create'])->name('employee.create');
        Route::post('employee', [\App\Http\Controllers\Shop\EmployeeController::class, 'store'])->name('employee.store');
        Route::get('employee/{user}/permission', [\App\Http\Controllers\Shop\EmployeeController::class, 'permission'])->name('employee.permission');
        Route::put('employee/{user}/permission', [\App\Http\Controllers\Shop\EmployeeController::class, 'updatePermission'])->name('employee.permission.update');
        Route::post('employee/{user}/reset-password', [\App\Http\Controllers\Shop\EmployeeController::class, 'resetPassword'])->name('employee.reset-password');
        Route::delete('employee/{user}', [\App\Http\Controllers\Shop\EmployeeController::class, 'destroy'])->name('employee.destroy');

        Route::get('order', [\App\Http\Controllers\Shop\OrderController::class, 'index'])->name('order.index');
        Route::get('order/{order}', [\App\Http\Controllers\Shop\OrderController::class, 'show'])->name('order.show');
        Route::put('order/{order}/status', [\App\Http\Controllers\Shop\OrderController::class, 'statusChange'])->name('order.status.change');
        Route::put('order/{order}/payment-status-toggle', [\App\Http\Controllers\Shop\OrderController::class, 'paymentStatusToggle'])->name('order.payment.status.toggle');
        Route::get('order/{order}/invoice-download', [\App\Http\Controllers\Shop\OrderController::class, 'downloadInvoice'])->name('download-invoice');
        Route::get('order/{order}/payment-slip', [\App\Http\Controllers\Shop\OrderController::class, 'paymentSlip'])->name('payment-slip');

        Route::get('return-order', [\App\Http\Controllers\Shop\ReturnOrderController::class, 'index'])->name('returnOrder.index');
        Route::get('return-order/{returnOrder}', [\App\Http\Controllers\Shop\ReturnOrderController::class, 'show'])->name('returnOrder.show');
        Route::post('return-order/{returnOrder}/status', [\App\Http\Controllers\Shop\ReturnOrderController::class, 'statusChange'])->name('returnOrder.status.change');

        Route::get('notification/show', [\App\Http\Controllers\Shop\NotificationController::class, 'show'])->name('notification.show');
        Route::get('notification/read-all', [\App\Http\Controllers\Shop\NotificationController::class, 'markAllAsRead'])->name('notification.readAll');
        Route::get('notification/{notification}/read', [\App\Http\Controllers\Shop\NotificationController::class, 'markAsRead'])->name('notification.read');
        Route::delete('notification/{notification}', [\App\Http\Controllers\Shop\NotificationController::class, 'destroy'])->name('notification.destroy');

        Route::get('withdraw', [\App\Http\Controllers\Shop\WithdrawController::class, 'index'])->name('withdraw.index');
        Route::post('withdraw', [\App\Http\Controllers\Shop\WithdrawController::class, 'store'])->name('withdraw.store');
        Route::delete('withdraw/{withdraw}', [\App\Http\Controllers\Shop\WithdrawController::class, 'delete'])->name('withdraw.delete');

        Route::get('gallery', [\App\Http\Controllers\Shop\GalleryController::class, 'index'])->name('gallery.index');
        Route::get('gallery/create', [\App\Http\Controllers\Shop\GalleryController::class, 'create'])->name('gallery.create');
        Route::post('gallery', [\App\Http\Controllers\Shop\GalleryController::class, 'store'])->name('gallery.store');

        Route::match(['get','post'],'pos',[\App\Http\Controllers\Shop\POSController::class,'index'])->name('pos.index');
        Route::match(['get','post'],'pos/sales',[\App\Http\Controllers\Shop\POSController::class,'sales'])->name('pos.sales');
        Route::match(['get','post'],'pos/draft',[\App\Http\Controllers\Shop\POSController::class,'draft'])->name('pos.draft');
        Route::delete('pos/draft/{posCart}',[\App\Http\Controllers\Shop\POSController::class,'draftDelete'])->name('pos.draft.delete');
        Route::match(['get','post'],'pos/invoice',[\App\Http\Controllers\Shop\POSController::class,'invoice'])->name('pos.invoice');
        Route::post('pos/store-order',[\App\Http\Controllers\Shop\POSController::class,'storeOrder'])->name('submit-order');
        Route::post('pos/store-customer',[\App\Http\Controllers\Shop\POSController::class,'storeCustomer'])->name('pos.customerStore');
        Route::get('pos/product',[\App\Http\Controllers\Shop\POSController::class,'getProduct'])->name('pos.product');
        Route::post('pos/cart',[\App\Http\Controllers\Shop\POSController::class,'addToCart'])->name('pos.addToCart');
        Route::get('pos/cart',[\App\Http\Controllers\Shop\POSController::class,'getCart'])->name('pos.getCart');
        Route::put('pos/cart',[\App\Http\Controllers\Shop\POSController::class,'updateCart'])->name('pos.updateCart');
        Route::delete('pos/cart',[\App\Http\Controllers\Shop\POSController::class,'removeCart'])->name('pos.removeCart');
        Route::post('pos/coupon',[\App\Http\Controllers\Shop\POSController::class,'applyCoupon'])->name('pos.applyCoupon');
        Route::delete('pos/coupon',[\App\Http\Controllers\Shop\POSController::class,'removeCoupon'])->name('pos.removeCoupon');
        Route::get('pos/product-detail/{product}',[\App\Http\Controllers\Shop\POSController::class,'getProductDetail'])->name('pos.product.detail');

        Route::get('flash-sale',[\App\Http\Controllers\Shop\FlashSaleController::class,'index'])->name('flashSale.index');
        Route::get('flash-sale/{flashSale}',[\App\Http\Controllers\Shop\FlashSaleController::class,'show'])->name('flashSale.show');
        Route::put('flash-sale/{flashSale}',[\App\Http\Controllers\Shop\FlashSaleController::class,'update'])->name('flashSale.update');
        Route::post('flash-sale/{flashSale}/product',[\App\Http\Controllers\Shop\FlashSaleController::class,'productStore'])->name('flashSale.productStore');
        Route::delete('flash-sale/product/{flashSaleProduct}',[\App\Http\Controllers\Shop\FlashSaleController::class,'productRemove'])->name('flashSale.productRemove');

        Route::get('subscription',[\App\Http\Controllers\Shop\SubscriptionController::class,'index'])->name('subscription.index');
        Route::post('subscription/purchase',[\App\Http\Controllers\Shop\SubscriptionController::class,'purchase'])->name('subscription.purchase');
        Route::get('subscription/payment-success',[\App\Http\Controllers\Shop\SubscriptionController::class,'paymentSuccess'])->name('subscription.payment-success');
        Route::get('subscription/payment-cancel',[\App\Http\Controllers\Shop\SubscriptionController::class,'paymentCancel'])->name('subscription.payment-cancel');

        Route::get('bulk-product-export',[\App\Http\Controllers\Shop\BulkProductExportController::class,'index'])->name('bulk-product-export.index');
        Route::post('bulk-product-export/export',[\App\Http\Controllers\Shop\BulkProductExportController::class,'export'])->name('bulk-product-export.export');
        Route::post('bulk-product-export/demo',[\App\Http\Controllers\Shop\BulkProductExportController::class,'demoExport'])->name('bulk-product-export.demo');
        Route::get('bulk-product-import',[\App\Http\Controllers\Shop\BulkProductImportController::class,'index'])->name('bulk-product-import.index');
        Route::get('bulk-product-import/format',[\App\Http\Controllers\Shop\BulkProductImportController::class,'formatExport'])->name('bulk-product-import.formatExport');
        Route::get('bulk-product-import/export',[\App\Http\Controllers\Shop\BulkProductImportController::class,'export'])->name('bulk-product-import.export');
        Route::post('bulk-product-import',[\App\Http\Controllers\Shop\BulkProductImportController::class,'store'])->name('bulk-product-import.store');

        Route::get('brand',[\App\Http\Controllers\Shop\BrandController::class,'index'])->name('brand.index');
        Route::get('category',[\App\Http\Controllers\Shop\CategoryController::class,'index'])->name('category.index');
        Route::get('subcategory',[\App\Http\Controllers\Shop\SubCategoryController::class,'index'])->name('subcategory.index');
        Route::get('color',[\App\Http\Controllers\Shop\ColorController::class,'index'])->name('color.index');
        Route::get('size',[\App\Http\Controllers\Shop\SizeController::class,'index'])->name('size.index');
        Route::get('unit',[\App\Http\Controllers\Shop\UnitController::class,'index'])->name('unit.index');
        Route::get('customer/chat',[\App\Http\Controllers\Shop\CustomerMessageController::class,'index'])->name('customer.chat.index');
        Route::get('customer/chat/{chatUser}',[\App\Http\Controllers\Shop\CustomerMessageController::class,'fetchMessages'])->name('customer.chat.fetch');
    });
});

/* ===================== Admin panel (protected) ===================== */
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:root'])->group(function () {

    Route::get('dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('dashboard/notification', [\App\Http\Controllers\Admin\NotificationController::class, 'index'])->name('dashboard.notification');
    Route::get('dashboard/statistics', [\App\Http\Controllers\Admin\DashboardController::class, 'orderStatistics'])->name('dashboard.statistics');
    Route::post('logout', [\App\Http\Controllers\Admin\Auth\LoginController::class, 'logout'])->name('logout');

    Route::get('profile', [\App\Http\Controllers\Admin\ProfileController::class, 'index'])->name('profile.index');
    Route::get('profile/edit', [\App\Http\Controllers\Admin\ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('profile/update', [\App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('profile.update');
    Route::get('profile/change-password', [\App\Http\Controllers\Admin\ProfileController::class, 'changePassword'])->name('profile.change-password');
    Route::post('profile/update-password', [\App\Http\Controllers\Admin\ProfileController::class, 'updatePassword'])->name('profile.update-password');

    Route::get('role', [\App\Http\Controllers\Admin\RolePermissionController::class, 'index'])->name('role.index');
    Route::post('role', [\App\Http\Controllers\Admin\RolePermissionController::class, 'store'])->name('role.store');
    Route::put('role/{role}', [\App\Http\Controllers\Admin\RolePermissionController::class, 'update'])->name('role.update');
    Route::delete('role/{role}', [\App\Http\Controllers\Admin\RolePermissionController::class, 'destroy'])->name('role.destroy');
    Route::get('role/{role}/permission', [\App\Http\Controllers\Admin\RolePermissionController::class, 'rolePermission'])->name('role.permission');
    Route::put('role/{role}/permission', [\App\Http\Controllers\Admin\RolePermissionController::class, 'updateRolePermission'])->name('role.permission.update');

    Route::get('employee', [\App\Http\Controllers\Admin\EmployeeManageController::class, 'index'])->name('employee.index');
    Route::get('employee/create', [\App\Http\Controllers\Admin\EmployeeManageController::class, 'create'])->name('employee.create');
    Route::post('employee', [\App\Http\Controllers\Admin\EmployeeManageController::class, 'store'])->name('employee.store');
    Route::delete('employee/{user}', [\App\Http\Controllers\Admin\EmployeeManageController::class, 'destroy'])->name('employee.destroy');
    Route::get('employee/{user}/permission', [\App\Http\Controllers\Admin\EmployeeManageController::class, 'permission'])->name('employee.permission');
    Route::put('employee/{user}/permission', [\App\Http\Controllers\Admin\EmployeeManageController::class, 'updatePermission'])->name('employee.permission.update');

    Route::get('currency', [\App\Http\Controllers\Admin\CurrencyController::class, 'index'])->name('currency.index');
    Route::get('currency/create', [\App\Http\Controllers\Admin\CurrencyController::class, 'create'])->name('currency.create');
    Route::post('currency', [\App\Http\Controllers\Admin\CurrencyController::class, 'store'])->name('currency.store');
    Route::get('currency/{currency}/edit', [\App\Http\Controllers\Admin\CurrencyController::class, 'edit'])->name('currency.edit');
    Route::put('currency/{currency}', [\App\Http\Controllers\Admin\CurrencyController::class, 'update'])->name('currency.update');
    Route::delete('currency/{currency}', [\App\Http\Controllers\Admin\CurrencyController::class, 'destroy'])->name('currency.destroy');

    Route::get('language', [\App\Http\Controllers\Admin\LanguageController::class, 'index'])->name('language.index');
    Route::get('language/create', [\App\Http\Controllers\Admin\LanguageController::class, 'create'])->name('language.create');
    Route::post('language', [\App\Http\Controllers\Admin\LanguageController::class, 'store'])->name('language.store');
    Route::get('language/{language}/edit', [\App\Http\Controllers\Admin\LanguageController::class, 'edit'])->name('language.edit');
    Route::put('language/{language}', [\App\Http\Controllers\Admin\LanguageController::class, 'update'])->name('language.update');
    Route::delete('language/{langId}', [\App\Http\Controllers\Admin\LanguageController::class, 'delete'])->name('language.delete');
    Route::get('language/{langId}/export', [\App\Http\Controllers\Admin\LanguageController::class, 'export'])->name('language.export');
    Route::post('language/{langId}/import', [\App\Http\Controllers\Admin\LanguageController::class, 'import'])->name('language.import');
    Route::put('language/{language}/set-default', [\App\Http\Controllers\Admin\LanguageController::class, 'setDefault'])->name('language.setDefault');

    Route::get('theme-color', [\App\Http\Controllers\Admin\ThemeColorController::class, 'index'])->name('themeColor.index');
    Route::put('theme-color', [\App\Http\Controllers\Admin\ThemeColorController::class, 'update'])->name('themeColor.update');
    Route::post('theme-color/change', [\App\Http\Controllers\Admin\ThemeColorController::class, 'change'])->name('themeColor.change');

    Route::get('generale-setting', [\App\Http\Controllers\Admin\GeneraleSettingController::class, 'index'])->name('generale-setting.index');
    Route::put('generale-setting', [\App\Http\Controllers\Admin\GeneraleSettingController::class, 'update'])->name('generale-setting.update');
    Route::post('generale-setting/update-command', [\App\Http\Controllers\Admin\GeneraleSettingController::class, 'updateCommand'])->name('generale-setting.update.command');

    Route::get('ai-prompt', [\App\Http\Controllers\Admin\GeneraleSettingController::class, 'aiPromptIndex'])->name('aiPrompt.index');
    Route::post('ai-prompt', [\App\Http\Controllers\Admin\GeneraleSettingController::class, 'aiPromptUpdate'])->name('aiPrompt.update');
    Route::get('ai-prompt/configure', [\App\Http\Controllers\Admin\GeneraleSettingController::class, 'aiPromptConfigure'])->name('aiPrompt.configure');
    Route::post('ai-prompt/configure', [\App\Http\Controllers\Admin\GeneraleSettingController::class, 'aiPromptConfigureUpdate'])->name('aiPrompt.configure.update');

    Route::get('mail-config', [\App\Http\Controllers\Admin\MailConfigurationController::class, 'index'])->name('mailConfig.index');
    Route::put('mail-config', [\App\Http\Controllers\Admin\MailConfigurationController::class, 'update'])->name('mailConfig.update');
    Route::post('mail-config/send-test', [\App\Http\Controllers\Admin\MailConfigurationController::class, 'sendTestMail'])->name('mailConfig.sendTestMail');

    Route::get('pusher-config', [\App\Http\Controllers\Admin\PusherConfigController::class, 'index'])->name('pusher.index');
    Route::put('pusher-config', [\App\Http\Controllers\Admin\PusherConfigController::class, 'update'])->name('pusher.update');

    Route::get('firebase', [\App\Http\Controllers\Admin\FirebaseController::class, 'index'])->name('firebase.index');
    Route::put('firebase', [\App\Http\Controllers\Admin\FirebaseController::class, 'update'])->name('firebase.update');

    Route::get('google-recaptcha', [\App\Http\Controllers\Admin\GoogleReCaptchaController::class, 'index'])->name('googleReCaptcha.index');
    Route::put('google-recaptcha', [\App\Http\Controllers\Admin\GoogleReCaptchaController::class, 'update'])->name('googleReCaptcha.update');

    Route::get('payment-gateway', [\App\Http\Controllers\Admin\PaymentGatewayController::class, 'index'])->name('paymentGateway.index');
    Route::put('payment-gateway/{paymentGateway}', [\App\Http\Controllers\Admin\PaymentGatewayController::class, 'update'])->name('paymentGateway.update');
    Route::get('payment-gateway/{paymentGateway}/toggle', [\App\Http\Controllers\Admin\PaymentGatewayController::class, 'toggle'])->name('paymentGateway.toggle');

    Route::get('verification', [\App\Http\Controllers\Admin\VerifyManageController::class, 'index'])->name('verification.index');
    Route::put('verification', [\App\Http\Controllers\Admin\VerifyManageController::class, 'update'])->name('verification.update');

    Route::get('sms-gateway', [\App\Http\Controllers\Admin\SMSGatewaySetupController::class, 'index'])->name('sms-gateway.index');
    Route::put('sms-gateway', [\App\Http\Controllers\Admin\SMSGatewaySetupController::class, 'update'])->name('sms-gateway.update');

    Route::get('business-setting', [\App\Http\Controllers\Admin\BusinessSetupController::class, 'index'])->name('business-setting.index');
    Route::put('business-setting', [\App\Http\Controllers\Admin\BusinessSetupController::class, 'update'])->name('business-setting.update');
    Route::get('business-setting/shop', [\App\Http\Controllers\Admin\BusinessSetupController::class, 'shop'])->name('business-setting.shop');
    Route::post('business-setting/shop-update', [\App\Http\Controllers\Admin\BusinessSetupController::class, 'shopUpdate'])->name('business-setting.shop-update');
    Route::get('business-setting/withdraw', [\App\Http\Controllers\Admin\BusinessSetupController::class, 'withdraw'])->name('business-setting.withdraw');
    Route::put('business-setting/withdraw-update', [\App\Http\Controllers\Admin\BusinessSetupController::class, 'withdrawUpdate'])->name('business-setting.withdraw-update');
    Route::post('business-setting/toggle-pos', [\App\Http\Controllers\Admin\BusinessSetupController::class, 'togglePOS'])->name('business-setting.toggle-pos');
    Route::post('business-setting/toggle-register', [\App\Http\Controllers\Admin\BusinessSetupController::class, 'toggleRegister'])->name('business-setting.toggle-register');

    Route::get('subscription-plan', [\App\Http\Controllers\Admin\SubscriptionPlanController::class, 'index'])->name('subscription-plan.index');
    Route::get('subscription-plan/create', [\App\Http\Controllers\Admin\SubscriptionPlanController::class, 'create'])->name('subscription-plan.create');
    Route::post('subscription-plan', [\App\Http\Controllers\Admin\SubscriptionPlanController::class, 'store'])->name('subscription-plan.store');
    Route::get('subscription-plan/{subscriptionPlan}/edit', [\App\Http\Controllers\Admin\SubscriptionPlanController::class, 'edit'])->name('subscription-plan.edit');
    Route::put('subscription-plan/{subscriptionPlan}', [\App\Http\Controllers\Admin\SubscriptionPlanController::class, 'update'])->name('subscription-plan.update');
    Route::get('subscription-plan/{subscriptionPlan}/toggle', [\App\Http\Controllers\Admin\SubscriptionPlanController::class, 'statusToggle'])->name('subscription-plan.toggle');
    Route::delete('subscription-plan/{subscriptionPlan}', [\App\Http\Controllers\Admin\SubscriptionPlanController::class, 'destroy'])->name('subscription-plan.destroy');
    Route::get('subscription-plan/subscriptions', [\App\Http\Controllers\Admin\SubscriptionPlanController::class, 'subscriptionList'])->name('subscription-plan.subscription.list');
    Route::post('subscription-plan/{shopSubscription}/subscription-status', [\App\Http\Controllers\Admin\SubscriptionPlanController::class, 'subscriptionStatus'])->name('subscription-plan.subscription.status');

    Route::get('product', [\App\Http\Controllers\Admin\ProductController::class, 'index'])->name('product.index');
    Route::get('product/{product}', [\App\Http\Controllers\Admin\ProductController::class, 'show'])->name('product.show');
    Route::get('product/{product}/approve', [\App\Http\Controllers\Admin\ProductController::class, 'approve'])->name('product.approve');
    Route::delete('product/{product}', [\App\Http\Controllers\Admin\ProductController::class, 'destroy'])->name('product.destroy');

    Route::get('category', [\App\Http\Controllers\Admin\CategoryController::class, 'index'])->name('category.index');
    Route::get('category/create', [\App\Http\Controllers\Admin\CategoryController::class, 'create'])->name('category.create');
    Route::post('category', [\App\Http\Controllers\Admin\CategoryController::class, 'store'])->name('category.store');
    Route::get('category/{category}/edit', [\App\Http\Controllers\Admin\CategoryController::class, 'edit'])->name('category.edit');
    Route::put('category/{category}', [\App\Http\Controllers\Admin\CategoryController::class, 'update'])->name('category.update');
    Route::get('category/{category}/toggle', [\App\Http\Controllers\Admin\CategoryController::class, 'statusToggle'])->name('category.toggle');

    Route::get('subcategory', [\App\Http\Controllers\Admin\SubCategoryController::class, 'index'])->name('subcategory.index');
    Route::get('subcategory/create', [\App\Http\Controllers\Admin\SubCategoryController::class, 'create'])->name('subcategory.create');
    Route::post('subcategory', [\App\Http\Controllers\Admin\SubCategoryController::class, 'store'])->name('subcategory.store');
    Route::get('subcategory/{subCategory}/edit', [\App\Http\Controllers\Admin\SubCategoryController::class, 'edit'])->name('subcategory.edit');
    Route::put('subcategory/{subCategory}', [\App\Http\Controllers\Admin\SubCategoryController::class, 'update'])->name('subcategory.update');
    Route::get('subcategory/{subCategory}/toggle', [\App\Http\Controllers\Admin\SubCategoryController::class, 'statusToggle'])->name('subcategory.toggle');

    Route::get('brand', [\App\Http\Controllers\Admin\BrandController::class, 'index'])->name('brand.index');
    Route::post('brand', [\App\Http\Controllers\Admin\BrandController::class, 'store'])->name('brand.store');
    Route::put('brand/{brand}', [\App\Http\Controllers\Admin\BrandController::class, 'update'])->name('brand.update');
    Route::get('brand/{brand}/toggle', [\App\Http\Controllers\Admin\BrandController::class, 'statusToggle'])->name('brand.toggle');

    Route::get('color', [\App\Http\Controllers\Admin\ColorController::class, 'index'])->name('color.index');
    Route::post('color', [\App\Http\Controllers\Admin\ColorController::class, 'store'])->name('color.store');
    Route::put('color/{color}', [\App\Http\Controllers\Admin\ColorController::class, 'update'])->name('color.update');
    Route::get('color/{color}/toggle', [\App\Http\Controllers\Admin\ColorController::class, 'statusToggle'])->name('color.toggle');

    Route::get('size', [\App\Http\Controllers\Admin\SizeController::class, 'index'])->name('size.index');
    Route::post('size', [\App\Http\Controllers\Admin\SizeController::class, 'store'])->name('size.store');
    Route::put('size/{size}', [\App\Http\Controllers\Admin\SizeController::class, 'update'])->name('size.update');
    Route::get('size/{size}/toggle', [\App\Http\Controllers\Admin\SizeController::class, 'statusToggle'])->name('size.toggle');

    Route::get('unit', [\App\Http\Controllers\Admin\UnitController::class, 'index'])->name('unit.index');
    Route::post('unit', [\App\Http\Controllers\Admin\UnitController::class, 'store'])->name('unit.store');
    Route::put('unit/{unit}', [\App\Http\Controllers\Admin\UnitController::class, 'update'])->name('unit.update');
    Route::get('unit/{unit}/toggle', [\App\Http\Controllers\Admin\UnitController::class, 'statusToggle'])->name('unit.toggle');

    Route::get('area', [\App\Http\Controllers\Admin\AreaController::class, 'index'])->name('area.index');
    Route::post('area', [\App\Http\Controllers\Admin\AreaController::class, 'store'])->name('area.store');
    Route::put('area/{area}', [\App\Http\Controllers\Admin\AreaController::class, 'update'])->name('area.update');
    Route::delete('area/{area}', [\App\Http\Controllers\Admin\AreaController::class, 'destroy'])->name('area.destroy');
    Route::get('area/{area}/toggle', [\App\Http\Controllers\Admin\AreaController::class, 'toggle'])->name('area.toggle');

    Route::get('country', [\App\Http\Controllers\Admin\CountryController::class, 'index'])->name('country.index');
    Route::post('country', [\App\Http\Controllers\Admin\CountryController::class, 'store'])->name('country.store');
    Route::put('country/{country}', [\App\Http\Controllers\Admin\CountryController::class, 'update'])->name('country.update');
    Route::delete('country/{country}', [\App\Http\Controllers\Admin\CountryController::class, 'destroy'])->name('country.destroy');

    Route::get('banner', [\App\Http\Controllers\Admin\BannerController::class, 'index'])->name('banner.index');
    Route::get('banner/create', [\App\Http\Controllers\Admin\BannerController::class, 'create'])->name('banner.create');
    Route::post('banner', [\App\Http\Controllers\Admin\BannerController::class, 'store'])->name('banner.store');
    Route::get('banner/{banner}/edit', [\App\Http\Controllers\Admin\BannerController::class, 'edit'])->name('banner.edit');
    Route::put('banner/{banner}', [\App\Http\Controllers\Admin\BannerController::class, 'update'])->name('banner.update');
    Route::get('banner/{banner}/toggle', [\App\Http\Controllers\Admin\BannerController::class, 'statusToggle'])->name('banner.toggle');
    Route::delete('banner/{banner}', [\App\Http\Controllers\Admin\BannerController::class, 'destroy'])->name('banner.destroy');

    Route::get('ad', [\App\Http\Controllers\Admin\AdController::class, 'index'])->name('ad.index');
    Route::get('ad/create', [\App\Http\Controllers\Admin\AdController::class, 'create'])->name('ad.create');
    Route::post('ad', [\App\Http\Controllers\Admin\AdController::class, 'store'])->name('ad.store');
    Route::get('ad/{ad}/edit', [\App\Http\Controllers\Admin\AdController::class, 'edit'])->name('ad.edit');
    Route::put('ad/{ad}', [\App\Http\Controllers\Admin\AdController::class, 'update'])->name('ad.update');
    Route::get('ad/{ad}/toggle', [\App\Http\Controllers\Admin\AdController::class, 'statusToggle'])->name('ad.toggle');
    Route::delete('ad/{ad}', [\App\Http\Controllers\Admin\AdController::class, 'destroy'])->name('ad.destroy');

    Route::get('flash-sale', [\App\Http\Controllers\Admin\FlashSaleController::class, 'index'])->name('flashSale.index');
    Route::get('flash-sale/create', [\App\Http\Controllers\Admin\FlashSaleController::class, 'create'])->name('flashSale.create');
    Route::post('flash-sale', [\App\Http\Controllers\Admin\FlashSaleController::class, 'store'])->name('flashSale.store');
    Route::get('flash-sale/{flashSale}/edit', [\App\Http\Controllers\Admin\FlashSaleController::class, 'edit'])->name('flashSale.edit');
    Route::put('flash-sale/{flashSale}', [\App\Http\Controllers\Admin\FlashSaleController::class, 'update'])->name('flashSale.update');
    Route::get('flash-sale/{flashSale}/toggle', [\App\Http\Controllers\Admin\FlashSaleController::class, 'statusToggle'])->name('flashSale.toggle');
    Route::get('flash-sale/{flashSale}/product', [\App\Http\Controllers\Admin\FlashSaleController::class, 'show'])->name('flashSale.product');
    Route::delete('flash-sale/{flashSale}', [\App\Http\Controllers\Admin\FlashSaleController::class, 'destroy'])->name('flashSale.destroy');

    Route::get('coupon', [\App\Http\Controllers\Admin\CouponController::class, 'index'])->name('coupon.index');
    Route::get('coupon/create', [\App\Http\Controllers\Admin\CouponController::class, 'create'])->name('coupon.create');
    Route::post('coupon', [\App\Http\Controllers\Admin\CouponController::class, 'store'])->name('coupon.store');
    Route::get('coupon/{coupon}/edit', [\App\Http\Controllers\Admin\CouponController::class, 'edit'])->name('coupon.edit');
    Route::put('coupon/{coupon}', [\App\Http\Controllers\Admin\CouponController::class, 'update'])->name('coupon.update');
    Route::get('coupon/{coupon}/toggle', [\App\Http\Controllers\Admin\CouponController::class, 'statusToggle'])->name('coupon.toggle');
    Route::delete('coupon/{coupon}', [\App\Http\Controllers\Admin\CouponController::class, 'destroy'])->name('coupon.destroy');

    Route::get('order', [\App\Http\Controllers\Admin\OrderController::class, 'index'])->name('order.index');
    Route::get('order/{order}', [\App\Http\Controllers\Admin\OrderController::class, 'show'])->name('order.show');
    Route::put('order/{order}/status', [\App\Http\Controllers\Admin\OrderController::class, 'statusChange'])->name('order.status.change');
    Route::put('order/{order}/payment-status-toggle', [\App\Http\Controllers\Admin\OrderController::class, 'paymentStatusToggle'])->name('order.payment.status.toggle');

    Route::get('return-order', [\App\Http\Controllers\Admin\ReturnOrderController::class, 'index'])->name('returnOrder.index');
    Route::get('return-order/{returnOrder}', [\App\Http\Controllers\Admin\ReturnOrderController::class, 'show'])->name('returnOrder.show');
    Route::put('return-order/{returnOrder}/payment-status', [\App\Http\Controllers\Admin\ReturnOrderController::class, 'paymentStatus'])->name('returnOrder.payment.status');
    Route::post('return-order/{returnOrder}/reject', [\App\Http\Controllers\Admin\ReturnOrderController::class, 'returnReject'])->name('returnOrder.reject');

    Route::get('customer', [\App\Http\Controllers\Admin\CustomerController::class, 'index'])->name('customer.index');
    Route::get('customer/create', [\App\Http\Controllers\Admin\CustomerController::class, 'create'])->name('customer.create');
    Route::post('customer', [\App\Http\Controllers\Admin\CustomerController::class, 'store'])->name('customer.store');
    Route::get('customer/{user}/edit', [\App\Http\Controllers\Admin\CustomerController::class, 'edit'])->name('customer.edit');
    Route::put('customer/{user}', [\App\Http\Controllers\Admin\CustomerController::class, 'update'])->name('customer.update');
    Route::delete('customer/{user}', [\App\Http\Controllers\Admin\CustomerController::class, 'destroy'])->name('customer.destroy');

    Route::get('customer-notification', [\App\Http\Controllers\Admin\CustomerNotificationController::class, 'index'])->name('customerNotification.index');
    Route::post('customer-notification/filter', [\App\Http\Controllers\Admin\CustomerNotificationController::class, 'filter'])->name('customerNotification.filter');
    Route::post('customer-notification/send', [\App\Http\Controllers\Admin\CustomerNotificationController::class, 'send'])->name('customerNotification.send');

    Route::get('shop', [\App\Http\Controllers\Admin\ShopController::class, 'index'])->name('shop.index');
    Route::get('shop/create', [\App\Http\Controllers\Admin\ShopController::class, 'create'])->name('shop.create');
    Route::post('shop', [\App\Http\Controllers\Admin\ShopController::class, 'store'])->name('shop.store');
    Route::get('shop/{shop}', [\App\Http\Controllers\Admin\ShopController::class, 'show'])->name('shop.show');
    Route::get('shop/{shop}/edit', [\App\Http\Controllers\Admin\ShopController::class, 'edit'])->name('shop.edit');
    Route::put('shop/{shop}', [\App\Http\Controllers\Admin\ShopController::class, 'update'])->name('shop.update');
    Route::put('shop/{shop}/status-toggle', [\App\Http\Controllers\Admin\ShopController::class, 'statusToggle'])->name('shop.status.toggle');
    Route::get('shop/{shop}/orders', [\App\Http\Controllers\Admin\ShopController::class, 'orders'])->name('shop.orders');
    Route::get('shop/{shop}/products', [\App\Http\Controllers\Admin\ShopController::class, 'products'])->name('shop.products');
    Route::get('shop/{shop}/reviews', [\App\Http\Controllers\Admin\ShopController::class, 'reviews'])->name('shop.reviews');
    Route::post('shop/{shop}/reset-password', [\App\Http\Controllers\Admin\ShopController::class, 'resetPassword'])->name('shop.reset.password');

    Route::get('rider', [\App\Http\Controllers\Admin\RiderController::class, 'index'])->name('rider.index');
    Route::get('rider/create', [\App\Http\Controllers\Admin\RiderController::class, 'create'])->name('rider.create');
    Route::post('rider', [\App\Http\Controllers\Admin\RiderController::class, 'store'])->name('rider.store');
    Route::get('rider/{user}', [\App\Http\Controllers\Admin\RiderController::class, 'show'])->name('rider.show');
    Route::get('rider/{user}/edit', [\App\Http\Controllers\Admin\RiderController::class, 'edit'])->name('rider.edit');
    Route::put('rider/{user}', [\App\Http\Controllers\Admin\RiderController::class, 'update'])->name('rider.update');
    Route::put('rider/{user}/toggle', [\App\Http\Controllers\Admin\RiderController::class, 'statusToggle'])->name('rider.toggle');
    Route::post('rider/assign-order', [\App\Http\Controllers\Admin\RiderController::class, 'assignOrder'])->name('rider.assign.order');
    Route::get('rider/{id}/location', [\App\Http\Controllers\Admin\RiderController::class, 'riderLocation'])->name('rider.location');

    Route::get('review', [\App\Http\Controllers\Admin\ReviewsController::class, 'index'])->name('review.index');
    Route::get('review/{reviewId}/toggle', [\App\Http\Controllers\Admin\ReviewsController::class, 'toggleReview'])->name('review.toggle');

    Route::get('withdraw', [\App\Http\Controllers\Admin\WithdrawController::class, 'index'])->name('withdraw.index');
    Route::get('withdraw/{withdraw}', [\App\Http\Controllers\Admin\WithdrawController::class, 'show'])->name('withdraw.show');
    Route::put('withdraw/{withdraw}', [\App\Http\Controllers\Admin\WithdrawController::class, 'update'])->name('withdraw.update');

    Route::get('delivery-charge', [\App\Http\Controllers\Admin\DeliveryChargeController::class, 'index'])->name('deliveryCharge.index');
    Route::get('delivery-charge/create', [\App\Http\Controllers\Admin\DeliveryChargeController::class, 'create'])->name('deliveryCharge.create');
    Route::post('delivery-charge', [\App\Http\Controllers\Admin\DeliveryChargeController::class, 'store'])->name('deliveryCharge.store');
    Route::get('delivery-charge/{deliveryCharge}/edit', [\App\Http\Controllers\Admin\DeliveryChargeController::class, 'edit'])->name('deliveryCharge.edit');
    Route::put('delivery-charge/{deliveryCharge}', [\App\Http\Controllers\Admin\DeliveryChargeController::class, 'update'])->name('deliveryCharge.update');
    Route::delete('delivery-charge/{deliveryCharge}', [\App\Http\Controllers\Admin\DeliveryChargeController::class, 'destroy'])->name('deliveryCharge.destroy');

    Route::get('vat-tax', [\App\Http\Controllers\Admin\VatTaxController::class, 'index'])->name('vatTax.index');
    Route::post('vat-tax', [\App\Http\Controllers\Admin\VatTaxController::class, 'store'])->name('vatTax.store');
    Route::put('vat-tax/{vatTax}', [\App\Http\Controllers\Admin\VatTaxController::class, 'update'])->name('vatTax.update');
    Route::get('vat-tax/{vatTax}/toggle', [\App\Http\Controllers\Admin\VatTaxController::class, 'toggle'])->name('vatTax.toggle');
    Route::delete('vat-tax/{vatTax}', [\App\Http\Controllers\Admin\VatTaxController::class, 'destroy'])->name('vatTax.destroy');

    Route::get('notification', [\App\Http\Controllers\Admin\NotificationController::class, 'index'])->name('notification.index');
    Route::get('notification/show', [\App\Http\Controllers\Admin\NotificationController::class, 'show'])->name('notification.show');
    Route::get('notification/{notification}/read', [\App\Http\Controllers\Admin\NotificationController::class, 'markAsRead'])->name('notification.read');
    Route::get('notification/read-all', [\App\Http\Controllers\Admin\NotificationController::class, 'markAllAsRead'])->name('notification.readAll');
    Route::delete('notification/{notification}', [\App\Http\Controllers\Admin\NotificationController::class, 'destroy'])->name('notification.destroy');

    Route::get('blog', [\App\Http\Controllers\Admin\BlogController::class, 'index'])->name('blog.index');
    Route::get('blog/create', [\App\Http\Controllers\Admin\BlogController::class, 'create'])->name('blog.create');
    Route::post('blog', [\App\Http\Controllers\Admin\BlogController::class, 'store'])->name('blog.store');
    Route::get('blog/{blog}/edit', [\App\Http\Controllers\Admin\BlogController::class, 'edit'])->name('blog.edit');
    Route::put('blog/{blog}', [\App\Http\Controllers\Admin\BlogController::class, 'update'])->name('blog.update');
    Route::get('blog/{blog}/toggle', [\App\Http\Controllers\Admin\BlogController::class, 'statusToggle'])->name('blog.toggle');
    Route::delete('blog/{blog}', [\App\Http\Controllers\Admin\BlogController::class, 'destroy'])->name('blog.destroy');
    Route::post('blog/generate-ai', [\App\Http\Controllers\Admin\BlogController::class, 'generateAIData'])->name('blog.generate.AI.data');

    Route::get('page', [\App\Http\Controllers\Admin\PageController::class, 'index'])->name('page.index');
    Route::get('page/create', [\App\Http\Controllers\Admin\PageController::class, 'create'])->name('page.create');
    Route::post('page', [\App\Http\Controllers\Admin\PageController::class, 'store'])->name('page.store');
    Route::get('page/{page}', [\App\Http\Controllers\Admin\PageController::class, 'show'])->name('page.show');
    Route::get('page/{page}/edit', [\App\Http\Controllers\Admin\PageController::class, 'edit'])->name('page.edit');
    Route::put('page/{page}', [\App\Http\Controllers\Admin\PageController::class, 'update'])->name('page.update');
    Route::delete('page/{page}', [\App\Http\Controllers\Admin\PageController::class, 'destroy'])->name('page.destroy');
    Route::post('page/generate-ai', [\App\Http\Controllers\Admin\PageController::class, 'generateAIData'])->name('page.generate.AI.data');

    Route::get('menu', [\App\Http\Controllers\Admin\MenuController::class, 'index'])->name('menu.index');
    Route::post('menu', [\App\Http\Controllers\Admin\MenuController::class, 'store'])->name('menu.store');
    Route::put('menu/{menu}', [\App\Http\Controllers\Admin\MenuController::class, 'update'])->name('menu.update');
    Route::get('menu/{menu}/remove', [\App\Http\Controllers\Admin\MenuController::class, 'remove'])->name('menu.remove');
    Route::delete('menu/{menu}', [\App\Http\Controllers\Admin\MenuController::class, 'destroy'])->name('menu.destroy');
    Route::post('menu/sort', [\App\Http\Controllers\Admin\MenuController::class, 'sort'])->name('menu.sort');
    Route::post('menu/drag', [\App\Http\Controllers\Admin\MenuController::class, 'drag'])->name('menu.drag');

    Route::get('footer', [\App\Http\Controllers\Admin\FooterController::class, 'index'])->name('footer.index');
    Route::put('footer/{footer}', [\App\Http\Controllers\Admin\FooterController::class, 'update'])->name('footer.update');
    Route::put('footer/item/{footerItem}', [\App\Http\Controllers\Admin\FooterController::class, 'updateItem'])->name('footer.update.item');
    Route::post('footer/section-sort', [\App\Http\Controllers\Admin\FooterController::class, 'sectionSort'])->name('footer.sectionSort');
    Route::post('footer/add-new', [\App\Http\Controllers\Admin\FooterController::class, 'addedNew'])->name('footer.addedNew');
    Route::post('footer/item-sort', [\App\Http\Controllers\Admin\FooterController::class, 'itemSort'])->name('footer.itemSort');
    Route::post('footer/{footerItem}/disable', [\App\Http\Controllers\Admin\FooterController::class, 'disabled'])->name('footer.disabled');
    Route::delete('footer/item/{footerItem}', [\App\Http\Controllers\Admin\FooterController::class, 'destroy'])->name('footer.destroy');

    Route::get('legal-page', [\App\Http\Controllers\Admin\LegalPageController::class, 'index'])->name('legalPage.index');
    Route::get('legal-page/{slug}/edit', [\App\Http\Controllers\Admin\LegalPageController::class, 'edit'])->name('legalPage.edit');
    Route::put('legal-page/{slug}', [\App\Http\Controllers\Admin\LegalPageController::class, 'update'])->name('legalPage.update');

    Route::get('social-link', [\App\Http\Controllers\Admin\SocialLinkController::class, 'index'])->name('socialLink.index');
    Route::put('social-link/{socialLink}', [\App\Http\Controllers\Admin\SocialLinkController::class, 'update'])->name('socialLink.update');
    Route::get('social-link/{socialLink}/toggle', [\App\Http\Controllers\Admin\SocialLinkController::class, 'toggle'])->name('socialLink.toggle');

    Route::get('social-auth', [\App\Http\Controllers\Admin\SocialAuthController::class, 'index'])->name('socialAuth.index');
    Route::put('social-auth/{socialAuth}', [\App\Http\Controllers\Admin\SocialAuthController::class, 'update'])->name('socialAuth.update');
    Route::get('social-auth/{socialAuth}/toggle', [\App\Http\Controllers\Admin\SocialAuthController::class, 'toggle'])->name('socialAuth.toggle');

    Route::get('contact-us', [\App\Http\Controllers\Admin\ContactUsController::class, 'index'])->name('contactUs.index');
    Route::put('contact-us/{contactUs}', [\App\Http\Controllers\Admin\ContactUsController::class, 'update'])->name('contactUs.update');

    Route::get('support', [\App\Http\Controllers\Admin\SupportController::class, 'index'])->name('support.index');
    Route::delete('support/{support}', [\App\Http\Controllers\Admin\SupportController::class, 'delete'])->name('support.delete');

    Route::get('support-ticket', [\App\Http\Controllers\Admin\SupportTicketController::class, 'index'])->name('supportTicket.index');
    Route::get('support-ticket/{supportTicket}', [\App\Http\Controllers\Admin\SupportTicketController::class, 'show'])->name('supportTicket.show');
    Route::post('support-ticket/{supportTicket}/set-scheduled', [\App\Http\Controllers\Admin\SupportTicketController::class, 'setScheduled'])->name('supportTicket.setScheduled');
    Route::post('support-ticket/{supportTicket}/send-message', [\App\Http\Controllers\Admin\SupportTicketController::class, 'sendMessage'])->name('supportTicket.sendMessage');
    Route::get('support-ticket/{supportTicket}/fetch-messages', [\App\Http\Controllers\Admin\SupportTicketController::class, 'fetchMessages'])->name('supportTicket.fetchMessages');
    Route::post('support-ticket/{supportTicket}/update-status', [\App\Http\Controllers\Admin\SupportTicketController::class, 'updateStatus'])->name('supportTicket.updateStatus');
    Route::get('support-ticket/{supportTicket}/chat-toggle', [\App\Http\Controllers\Admin\SupportTicketController::class, 'chatToggle'])->name('supportTicket.chatToggle');
    Route::get('support-ticket/message/{supportTicketMessage}/pin', [\App\Http\Controllers\Admin\SupportTicketController::class, 'pinMessage'])->name('supportTicket.pinMessage');

    Route::get('ticket-issue-type', [\App\Http\Controllers\Admin\TicketIssueTypeController::class, 'index'])->name('ticketIssueType.index');
    Route::post('ticket-issue-type', [\App\Http\Controllers\Admin\TicketIssueTypeController::class, 'store'])->name('ticketIssueType.store');
    Route::put('ticket-issue-type/{ticketIssueType}', [\App\Http\Controllers\Admin\TicketIssueTypeController::class, 'update'])->name('ticketIssueType.update');
    Route::get('ticket-issue-type/{ticketIssueType}/toggle', [\App\Http\Controllers\Admin\TicketIssueTypeController::class, 'toggleStatus'])->name('ticketIssueType.toggle');
    Route::delete('ticket-issue-type/{ticketIssueType}', [\App\Http\Controllers\Admin\TicketIssueTypeController::class, 'destroy'])->name('ticketIssueType.delete');
});

// pwaSetting — controller/model were server-withheld
Route::match(["get","post"], "admin/pwa-setting", function () { return back()->with("warning", "PWA setting unavailable"); })->name("admin.pwaSetting.update")->middleware(['auth', 'role:root']);
