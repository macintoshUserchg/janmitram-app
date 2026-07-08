<!--- Dashboard --->
<li>
    <a class="menu <?php echo e($request->routeIs('admin.dashboard.*') ? 'active' : ''); ?>"
        href="<?php echo e(route('admin.dashboard.index')); ?>">
        <span>
            <img class="menu-icon" src="<?php echo e(asset('assets/icons-admin/dashboard.svg')); ?>" alt="icon" loading="lazy" />
            <?php echo e(__('Dashboard')); ?>

        </span>
    </a>
</li>


<?php
    use App\Enums\OrderStatus;
    $orderStatuses = OrderStatus::cases();
?>
<?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'admin.order.index')): ?>
    <!--- Orders --->
    <li>
        <a class="menu <?php echo e($request->routeIs('admin.order.*') ? 'active' : ''); ?>" href="<?php echo e(route('admin.order.index')); ?>">
            <span>
                <img class="menu-icon" src="<?php echo e(asset('assets/icons-admin/orders.svg')); ?>" alt="icon" loading="lazy" />
                <?php echo e(__('Order Management')); ?>

            </span>
        </a>
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

<?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'admin.returnOrder.index')): ?>
    <li>
        <a class="menu <?php echo e($request->routeIs('admin.returnOrder.*') ? 'active' : ''); ?>"
            href="<?php echo e(route('admin.returnOrder.index')); ?>">
            <span>
                <img class="menu-icon" src="<?php echo e(asset('assets/icons-admin/delivery-cart-arrow-up.svg')); ?>" alt="icon"
                    loading="lazy" />
                <?php echo e(__('Refund Management')); ?>

            </span>
        </a>
    </li>
<?php endif; ?>

<?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', [
    'admin.conversation.customer.chat.index',
    'admin.conversation.getUsers',
    'admin.conversation.getMessageAdmin'
])): ?>
    <li>
        <a class="menu <?php echo e($request->routeIs('shop.customer.chat.index') ? 'active' : ''); ?>"
            href="<?php echo e(route('shop.customer.chat.index')); ?>">
            <span class="position-relative">
                <img class="menu-icon" src="<?php echo e(asset('assets/icons-admin/message.svg')); ?>" alt="icon" loading="lazy" />
                <?php echo e(__('Conversations')); ?>

                <span id="unread-message-badge" class="bg-success text-white ms-2 position-absolute d-none"
                    style="top: 0; transform: translateY(-50%); left: 5px; height: 16px; width: 16px; border-radius: 50%; font-size: 10px; display: flex; align-items: center; justify-content: center;">
                    0
                </span>
            </span>
        </a>
    </li>
<?php endif; ?>

<?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', [
    'admin.category.index',
    'admin.subcategory.index',
    'admin.category.create',
    'admin.subcategory.create'
])): ?>
    <!--- categories --->
    <li>
        <a class="menu <?php echo e(request()->routeIs('admin.category.*', 'admin.subcategory.*') ? 'active' : ''); ?>"
            data-bs-toggle="collapse" href="#categoryMenu">
            <span>
                <img class="menu-icon" src="<?php echo e(asset('assets/icons-admin/category.svg')); ?>" alt="icon"
                    loading="lazy" />
                <?php echo e(__('Category Management')); ?>

            </span>
            <img src="<?php echo e(asset('assets/icons-admin/caret-down.svg')); ?>" alt="icon" class="downIcon">
        </a>
        <div class="collapse dropdownMenuCollapse <?php echo e($request->routeIs('admin.category.*', 'admin.subcategory.*') ? 'show' : ''); ?>"
            id="categoryMenu">
            <div class="listBar">
                <!---categories--->
                <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'admin.category.index')): ?>
                    <a href="<?php echo e(route('admin.category.index')); ?>"
                        class="subMenu hasCount <?php echo e(request()->routeIs('admin.category.index') ? 'active' : ''); ?>">
                        <?php echo e(__('All Category')); ?>

                    </a>
                <?php endif; ?>
                <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'admin.category.create')): ?>
                    <a href="<?php echo e(route('admin.category.create')); ?>"
                        class="subMenu hasCount <?php echo e(request()->routeIs('admin.category.create') ? 'active' : ''); ?>">
                        <?php echo e(__('Add Category')); ?>

                    </a>
                <?php endif; ?>
                <!--- sub categories--->
                <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'admin.subcategory.index')): ?>
                    <a href="<?php echo e(route('admin.subcategory.index')); ?>"
                        class="subMenu hasCount <?php echo e(request()->routeIs('admin.subcategory.index') ? 'active' : ''); ?>">
                        <?php echo e(__('All Sub Category')); ?>

                    </a>
                <?php endif; ?>
                <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'admin.subcategory.create')): ?>
                    <a href="<?php echo e(route('admin.subcategory.create')); ?>"
                        class="subMenu hasCount <?php echo e(request()->routeIs('admin.subcategory.create') ? 'active' : ''); ?>">
                        <?php echo e(__('Add Sub Category')); ?>

                    </a>
                <?php endif; ?>
            </div>
        </div>
    </li>
