<!--- Dashboard --->
<li>
    <a class="menu <?php echo e($request->routeIs('shop.dashboard.*') ? 'active' : ''); ?>"
        href="<?php echo e(route('shop.dashboard.index')); ?>">
        <span>
            <img class="menu-icon" src="<?php echo e(asset('assets/icons-admin/dashboard.svg')); ?>" alt="icon" loading="lazy" />
            <?php echo e(__('Dashboard')); ?>

        </span>
    </a>
</li>

<?php if($generaleSetting?->business_based_on == 'subscription'): ?>
    <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'shop.subscription.index')): ?>
        <!--- subscription --->
        <li>
            <a href="<?php echo e(route('shop.subscription.index')); ?>"
                class="menu <?php echo e(request()->routeIs('shop.subscription.*') ? 'active' : ''); ?>">
                <span>
                    <img class="menu-icon" src="<?php echo e(asset('assets/icons-admin/crown.svg')); ?>" alt="icon"
                        loading="lazy" />
                    <?php echo e(__('Subscription')); ?>

                </span>
            </a>
        </li>
    <?php endif; ?>
<?php endif; ?>

<?php
    use App\Enums\OrderStatus;
    $orderStatuses = OrderStatus::cases();
?>
<?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'shop.order.index')): ?>
    <!--- Orders--->
    <li>
        <a class="menu <?php echo e(request()->routeIs('shop.order.*') ? 'active' : ''); ?>" data-bs-toggle="collapse"
            href="#settingMenu">
            <span>
                <img class="menu-icon" src="<?php echo e(asset('assets/icons-admin/orders.svg')); ?>" alt="icon" loading="lazy" />
                <?php echo e(__('All Orders')); ?>

            </span>
            <img src="<?php echo e(asset('assets/icons-admin/caret-down.svg')); ?>" alt="" class="downIcon" loading="lazy" />
        </a>
        <div class="collapse dropdownMenuCollapse <?php echo e($request->routeIs('shop.order.*') ? 'show' : ''); ?>" id="settingMenu">
            <div class="listBar">
                <a href="<?php echo e(route('shop.order.index')); ?>"
                    class="subMenu hasCount <?php echo e(request()->url() === route('shop.order.index') ? 'active' : ''); ?>">
                    <?php echo e(__('All')); ?> <span class="count statusAll"><?php echo e($allOrders > 99 ? '99+' : $allOrders); ?></span>
                </a>
                <?php $__currentLoopData = $orderStatuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e(route('shop.order.index', str_replace(' ', '_', $status->value))); ?>"
                        class="subMenu hasCount <?php echo e(request()->url() === route('shop.order.index', str_replace(' ', '_', $status->value)) ? 'active' : ''); ?>">
                        <span><?php echo e(__($status->value)); ?></span>
                        <span class="count status<?php echo e(Str::camel($status->value)); ?>">
                            <?php echo e(${Str::camel($status->value)} > 99 ? '99+' : ${Str::camel($status->value)}); ?>

                        </span>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </li>
<?php endif; ?>

<?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', ['shop.pos.index', 'shop.pos.draft', 'shop.pos.sales'])): ?>
    <li>
        <a class="menu <?php echo e(request()->routeIs('shop.pos.*') ? 'active' : ''); ?>" data-bs-toggle="collapse" href="#posMenu">
            <span>
                <img class="menu-icon" src="<?php echo e(asset('assets/icons-admin/pos.svg')); ?>" alt="icon" loading="lazy" />
                <?php echo e(__('POS Management')); ?>

            </span>
            <img src="<?php echo e(asset('assets/icons-admin/caret-down.svg')); ?>" alt="icon" class="downIcon">
        </a>
        <div class="collapse dropdownMenuCollapse <?php echo e($request->routeIs('shop.pos.*') ? 'show' : ''); ?>" id="posMenu">
            <div class="listBar">
                <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'shop.pos.index')): ?>
                    <a href="<?php echo e(route('shop.pos.index')); ?>"
                        class="subMenu hasCount <?php echo e(request()->routeIs('shop.pos.index') ? 'active' : ''); ?>">
                        <?php echo e(__('POS')); ?>

                    </a>
                <?php endif; ?>
                <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'shop.pos.sales')): ?>
                    <a href="<?php echo e(route('shop.pos.sales')); ?>"
                        class="subMenu hasCount <?php echo e(request()->routeIs('shop.pos.sales') ? 'active' : ''); ?>">
                        <?php echo e(__('POS Sales History')); ?>

                    </a>
                <?php endif; ?>
                <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'shop.pos.draft')): ?>
                    <a href="<?php echo e(route('shop.pos.draft')); ?>"
                        class="subMenu hasCount <?php echo e(request()->routeIs('shop.pos.draft') ? 'active' : ''); ?>">
                        <?php echo e(__('POS Draft')); ?>

                    </a>
                <?php endif; ?>
            </div>
        </div>
    </li>
