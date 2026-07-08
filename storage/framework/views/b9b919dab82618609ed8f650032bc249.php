<div class="app-sidebar">
    <div class="scrollbar-sidebar" style=" overflow-y: auto;
    overflow-x: hidden;">
        <button type="button" class="sidebar-mobile-close" data-class="closed-sidebar">
            <i class="fa fa-times" aria-hidden="true"></i>
        </button>
        <div class="branding-logo">
            <?php
                $request = request();

                $shop = generaleSetting('shop');
                $rootShop = generaleSetting('rootShop');
                $isAdmin = $shop->id == $rootShop->id ? true : false;

                $url = $isAdmin ? route('admin.dashboard.index') : route('shop.dashboard.index');
            ?>
            <a href="<?php echo e($url); ?>">
                <img src="<?php echo e($generaleSetting?->logo ?? asset('assets/logo.png')); ?>" alt="logo" loading="lazy" />
            </a>
        </div>
        <div class="branding-logo-forMobile">
            <a href="<?php echo e($generaleSetting?->logo ?? asset('assets/logo.png')); ?>"></a>
        </div>
        <div class="app-sidebar-inner">
            <ul class="vertical-nav-menu">
                <?php if($isAdmin): ?>
                    <?php echo $__env->make('layouts.partials.admin-menu', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php else: ?>
                    <?php echo $__env->make('layouts.partials.shop-menu', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php endif; ?>
            </ul>
        </div>
        <div class="sideBarfooter">
            <button type="button" class="fullbtn hite-icon" onclick="toggleFullScreen(document.body)">
                <img src="<?php echo e(asset('assets/icons-admin/expand.svg')); ?>" alt="icon" loading="lazy" />
            </button>
            <?php if($isAdmin): ?>
                <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'admin.profile.index')): ?>
                    <a href="<?php echo e(route('admin.profile.index')); ?>" class="fullbtn hite-icon">
                        <img src="<?php echo e(asset('assets/icons-admin/user-circle.svg')); ?>" alt="">
                    </a>
                <?php endif; ?>
            <?php else: ?>
                <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'shop.profile.index')): ?>
                    <a href="<?php echo e(route('shop.profile.index')); ?>" class="fullbtn hite-icon">
                        <img src="<?php echo e(asset('assets/icons-admin/user-circle.svg')); ?>" alt="">
                    </a>
                <?php endif; ?>
            <?php endif; ?>

            <?php if (\Illuminate\Support\Facades\Blade::check('role', 'root')): ?>
            <a href="<?php echo e(route('marketplace.index')); ?>">
                <img src="<?php echo e(asset('assets/icons-admin/shop.svg')); ?>" alt="icon" loading="lazy" />
            </a>
            <a href="<?php echo e(route('marketplace.upgrade')); ?>" class="fullbtn hite-icon">
                <small style="font-size: 10px; color: #888;">
                    <?php echo e(config('app.version')); ?>

                </small>
            </a>
            <?php else: ?>
            <a href="javascript:void(0)" class="fullbtn hite-icon logout">
                <img src="<?php echo e(asset('assets/icons-admin/log-out.svg')); ?>" alt="icon" loading="lazy" />
            </a>
            <a href="javascript:void(0)" class="fullbtn hite-icon">
                <small style="font-size: 10px; color: #888;">
                    <?php echo e(config('app.version')); ?>

                </small>
            </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php /**PATH /home/u939461333/domains/janmitram.com/public_html/app/resources/views/layouts/sidebar.blade.php ENDPATH**/ ?>