<?php endif; ?>
<?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', ['shop.product.index', 'shop.product.create'])): ?>
    <!--- Products--->
    <li>
        <a class="menu <?php echo e(request()->routeIs('shop.product.*', 'shop.digital.product.*') ? 'active' : ''); ?>"
            data-bs-toggle="collapse" href="#productMenu">
            <span>
                <img class="menu-icon" src="<?php echo e(asset('assets/icons-admin/product.svg')); ?>" alt="icon" loading="lazy" />
                <?php echo e(__('Product Management')); ?>

            </span>
            <img src="<?php echo e(asset('assets/icons-admin/caret-down.svg')); ?>" alt="icon" class="downIcon">
        </a>
        <div class="collapse dropdownMenuCollapse <?php echo e($request->routeIs('shop.product.*', 'shop.digital.product.*') ? 'show' : ''); ?>"
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

<?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', ['admin.brand.index', 'admin.color.index', 'admin.size.index', 'admin.unit.index'])): ?>
    <!--- Product Varient --->
    <li>
        <a class="menu <?php echo e(request()->routeIs('admin.brand.*', 'admin.color.*', 'admin.size.*', 'admin.unit.*') ? 'active' : ''); ?>"
            data-bs-toggle="collapse" href="#productVarientMenu">
            <span>
                <img class="menu-icon" src="<?php echo e(asset('assets/icons-admin/boxes.svg')); ?>" alt="icon" loading="lazy" />
                <?php echo e(__('Product Variant Management')); ?>

            </span>
            <img src="<?php echo e(asset('assets/icons-admin/caret-down.svg')); ?>" alt="icon" class="downIcon">
        </a>
        <div class="collapse dropdownMenuCollapse <?php echo e($request->routeIs('admin.brand.*', 'admin.color.*', 'admin.size.*', 'admin.unit.*') ? 'show' : ''); ?>"
            id="productVarientMenu">
            <div class="listBar">
                <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'admin.brand.index')): ?>
                    <a href="<?php echo e(route('admin.brand.index')); ?>"
                        class="subMenu hasCount <?php echo e(request()->routeIs('admin.brand.index') ? 'active' : ''); ?>">
                        <?php echo e(__('Brand')); ?>

                    </a>
                <?php endif; ?>
                <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'admin.color.index')): ?>
                    <a href="<?php echo e(route('admin.color.index')); ?>"
                        class="subMenu hasCount <?php echo e(request()->routeIs('admin.color.index') ? 'active' : ''); ?>">
                        <?php echo e(__('Color')); ?>

                    </a>
                <?php endif; ?>
                <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'admin.size.index')): ?>
                    <a href="<?php echo e(route('admin.size.index')); ?>"
                        class="subMenu hasCount <?php echo e(request()->routeIs('admin.size.index') ? 'active' : ''); ?>">
                        <?php echo e(__('Size')); ?>

                    </a>
                <?php endif; ?>
                <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'admin.unit.index')): ?>
                    <a href="<?php echo e(route('admin.unit.index')); ?>"
                        class="subMenu hasCount <?php echo e(request()->routeIs('admin.unit.index') ? 'active' : ''); ?>">
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

<?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', ['admin.banner.index', 'admin.flashSale.index', 'admin.ad.index', 'admin.coupon.index'])): ?>
    <!--- Promotion management--->
    <li>
        <a class="menu <?php echo e(request()->routeIs('admin.flashSale.*', 'admin.banner.*', 'admin.ad.*', 'admin.coupon.*') ? 'active' : ''); ?>"
            data-bs-toggle="collapse" href="#promotionMenu">
            <span>
                <img class="menu-icon" src="<?php echo e(asset('assets/icons-admin/ads.svg')); ?>" alt="icon" loading="lazy" />
                <?php echo e(__('Promotion Management')); ?>

            </span>
            <img src="<?php echo e(asset('assets/icons-admin/caret-down.svg')); ?>" alt="icon" class="downIcon">
        </a>
        <div class="collapse dropdownMenuCollapse <?php echo e($request->routeIs('admin.flashSale.*', 'admin.banner.*', 'admin.ad.*', 'admin.coupon.*') ? 'show' : ''); ?>"
            id="promotionMenu">
            <div class="listBar">
                <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'admin.flashSale.index')): ?>
                    <a href="<?php echo e(route('admin.flashSale.index')); ?>"
                        class="subMenu hasCount <?php echo e(request()->routeIs('admin.flashSale.*') ? 'active' : ''); ?>">
                        <?php echo e(__('Flash Deals')); ?>

                    </a>
                <?php endif; ?>
                <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'admin.banner.index')): ?>
                    <a href="<?php echo e(route('admin.banner.index')); ?>"
                        class="subMenu hasCount <?php echo e(request()->routeIs('admin.banner.*') ? 'active' : ''); ?>">
                        <?php echo e(__('Banner Setup')); ?>

                    </a>
                <?php endif; ?>
                <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'admin.ad.index')): ?>
                    <a href="<?php echo e(route('admin.ad.index')); ?>"
                        class="subMenu hasCount <?php echo e(request()->routeIs('admin.ad.*') ? 'active' : ''); ?>">
                        <?php echo e(__('Ads Campaign ')); ?>

                    </a>
                <?php endif; ?>
                <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'admin.coupon.index')): ?>
                    <a href="<?php echo e(route('admin.coupon.index')); ?>"
                        class="subMenu hasCount <?php echo e(request()->routeIs('admin.coupon.*') ? 'active' : ''); ?>">
                        <?php echo e(__('Promo Code')); ?>

                    </a>
                <?php endif; ?>
            </div>
        </div>
    </li>