<?php endif; ?>

<?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'shop.returnOrder.index')): ?>
  <li>
        <a class="menu <?php echo e($request->routeIs('shop.returnOrder.*') ? 'active' : ''); ?>" href="<?php echo e(route('shop.returnOrder.index')); ?>">
            <span>
                <img class="menu-icon" src="<?php echo e(asset('assets/icons-admin/delivery-cart-arrow-up.svg')); ?>" alt="icon" loading="lazy" />
                <?php echo e(__('Refund Management')); ?>

            </span>
        </a>
    </li>
<?php endif; ?>

<li>
    <a class="menu <?php echo e($request->routeIs('shop.customer.chat.index') ? 'active' : ''); ?>"
        href="<?php echo e(route('shop.customer.chat.index')); ?>">
        <span class="position-relative">
            <img class="menu-icon" src="<?php echo e(asset('assets/icons-admin/message.svg')); ?>" alt="icon" loading="lazy" />
            <?php echo e(__('Messages')); ?>

            <span id="unread-message-badge" class="bg-success text-white ms-2 position-absolute d-none"
                style="top: 0; transform: translateY(-50%); left: 5px; height: 16px; width: 16px; border-radius: 50%; font-size: 10px; display: flex; align-items: center; justify-content: center;">
                0
            </span>
        </span>
    </a>
</li>

<?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', ['shop.category.index', 'shop.subcategory.index'])): ?>
    <!--- categories--->
    <li>
        <a class="menu <?php echo e(request()->routeIs('shop.category.*', 'shop.subcategory.*') ? 'active' : ''); ?>"
            data-bs-toggle="collapse" href="#categoryMenu">
            <span>
                <img class="menu-icon" src="<?php echo e(asset('assets/icons-admin/category.svg')); ?>" alt="icon"
                    loading="lazy" />
                <?php echo e(__('Category Management')); ?>

            </span>
            <img src="<?php echo e(asset('assets/icons-admin/caret-down.svg')); ?>" alt="" class="downIcon" loading="lazy" />
        </a>
        <div class="collapse dropdownMenuCollapse <?php echo e($request->routeIs('shop.category.*', 'shop.subcategory.*') ? 'show' : ''); ?>"
            id="categoryMenu">
            <div class="listBar">
                <!---  categories--->
                <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'shop.category.index')): ?>
                    <a href="<?php echo e(route('shop.category.index')); ?>"
                        class="subMenu hasCount <?php echo e(request()->routeIs('shop.category.index') ? 'active' : ''); ?>">
                        <?php echo e(__('Category')); ?>

                    </a>
                <?php endif; ?>
                <!--- sub categories--->
                <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'shop.subcategory.index')): ?>
                    <a href="<?php echo e(route('shop.subcategory.index')); ?>"
                        class="subMenu hasCount <?php echo e(request()->routeIs('shop.subcategory.index') ? 'active' : ''); ?>">
                        <?php echo e(__('Sub Category')); ?>

                    </a>
                <?php endif; ?>
            </div>
        </div>
    </li>
<?php endif; ?>

<?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', ['shop.product.index'])): ?>
    <!--- Products--->
    <li>
        <a class="menu <?php echo e(request()->routeIs('shop.product.*','shop.digital.product.*') ? 'active' : ''); ?>" data-bs-toggle="collapse"
            href="#productMenu">
            <span>
                <img class="menu-icon" src="<?php echo e(asset('assets/icons-admin/product.svg')); ?>" alt="icon" loading="lazy" />
                <?php echo e(__('Product Management')); ?>

            </span>
            <img src="<?php echo e(asset('assets/icons-admin/caret-down.svg')); ?>" alt="" class="downIcon" loading="lazy" />
        </a>
        <div class="collapse dropdownMenuCollapse <?php echo e($request->routeIs('shop.product.*','shop.digital.product.*') ? 'show' : ''); ?>"
            id="productMenu">
            <div class="listBar">
                <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'shop.product.index')): ?>
                    <a href="<?php echo e(route('shop.product.index')); ?>"
                        class="subMenu hasCount <?php echo e(request()->routeIs('shop.product.index') ? 'active' : ''); ?>">
                        <?php echo e(__('All Product')); ?>

                    </a>
                <?php endif; ?>
                <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'shop.product.create')): ?>
                    <a href="<?php echo e(route('shop.product.create')); ?>"
                        class="subMenu hasCount <?php echo e(request()->routeIs('shop.product.create') ? 'active' : ''); ?>">
                        <?php echo e(__('Add Product')); ?>

                    </a>
                <?php endif; ?>
                <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'shop.product.create')): ?>
                    <a href="<?php echo e(route('shop.digital.product.create')); ?>"
                        class="subMenu hasCount <?php echo e(request()->routeIs('shop.digital.product.create') ? 'active' : ''); ?>">
                        <?php echo e(__('Add Digital Product')); ?>

                    </a>
                <?php endif; ?>
            </div>
        </div>
    </li>
<?php endif; ?>


<?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', ['shop.brand.index', 'shop.color.index', 'shop.size.index', 'shop.unit.index'])): ?>
    <!--- Product Varient --->
    <li>
        <a class="menu <?php echo e(request()->routeIs('shop.brand.*', 'shop.color.*', 'shop.size.*', 'shop.unit.*') ? 'active' : ''); ?>"
            data-bs-toggle="collapse" href="#productVarientMenu">
            <span>
                <img class="menu-icon" src="<?php echo e(asset('assets/icons-admin/boxes.svg')); ?>" alt="icon"
                    loading="lazy" />
                <?php echo e(__('Product Variant Management')); ?>

            </span>
            <img src="<?php echo e(asset('assets/icons-admin/caret-down.svg')); ?>" alt="icon" class="downIcon">
        </a>
        <div class="collapse dropdownMenuCollapse <?php echo e($request->routeIs('shop.brand.*', 'shop.color.*', 'shop.size.*', 'shop.unit.*') ? 'show' : ''); ?>"
            id="productVarientMenu">
            <div class="listBar">
                <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'shop.brand.index')): ?>
                    <a href="<?php echo e(route('shop.brand.index')); ?>"
                        class="subMenu hasCount <?php echo e(request()->routeIs('shop.brand.index') ? 'active' : ''); ?>">
                        <?php echo e(__('Brand')); ?>

                    </a>
                <?php endif; ?>
                <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'shop.color.index')): ?>
                    <a href="<?php echo e(route('shop.color.index')); ?>"
                        class="subMenu hasCount <?php echo e(request()->routeIs('shop.color.index') ? 'active' : ''); ?>">
                        <?php echo e(__('Color')); ?>

                    </a>
                <?php endif; ?>
                <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'shop.size.index')): ?>
                    <a href="<?php echo e(route('shop.size.index')); ?>"
                        class="subMenu hasCount <?php echo e(request()->routeIs('shop.size.index') ? 'active' : ''); ?>">
                        <?php echo e(__('Size')); ?>

                    </a>
                <?php endif; ?>
                <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'shop.unit.index')): ?>
                    <a href="<?php echo e(route('shop.unit.index')); ?>"
                        class="subMenu hasCount <?php echo e(request()->routeIs('shop.unit.index') ? 'active' : ''); ?>">
                        <?php echo e(__('Unit')); ?>

                    </a>
                <?php endif; ?>
            </div>
        </div>
    </li>