<?php endif; ?>

<?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'admin.customerNotification.index')): ?>
    <!--- notification--->
    <li>
        <a class="menu <?php echo e($request->routeIs('admin.customerNotification.*') ? 'active' : ''); ?>"
            href="<?php echo e(route('admin.customerNotification.index')); ?>">
            <span>
                <img class="menu-icon" src="<?php echo e(asset('assets/icons-admin/notification.svg')); ?>" alt="icon"
                    loading="lazy" />
                <?php echo e(__('Push Notification')); ?>

            </span>
        </a>
    </li>
<?php endif; ?>


<?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', ['admin.blog.index', 'admin.blog.create'])): ?>
    <!--- blogs--->
    <li>
        <a class="menu <?php echo e(request()->routeIs('admin.blog.*') ? 'active' : ''); ?>" data-bs-toggle="collapse"
            href="#blogMenu">
            <span>
                <img class="menu-icon" src="<?php echo e(asset('assets/icons-admin/promotional.svg')); ?>" alt="icon"
                    loading="lazy" />
                <?php echo e(__('Blog Management')); ?>

            </span>
            <img src="<?php echo e(asset('assets/icons-admin/caret-down.svg')); ?>" alt="icon" class="downIcon">
        </a>
        <div class="collapse dropdownMenuCollapse <?php echo e($request->routeIs('admin.blog.*') ? 'show' : ''); ?>" id="blogMenu">
            <div class="listBar">
                <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'admin.blog.index')): ?>
                    <a href="<?php echo e(route('admin.blog.index')); ?>"
                        class="subMenu hasCount <?php echo e(request()->routeIs('admin.blog.index') ? 'active' : ''); ?>">
                        <?php echo e(__('All Blog')); ?>

                    </a>
                <?php endif; ?>
                <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'admin.blog.create')): ?>
                    <a href="<?php echo e(route('admin.blog.create')); ?>"
                        class="subMenu hasCount <?php echo e(request()->routeIs('admin.blog.create') ? 'active' : ''); ?>">
                        <?php echo e(__('Add Blog')); ?>

                    </a>
                <?php endif; ?>
            </div>
        </div>
    </li>
<?php endif; ?>

<?php if(module_exists('report') ): ?>
    <?php echo $__env->make('report::layouts.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php endif; ?>

<?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', ['admin.customer.index', 'admin.customer.create'])): ?>
    <!--- customers--->
    <li>
        <a class="menu <?php echo e(request()->routeIs('admin.customer.*') ? 'active' : ''); ?>" data-bs-toggle="collapse"
            href="#customerMenu">
            <span>
                <img class="menu-icon" src="<?php echo e(asset('assets/icons-admin/customer.svg')); ?>" alt="icon"
                    loading="lazy" />
                <?php echo e(__('Customer Management')); ?>

            </span>
            <img src="<?php echo e(asset('assets/icons-admin/caret-down.svg')); ?>" alt="icon" class="downIcon">
        </a>
        <div class="collapse dropdownMenuCollapse <?php echo e($request->routeIs('admin.customer.*') ? 'show' : ''); ?>"
            id="customerMenu">
            <div class="listBar">
                <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'admin.customer.index')): ?>
                    <a href="<?php echo e(route('admin.customer.index')); ?>"
                        class="subMenu hasCount <?php echo e(request()->routeIs('admin.customer.index') ? 'active' : ''); ?>">
                        <?php echo e(__('All Customer')); ?>

                    </a>
                <?php endif; ?>
                <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'admin.customer.create')): ?>
                    <a href="<?php echo e(route('admin.customer.create')); ?>"
                        class="subMenu hasCount <?php echo e(request()->routeIs('admin.customer.create') ? 'active' : ''); ?>">
                        <?php echo e(__('Add Customer')); ?>

                    </a>
                <?php endif; ?>
            </div>
        </div>
    </li>
<?php endif; ?>
<?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', ['admin.rider.index', 'admin.rider.create'])): ?>
    <!--- rider--->
    <li>
        <a class="menu <?php echo e(request()->routeIs('admin.rider.*') ? 'active' : ''); ?>" data-bs-toggle="collapse"
            href="#riderMenu">
            <span>
                <img class="menu-icon" src="<?php echo e(asset('assets/icons-admin/rider.svg')); ?>" alt="icon"
                    loading="lazy" />
                <?php echo e(__('Driver Management')); ?>

            </span>
            <img src="<?php echo e(asset('assets/icons-admin/caret-down.svg')); ?>" alt="icon" class="downIcon">
        </a>
        <div class="collapse dropdownMenuCollapse <?php echo e($request->routeIs('admin.rider.*') ? 'show' : ''); ?>"
            id="riderMenu">
            <div class="listBar">
                <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'admin.rider.index')): ?>
                    <a href="<?php echo e(route('admin.rider.index')); ?>"
                        class="subMenu hasCount <?php echo e(request()->routeIs('admin.rider.index') ? 'active' : ''); ?>">
                        <?php echo e(__('All Driver')); ?>

                    </a>
                <?php endif; ?>
                <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'admin.rider.create')): ?>
                    <a href="<?php echo e(route('admin.rider.create')); ?>"
                        class="subMenu hasCount <?php echo e(request()->routeIs('admin.rider.create') ? 'active' : ''); ?>">
                        <?php echo e(__('Add Driver')); ?>

                    </a>
                <?php endif; ?>
            </div>
        </div>
    </li>