<?php endif; ?>

<?php if(module_exists('purchase') ): ?>
    <?php echo $__env->make('purchase::layouts.purchaseSidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php endif; ?>

<?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', ['shop.flashSale.index', 'shop.banner.index', 'shop.voucher.index'])): ?>
    <!--- Promotion management--->
    <li>
        <a class="menu <?php echo e(request()->routeIs('shop.flashSale.*', 'shop.banner.*', 'shop.voucher.*') ? 'active' : ''); ?>"
            data-bs-toggle="collapse" href="#promotionMenu">
            <span>
                <img class="menu-icon" src="<?php echo e(asset('assets/icons-admin/ads.svg')); ?>" alt="icon" loading="lazy" />
                <?php echo e(__('Promotion Management')); ?>

            </span>
            <img src="<?php echo e(asset('assets/icons-admin/caret-down.svg')); ?>" alt="icon" class="downIcon">
        </a>
        <div class="collapse dropdownMenuCollapse <?php echo e($request->routeIs('shop.flashSale.*', 'shop.banner.*', 'shop.voucher.*') ? 'show' : ''); ?>"
            id="promotionMenu">
            <div class="listBar">
                <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'shop.flashSale.index')): ?>
                    <a href="<?php echo e(route('shop.flashSale.index')); ?>"
                        class="subMenu hasCount <?php echo e(request()->routeIs('shop.flashSale.*') ? 'active' : ''); ?>">
                        <?php echo e(__('Flash Deals')); ?>

                    </a>
                <?php endif; ?>
                <?php if($businessModel == 'multi'): ?>
                    <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'shop.banner.index')): ?>
                        <a href="<?php echo e(route('shop.banner.index')); ?>"
                            class="subMenu hasCount <?php echo e(request()->routeIs('shop.banner.*') ? 'active' : ''); ?>">
                            <?php echo e(__('Banner Setup ')); ?>

                        </a>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'shop.voucher.index')): ?>
                    <a href="<?php echo e(route('shop.voucher.index')); ?>"
                        class="subMenu hasCount <?php echo e(request()->routeIs('shop.voucher.*') ? 'active' : ''); ?>">
                        <?php echo e(__('Promo Code')); ?>

                    </a>
                <?php endif; ?>
            </div>
        </div>
    </li>
<?php endif; ?>

<?php if(module_exists('report') ): ?>
    <?php echo $__env->make('report::layouts.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php endif; ?>

<?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', ['shop.employee.index'])): ?>
    <!--- employee--->
    <li>
        <a class="menu <?php echo e(request()->routeIs('shop.employee.*') ? 'active' : ''); ?>" data-bs-toggle="collapse"
            href="#employeeMenu">
            <span>
                <img class="menu-icon" src="<?php echo e(asset('assets/icons-admin/employee.svg')); ?>" alt="icon"
                    loading="lazy" />
                <?php echo e(__('Employee Management')); ?>

            </span>
            <img src="<?php echo e(asset('assets/icons-admin/caret-down.svg')); ?>" alt="" class="downIcon"
                loading="lazy" />
        </a>
        <div class="collapse dropdownMenuCollapse <?php echo e($request->routeIs('shop.employee.*') ? 'show' : ''); ?>"
            id="employeeMenu">
            <div class="listBar">
                <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'shop.employee.index')): ?>
                    <a href="<?php echo e(route('shop.employee.index')); ?>"
                        class="subMenu hasCount <?php echo e(request()->routeIs('shop.employee.index') ? 'active' : ''); ?>">
                        <?php echo e(__('Employees')); ?>

                    </a>
                <?php endif; ?>
                <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'shop.employee.create')): ?>
                    <a href="<?php echo e(route('shop.employee.create')); ?>"
                        class="subMenu hasCount <?php echo e(request()->routeIs('shop.employee.create') ? 'active' : ''); ?>">
                        <?php echo e(__('Add Employee')); ?>

                    </a>
                <?php endif; ?>
            </div>
        </div>
    </li>