<?php endif; ?>
<?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', ['admin.employee.index', 'admin.employee.create'])): ?>
    <!--- employees--->
    <li>
        <a class="menu <?php echo e(request()->routeIs('admin.employee.*') ? 'active' : ''); ?>" data-bs-toggle="collapse"
            href="#employeeMenu">
            <span>
                <img class="menu-icon" src="<?php echo e(asset('assets/icons-admin/employee.svg')); ?>" alt="icon"
                    loading="lazy" />
                <?php echo e(__('Employee Management')); ?>

            </span>
            <img src="<?php echo e(asset('assets/icons-admin/caret-down.svg')); ?>" alt="icon" class="downIcon">
        </a>
        <div class="collapse dropdownMenuCollapse <?php echo e($request->routeIs('admin.employee.*') ? 'show' : ''); ?>"
            id="employeeMenu">
            <div class="listBar">
                <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'admin.employee.index')): ?>
                    <a href="<?php echo e(route('admin.employee.index')); ?>"
                        class="subMenu hasCount <?php echo e(request()->routeIs('admin.employee.index') ? 'active' : ''); ?>">
                        <?php echo e(__('All Employee')); ?>

                    </a>
                <?php endif; ?>
                <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'admin.employee.create')): ?>
                    <a href="<?php echo e(route('admin.employee.create')); ?>"
                        class="subMenu hasCount <?php echo e(request()->routeIs('admin.employee.create') ? 'active' : ''); ?>">
                        <?php echo e(__('Add Employee')); ?>

                    </a>
                <?php endif; ?>
            </div>
        </div>
    </li>
<?php endif; ?>
<?php if($businessModel == 'multi'): ?>
    <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', ['admin.shop.index', 'admin.shop.create'])): ?>
        <!--- shop management--->
        <li>
            <a class="menu <?php echo e(request()->routeIs('admin.shop.*') ? 'active' : ''); ?>"
                data-bs-toggle="collapse" href="#shopMenu">
                <span>
                    <img class="menu-icon" src="<?php echo e(asset('assets/icons-admin/shop.svg')); ?>" alt="icon"
                        loading="lazy" />
                    <?php echo e(__('Shop Management')); ?>

                </span>
                <img src="<?php echo e(asset('assets/icons-admin/caret-down.svg')); ?>" alt="icon" class="downIcon">
            </a>
            <div class="collapse dropdownMenuCollapse <?php echo e($request->routeIs('admin.shop.*') ? 'show' : ''); ?>"
                id="shopMenu">
                <div class="listBar">

                    <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'admin.shop.index')): ?>
                        <a href="<?php echo e(route('admin.shop.index')); ?>"
                            class="subMenu hasCount <?php echo e(request()->routeIs('admin.shop.index') ? 'active' : ''); ?>">
                            <?php echo e(__('All Shop')); ?>

                        </a>
                    <?php endif; ?>
                    <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'admin.shop.create')): ?>
                        <a href="<?php echo e(route('admin.shop.create')); ?>"
                            class="subMenu hasCount <?php echo e(request()->routeIs('admin.shop.create') ? 'active' : ''); ?>">
                            <?php echo e(__('Add Shop')); ?>

                        </a>
                    <?php endif; ?>

                </div>
            </div>
        </li>
    <?php endif; ?>
<?php endif; ?>

<?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'shop.profile.index')): ?>
    <!--- profile--->
    <li>
            <a class="menu <?php echo e($request->routeIs('shop.profile.*') ? 'active' : ''); ?>"
                href="<?php echo e(route('shop.profile.index')); ?>">
                <span>
                    <img class="menu-icon" src="<?php echo e(asset('assets/icons-admin/user-circle.svg')); ?>" alt="icon"
                        loading="lazy" />
                    <?php echo e(__('My Profile')); ?>

                </span>
            </a>
        </li>
<?php endif; ?>

<?php if(module_exists('purchase') ): ?>
    <?php echo $__env->make('purchase::layouts.supplierSidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php endif; ?>

<?php if($businessModel == 'multi'): ?>
    <!--- admin Shop products --->
    <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', ['admin.product.index'])): ?>
        <li>
            <a class="menu <?php echo e(request()->routeIs('admin.product.index') ? 'active' : ''); ?>" data-bs-toggle="collapse"
                href="#shopProducts">
                <span>
                    <img class="menu-icon" src="<?php echo e(asset('assets/icons-admin/shop-product.svg')); ?>" alt="icon"
                        loading="lazy" />
                    <?php echo e(__('Shop Product Management')); ?>

                </span>
                <img src="<?php echo e(asset('assets/icons-admin/caret-down.svg')); ?>" alt="" class="downIcon">
            </a>
            <div class="collapse dropdownMenuCollapse <?php echo e($request->routeIs('admin.product.index') ? 'show' : ''); ?>"
                id="shopProducts">
                <div class="listBar">
                    <?php if($generaleSetting?->new_product_approval): ?>
                        <a href="<?php echo e(route('admin.product.index', 'status=0')); ?>"
                            class="subMenu <?php echo e(request()->filled('status') && request()->status == 0 ? 'active' : ''); ?>"
                            title="<?php echo e(__('Pending Product')); ?>">
                            <?php echo e(__('Pending Product')); ?>

                        </a>
                    <?php endif; ?>

                    <?php if($generaleSetting?->update_product_approval): ?>
                        <a href="<?php echo e(route('admin.product.index', 'status=1')); ?>"
                            class="subMenu <?php echo e(request()->filled('status') && request()->status == 1 ? 'active' : ''); ?>"
                            title="<?php echo e(__('Update Request Product')); ?>">
                            <?php echo e(__('Update Product Request')); ?>

                        </a>
                    <?php endif; ?>

                    <a href="<?php echo e(route('admin.product.index', 'approve=true')); ?>"
                        class="subMenu <?php echo e(request()->filled('approve') && request()->approve == 'true' ? 'active' : ''); ?>"
                        title="<?php echo e(__('Accepted Item')); ?>">
                        <?php echo e(__('Accepted Product')); ?>

                    </a>
                </div>
            </div>
        </li>
    <?php endif; ?>

    <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', [
        'admin.subscription-plan.index',
        'admin.subscription-plan.create',
        'admin.subscription-plan.subscription.list'
    ])): ?>
        <!--- subscription plans --->
        <li>
            <a class="menu <?php echo e(request()->routeIs('admin.subscription-plan.*') ? 'active' : ''); ?>"
                data-bs-toggle="collapse" href="#subscriptionMenu">
                <span>
                    <img class="menu-icon" src="<?php echo e(asset('assets/icons-admin/crown.svg')); ?>" alt="icon"
                        loading="lazy" />
                    <?php echo e(__('Subscription Management')); ?>

                </span>
                <img src="<?php echo e(asset('assets/icons-admin/caret-down.svg')); ?>" alt="icon" class="downIcon">
            </a>
            <div class="collapse dropdownMenuCollapse <?php echo e($request->routeIs('admin.subscription-plan.*') ? 'show' : ''); ?>"
                id="subscriptionMenu">
                <div class="listBar">
                    <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'admin.subscription-plan.subscription.list')): ?>
                        <a href="<?php echo e(route('admin.subscription-plan.subscription.list')); ?>"
                            class="subMenu hasCount <?php echo e(request()->routeIs('admin.subscription-plan.subscription.list') ? 'active' : ''); ?>">
                            <?php echo e(__('All Subscription')); ?>

                        </a>
                    <?php endif; ?>
                    <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'admin.subscription-plan.index')): ?>
                        <a href="<?php echo e(route('admin.subscription-plan.index')); ?>"
                            class="subMenu hasCount <?php echo e(request()->routeIs('admin.subscription-plan.index') ? 'active' : ''); ?>">
                            <?php echo e(__('Subscription Plan')); ?>

                        </a>
                    <?php endif; ?>
                    <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'admin.subscription-plan.create')): ?>
                        <a href="<?php echo e(route('admin.subscription-plan.create')); ?>"
                            class="subMenu hasCount <?php echo e(request()->routeIs('admin.subscription-plan.create') ? 'active' : ''); ?>">
                            <?php echo e(__('Add Subscription Plan')); ?>

                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </li>
    <?php endif; ?>
<?php endif; ?>
<?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', ['admin.supportTicket.index', 'admin.support.index'])): ?>
    <li>
        <a class="menu <?php echo e(request()->routeIs('admin.supportTicket.*', 'admin.support.*') ? 'active' : ''); ?>"
            data-bs-toggle="collapse" href="#supportMenu">
            <span>
                <img class="menu-icon" src="<?php echo e(asset('assets/icons-admin/3rd-config.svg')); ?>" alt="icon"
                    loading="lazy" />
                <?php echo e(__('Support Management')); ?>

            </span>
            <img src="<?php echo e(asset('assets/icons-admin/caret-down.svg')); ?>" alt="icon" class="downIcon">
        </a>
        <div class="collapse dropdownMenuCollapse <?php echo e($request->routeIs('admin.supportTicket.*', 'admin.support.*') ? 'show' : ''); ?>"
            id="supportMenu">
            <div class="listBar">
                <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'admin.supportTicket.index')): ?>
                    <a href="<?php echo e(route('admin.supportTicket.index')); ?>"
                        class="subMenu hasCount <?php echo e(request()->routeIs('admin.supportTicket.*') ? 'active' : ''); ?>">
                        <?php echo e(__('Help Requests')); ?>

                    </a>
                <?php endif; ?>
                <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'admin.support.index')): ?>
                    <a href="<?php echo e(route('admin.support.index')); ?>"
                        class="subMenu hasCount <?php echo e(request()->routeIs('admin.support.*') ? 'active' : ''); ?>">
                        <?php echo e(__('Help Notes')); ?>

                    </a>
                <?php endif; ?>
            </div>
        </div>
    </li>
<?php endif; ?>
<?php if($businessModel == 'multi'): ?>
    <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', ['admin.withdraw.index'])): ?>
        <!--- withdraw --->
        <li>
            <a class="menu <?php echo e($request->routeIs('admin.withdraw.*') ? 'active' : ''); ?>"
                href="<?php echo e(route('admin.withdraw.index')); ?>">
                <span>
                    <img class="menu-icon" src="<?php echo e(asset('assets/icons-admin/withdraw.svg')); ?>" alt="icon"
                        loading="lazy" />
                    <?php echo e(__('Withdrawal Management')); ?>

                </span>
            </a>
        </li>
    <?php endif; ?>