<?php endif; ?>

<?php if(module_exists('purchase') ): ?>
    <?php echo $__env->make('purchase::layouts.supplierSidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php endif; ?>

<?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', ['shop.profile.index'])): ?>
    <!--- Profile --->
    <li>
        <a class="menu <?php echo e($request->routeIs('shop.profile.*') ? 'active' : ''); ?>"
            href="<?php echo e(route('shop.profile.index')); ?>">
            <span>
                <img class="menu-icon" src="<?php echo e(asset('assets/icons-admin/shop.svg')); ?>" alt="icon"
                    loading="lazy" />
                <?php echo e(__('My Shop')); ?>

            </span>
        </a>
    </li>
<?php endif; ?>



<?php if(!auth()->user()->hasRole('root')): ?>
    <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'shop.withdraw.index')): ?>
        <!--- withdraw --->
        <li>
            <a class="menu <?php echo e($request->routeIs('shop.withdraw.*') ? 'active' : ''); ?>"
                href="<?php echo e(route('shop.withdraw.index')); ?>">
                <span>
                    <img class="menu-icon" src="<?php echo e(asset('assets/icons-admin/withdraw.svg')); ?>" alt="icon"
                        loading="lazy" />
                    <?php echo e(__('Withdraws')); ?>

                </span>
            </a>
        </li>
    <?php endif; ?>
<?php endif; ?>

<?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', ['shop.bulk-product-export.index', 'shop.bulk-product-import.index', 'shop.gallery.index'])): ?>
    <!--- Import / Export --->
    <li>
        <a class="menu <?php echo e(request()->routeIs('shop.bulk-product-export.*', 'shop.bulk-product-import.*', 'shop.gallery.*') ? 'active' : ''); ?>"
            data-bs-toggle="collapse" href="#supportMenu">
            <span>
                <img class="menu-icon" src="<?php echo e(asset('assets/icons-admin/download.svg')); ?>" alt="icon"
                    loading="lazy" />
                <?php echo e(__('Import/Export')); ?>

            </span>
            <img src="<?php echo e(asset('assets/icons-admin/caret-down.svg')); ?>" alt="icon" class="downIcon">
        </a>
        <div class="collapse dropdownMenuCollapse <?php echo e($request->routeIs('shop.bulk-product-export.*', 'shop.bulk-product-import.*', 'shop.gallery.*') ? 'show' : ''); ?>"
            id="supportMenu">
            <div class="listBar">
                <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'shop.bulk-product-export.index')): ?>
                    <a href="<?php echo e(route('shop.bulk-product-export.index')); ?>"
                        class="subMenu hasCount <?php echo e(request()->routeIs('shop.bulk-product-export.*') ? 'active' : ''); ?>">
                        <?php echo e(__('Product Export')); ?>

                    </a>
                <?php endif; ?>
                <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'shop.bulk-product-import.index')): ?>
                    <a href="<?php echo e(route('shop.bulk-product-import.index')); ?>"
                        class="subMenu hasCount <?php echo e(request()->routeIs('shop.bulk-product-import.*') ? 'active' : ''); ?>">
                        <?php echo e(__('Product Import')); ?>

                    </a>
                <?php endif; ?>
                <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'shop.gallery.index')): ?>
                    <a href="<?php echo e(route('shop.gallery.index')); ?>"
                        class="subMenu hasCount <?php echo e(request()->routeIs('shop.gallery.*') ? 'active' : ''); ?>">
                        <?php echo e(__('Gallery Import')); ?>

                    </a>
                <?php endif; ?>
            </div>
        </div>
    </li>
<?php endif; ?>
<?php /**PATH /home/u939461333/domains/janmitram.com/public_html/app/resources/views/layouts/partials/shop-menu.blade.php ENDPATH**/ ?>