<?php endif; ?>

<?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', ['shop.bulk-product-export.index', 'shop.bulk-product-import.index', 'shop.gallery.index'])): ?>
    <!--- Import / Export --->
    <li>
        <a class="menu <?php echo e(request()->routeIs('shop.bulk-product-export.*', 'shop.bulk-product-import.*', 'shop.gallery.*') ? 'active' : ''); ?>"
            data-bs-toggle="collapse" href="#exportImportMenu">
            <span>
                <img class="menu-icon" src="<?php echo e(asset('assets/icons-admin/download.svg')); ?>" alt="icon"
                    loading="lazy" />
                <?php echo e(__('Import/Export')); ?>

            </span>
            <img src="<?php echo e(asset('assets/icons-admin/caret-down.svg')); ?>" alt="icon" class="downIcon">
        </a>
        <div class="collapse dropdownMenuCollapse <?php echo e($request->routeIs('shop.bulk-product-export.*', 'shop.bulk-product-import.*', 'shop.gallery.*') ? 'show' : ''); ?>"
            id="exportImportMenu">
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
<?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', ['admin.country.index', 'admin.area.index'])): ?>
    <!--- Address --->
    <li>
        <a class="menu <?php echo e(request()->routeIs('admin.country.*', 'admin.area.*') ? 'active' : ''); ?>"
            data-bs-toggle="collapse" href="#addressMenu">
            <span>
                <img class="menu-icon" src="<?php echo e(asset('assets/icons-admin/country.svg')); ?>" alt="icon"
                    loading="lazy" />
                <?php echo e(__('Address')); ?>

            </span>
            <img src="<?php echo e(asset('assets/icons-admin/caret-down.svg')); ?>" alt="icon" class="downIcon">
        </a>
        <div class="collapse dropdownMenuCollapse <?php echo e($request->routeIs('admin.country.*', 'admin.area.*') ? 'show' : ''); ?>"
            id="addressMenu">
            <div class="listBar">
                <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'admin.country.index')): ?>
                    <a href="<?php echo e(route('admin.country.index')); ?>"
                        class="subMenu hasCount <?php echo e(request()->routeIs('admin.country.*') ? 'active' : ''); ?>">
                        <?php echo e(__('Country')); ?>

                    </a>
                <?php endif; ?>
                <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'admin.area.index')): ?>
                    <a href="<?php echo e(route('admin.area.index')); ?>"
                        class="subMenu hasCount <?php echo e(request()->routeIs('admin.area.*') ? 'active' : ''); ?>">
                        <?php echo e(__('Area & Delivery')); ?>

                    </a>
                <?php endif; ?>
            </div>
        </div>
    </li>
<?php endif; ?>



<?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', ['admin.language.index'])): ?>
    <!--- Languages --->
    <li>
        <a href="<?php echo e(route('admin.language.index')); ?>"
            class="menu <?php echo e(request()->routeIs('admin.language.*') ? 'active' : ''); ?>">
            <span>
                <img class="menu-icon" src="<?php echo e(asset('assets/icons-admin/Language.svg')); ?>" alt="icon"
                    loading="lazy" />
                <?php echo e(__('Languages')); ?>

            </span>
        </a>
    </li>
<?php endif; ?>

<?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', [
    'admin.generale-setting.index',
    'admin.business-setting.index',
    'admin.socialLink.index',
    'admin.themeColor.index',
    'admin.deliveryCharge.index',
    'admin.ticketIssueType.index',
    'admin.verification.index',
    'admin.vatTax.index',
    'admin.currency.index',
    'admin.aiPrompt.index'
])): ?>
    <!--- Settings --->
    <li>
        <a class="menu <?php echo e(request()->routeIs('admin.generale-setting.*', 'admin.business-setting.*', 'admin.socialLink.*', 'admin.themeColor.*', 'admin.deliveryCharge.*', 'admin.ticketIssueType.*', 'admin.verification.*', 'admin.vatTax.*', 'admin.currency.*', 'admin.aiPrompt.index') ? 'active' : ''); ?>"
            data-bs-toggle="collapse" href="#settings">
            <span>
                <img class="menu-icon" src="<?php echo e(asset('assets/icons-admin/settings.svg')); ?>" alt="icon"
                    loading="lazy" />
                <?php echo e(__('Business Settings')); ?>

            </span>
            <img src="<?php echo e(asset('assets/icons-admin/caret-down.svg')); ?>" alt="" class="downIcon">
        </a>
        <div class="collapse dropdownMenuCollapse <?php echo e($request->routeIs('admin.generale-setting.*', 'admin.business-setting.*', 'admin.socialLink.*', 'admin.themeColor.*', 'admin.deliveryCharge.*', 'admin.ticketIssueType.*', 'admin.verification.*', 'admin.vatTax.*', 'admin.currency.*', 'admin.aiPrompt.index') ? 'show' : ''); ?>"
            id="settings">
            <div class="listBar">
                <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'admin.generale-setting.index')): ?>
                    <a href="<?php echo e(route('admin.generale-setting.index')); ?>"
                        class="subMenu <?php echo e(request()->routeIs('admin.generale-setting.index') ? 'active' : ''); ?>">
                        <?php echo e(__('General Settings')); ?>

                    </a>
                <?php endif; ?>

                <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'admin.business-setting.index')): ?>
                    <a href="<?php echo e(route('admin.business-setting.index')); ?>"
                        class="subMenu <?php echo e(request()->routeIs('admin.business-setting.*') ? 'active' : ''); ?>">
                        <?php echo e(__('Business Setup')); ?>

                    </a>
                <?php endif; ?>

                <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'admin.verification.index')): ?>
                    <a href="<?php echo e(route('admin.verification.index')); ?>"
                        class="subMenu <?php echo e(request()->routeIs('admin.verification.*') ? 'active' : ''); ?>">
                        <?php echo e(__('Manage Verification')); ?>

                    </a>
                <?php endif; ?>
                <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'admin.aiPrompt.index')): ?>
                    <a href="<?php echo e(route('admin.aiPrompt.index')); ?>"
                        class="subMenu <?php echo e(request()->routeIs('admin.aiPrompt.index') ? 'active' : ''); ?>">
                        <?php echo e(__('Ai Prompt')); ?>

                    </a>
                <?php endif; ?>

                <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'admin.currency.index')): ?>
                    <a href="<?php echo e(route('admin.currency.index')); ?>"
                        class="subMenu <?php echo e(request()->routeIs('admin.currency.*') ? 'active' : ''); ?>">
                        <?php echo e(__('Currency')); ?>

                    </a>
                <?php endif; ?>

                

                <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'admin.vatTax.index')): ?>
                    <a href="<?php echo e(route('admin.vatTax.index')); ?>"
                        class="subMenu <?php echo e(request()->routeIs('admin.vatTax.*') ? 'active' : ''); ?>">
                        <?php echo e(__('VAT & Tax')); ?>

                    </a>
                <?php endif; ?>

                <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'admin.themeColor.index')): ?>
                    <a href="<?php echo e(route('admin.themeColor.index')); ?>"
                        class="subMenu <?php echo e(request()->routeIs('admin.themeColor.*') ? 'active' : ''); ?>">
                        <?php echo e(__('Theme Colors')); ?>

                    </a>
                <?php endif; ?>

                <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'admin.socialLink.index')): ?>
                    <a href="<?php echo e(route('admin.socialLink.index')); ?>"
                        class="subMenu <?php echo e(request()->routeIs('admin.socialLink.index') ? 'active' : ''); ?>">
                        <?php echo e(__('Social Links')); ?>

                    </a>
                <?php endif; ?>

                <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'admin.ticketIssueType.index')): ?>
                    <a href="<?php echo e(url('/admin/ticket-issue-types')); ?>"
                        class="subMenu <?php echo e(request()->routeIs('admin.ticketIssueType.index') ? 'active' : ''); ?>">
                        <?php echo e(__('Ticket Issue Types')); ?>

                    </a>
                <?php endif; ?>
            </div>
        </div>
    </li>
<?php endif; ?>

<!--- cms --->
<?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', ['admin.menu.index', 'admin.page.index', 'admin.footer.index'])): ?>
    <li>
        <a class="menu <?php echo e(request()->routeIs('admin.menu.index*', 'admin.page.*', 'admin.footer.*') ? 'active' : ''); ?>"
            data-bs-toggle="collapse" href="#cms">
            <span>
                <img class="menu-icon" src="<?php echo e(asset('assets/icons-admin/legal.svg')); ?>" alt="icon"
                    loading="lazy" />
                <?php echo e(__('CMS')); ?>

            </span>
            <img src="<?php echo e(asset('assets/icons-admin/caret-down.svg')); ?>" alt="" class="downIcon">
        </a>
        <div class="collapse dropdownMenuCollapse <?php echo e($request->routeIs('admin.menu.*', 'admin.page.*', 'admin.footer.*') ? 'show' : ''); ?>"
            id="cms">
            <div class="listBar">
                <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'admin.page.index')): ?>
                    <a href="<?php echo e(route('admin.page.index')); ?>"
                        class="subMenu <?php echo e(request()->routeIs('admin.page.*') ? 'active' : ''); ?>">
                        <?php echo e(__('Pages')); ?>

                    </a>
                <?php endif; ?>

                <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'admin.menu.index')): ?>
                    <a href="<?php echo e(route('admin.menu.index')); ?>"
                        class="subMenu <?php echo e(request()->routeIs('admin.menu.index') ? 'active' : ''); ?>">
                        <?php echo e(__('Menus')); ?>

                    </a>
                <?php endif; ?>

                <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'admin.footer.index')): ?>
                    <a href="<?php echo e(route('admin.footer.index')); ?>"
                        class="subMenu <?php echo e(request()->routeIs('admin.footer.index') ? 'active' : ''); ?>">
                        <?php echo e(__('Footer')); ?>

                    </a>
                <?php endif; ?>
            </div>
        </div>
    </li>
<?php endif; ?>

<!--- third party configuration --->
<?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', [
    'admin.pusher.index',
    'admin.mailConfig.index',
    'admin.paymentGateway.index',
    'admin.sms-gateway.index',
    'admin.firebase.index',
    'admin.googleReCaptcha.index',
    'admin.aiPrompt.configure'
])): ?>
    <li>
        <a class="menu <?php echo e(request()->routeIs('admin.pusher.*', 'admin.mailConfig.*', 'admin.paymentGateway.*', 'admin.sms-gateway.*', 'admin.firebase.*', 'admin.googleReCaptcha.*', 'admin.aiPrompt.configure') ? 'active' : ''); ?>"
            data-bs-toggle="collapse" href="#thirdPartConfig" title="Third Party configuration">
            <span>
                <img class="menu-icon" src="<?php echo e(asset('assets/icons-admin/3rd-config.svg')); ?>" alt="icon"
                    loading="lazy" />
                <?php echo e(__('3rd Party Configuration')); ?>

            </span>
            <img src="<?php echo e(asset('assets/icons-admin/caret-down.svg')); ?>" alt="" class="downIcon">
        </a>
        <div class="collapse dropdownMenuCollapse <?php echo e($request->routeIs('admin.pusher.*', 'admin.mailConfig.*', 'admin.paymentGateway.*', 'admin.sms-gateway.*', 'admin.firebase.*', 'admin.googleReCaptcha.*', 'admin.aiPrompt.configure') ? 'show' : ''); ?>"
            id="thirdPartConfig">
            <div class="listBar">
                <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'admin.paymentGateway.index')): ?>
                    <a href="<?php echo e(route('admin.paymentGateway.index')); ?>"
                        class="subMenu <?php echo e(request()->routeIs('admin.paymentGateway.*') ? 'active' : ''); ?>">
                        <?php echo e(__('Payment Gateway')); ?>

                    </a>
                <?php endif; ?>

                <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'admin.sms-gateway.index')): ?>
                    <a href="<?php echo e(route('admin.sms-gateway.index')); ?>"
                        class="subMenu <?php echo e(request()->routeIs('admin.sms-gateway.*') ? 'active' : ''); ?>">
                        <?php echo e(__('SMS Gateway')); ?>

                    </a>
                <?php endif; ?>

                

                <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'admin.pusher.index')): ?>
                    <a href="<?php echo e(route('admin.pusher.index')); ?>"
                        class="subMenu <?php echo e(request()->routeIs('admin.pusher.*') ? 'active' : ''); ?>">
                        <?php echo e(__('Pusher Setup')); ?>

                    </a>
                <?php endif; ?>

                <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'admin.mailConfig.index')): ?>
                    <a href="<?php echo e(route('admin.mailConfig.index')); ?>"
                        class="subMenu <?php echo e(request()->routeIs('admin.mailConfig.*') ? 'active' : ''); ?>">
                        <?php echo e(__('Mail Config')); ?>

                    </a>
                <?php endif; ?>
                <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'admin.aiPrompt.configure')): ?>
                    <a href="<?php echo e(route('admin.aiPrompt.configure')); ?>"
                        class="subMenu <?php echo e(request()->routeIs('admin.aiPrompt.configure') ? 'active' : ''); ?>">
                        <?php echo e(__('OpenAI Config')); ?>

                    </a>
                <?php endif; ?>

                <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'admin.firebase.index')): ?>
                    <a href="<?php echo e(route('admin.firebase.index')); ?>"
                        class="subMenu <?php echo e(request()->routeIs('admin.firebase.*') ? 'active' : ''); ?>">
                        <?php echo e(__('Firebase Notification')); ?>

                    </a>
                <?php endif; ?>

                <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'admin.googleReCaptcha.index')): ?>
                    <a href="<?php echo e(route('admin.googleReCaptcha.index')); ?>"
                        class="subMenu <?php echo e(request()->routeIs('admin.googleReCaptcha.*') ? 'active' : ''); ?>">
                        <?php echo e(__('Google ReCaptcha')); ?>

                    </a>
                <?php endif; ?>
            </div>
        </div>
    </li>
<?php endif; ?>

<!--- roles and permissions --->
<?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', ['admin.role.index'])): ?>
    <li>
        <a class="menu <?php echo e($request->routeIs('admin.role.*') ? 'active' : ''); ?>"
            href="<?php echo e(route('admin.role.index')); ?>">
            <span>
                <img class="menu-icon" src="<?php echo e(asset('assets/icons-admin/role-permission.svg')); ?>" alt="icon"
                    loading="lazy" />
                <?php echo e(__('Roles & Permissions')); ?>

            </span>
        </a>
    </li>
<?php endif; ?>

<!--- contact us --->
<?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'admin.contactUs.index')): ?>
    <li>
        <a href="<?php echo e(route('admin.contactUs.index')); ?>"
            class="menu <?php echo e(request()->routeIs('admin.contactUs.*') ? 'active' : ''); ?>">
            <span>
                <img class="menu-icon" src="<?php echo e(asset('assets/icons-admin/contacts.svg')); ?>" alt="icon"
                    loading="lazy" />
                <?php echo e(__('Contact Us')); ?>

            </span>
        </a>
    </li>
<?php endif; ?>

<li>
    <a href="<?php echo e(route('marketplace.addons')); ?>"
        class="menu <?php echo e(request()->routeIs('marketplace.addons') ? 'active' : ''); ?>">
        <span>
            <img class="menu-icon" src="<?php echo e(asset('assets/icons-admin/plug.svg')); ?>" alt="icon"
                loading="lazy" />
            <?php echo e(__('Add-ons')); ?>

        </span>
    </a>
</li>

<?php /**PATH /home/u939461333/domains/janmitram.com/public_html/app/resources/views/layouts/partials/admin-menu.blade.php ENDPATH**/ ?>