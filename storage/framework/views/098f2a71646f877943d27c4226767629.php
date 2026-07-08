<?php $__env->startSection('header-title', __('Shops')); ?>

<?php $__env->startSection('content'); ?>
    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="w-100 page-title-heading d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div>
                    <?php echo e(__('Shops')); ?>

                    <div class="page-title-subheading">
                        <?php echo e(__('This is a shops list')); ?>

                    </div>
                </div>
                <div class="d-flex gap-2 align-items-center gap-md-4 flex-wrap">
                    <div class="d-flex gap-2 gap-md-3">
                        <button class="gridBtn" id="gridView" data-bs-toggle="tooltip" data-bs-placement="top"
                        data-bs-title="<?php echo e(__('Grid View')); ?>">
                            <i class="bi bi-grid-3x3-gap-fill"></i>
                        </button>
                        <button class="gridBtn" id="listView" data-bs-toggle="tooltip" data-bs-placement="top"
                            data-bs-title="<?php echo e(__('List View')); ?>">
                            <i class="fa-solid fa-list-ul"></i>
                        </button>
                    </div>

                    <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'admin.shop.create')): ?>
                    <a href="<?php echo e(route('admin.shop.create')); ?>" class="btn py-2 btn-primary">
                        <i class="fa fa-plus-circle"></i>
                        <?php echo e(__('Add Shop')); ?>

                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">

        <div class="row row-gap mb-4 d-none" id="gridItem">
            <?php $__currentLoopData = $shops; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $shop): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-12 col-md-6 col-xl-4 col-xxl-3">
                    <div class="card shadow-sm rounded-12 show-card position-relative overflow-hidden">
                        <div class="card-body shop p-2">
                            <div class="banner">
                                <img class="img-fit" src="<?php echo e($shop->banner); ?>" />
                            </div>
                            <div class="main-content">
                                <div class="logo">
                                    <img class="img-fit" src="<?php echo e($shop->logo); ?>" />
                                </div>
                                <div class="personal">
                                    <span class="name"><?php echo e($shop->name); ?></span>
                                    <span class="email"><?php echo e($shop->user?->email); ?></span>
                                </div>
                            </div>
                            <div class="d-flex flex-column gap-2 px-3 mt-2">
                                <div class="item">
                                    <strong><?php echo e(__('Status')); ?></strong>
                                    <label class="switch mb-0" data-bs-toggle="tooltip" data-bs-placement="left"
                                        data-bs-title="<?php echo e(__('Click here to change status')); ?>">
                                        <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'admin.shop.status.toggle')): ?>
                                            <a href="<?php echo e(route('admin.shop.status.toggle', $shop->id)); ?>">
                                                <input type="checkbox" <?php echo e($shop->user?->is_active ? 'checked' : ''); ?>>
                                                <span class="slider round"></span>
                                            </a>
                                        <?php else: ?>
                                            <input type="checkbox" <?php echo e($shop->user?->is_active ? 'checked' : ''); ?>>
                                        <?php endif; ?>
                                    </label>
                                </div>
                                <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'admin.shop.products')): ?>
                                <div class="item">
                                    <strong><?php echo e(__('Products')); ?></strong>
                                    <a href="<?php echo e(route('admin.shop.products', $shop->id)); ?>" class="btn btn-secondary btn-sm"
                                        data-bs-toggle="tooltip" data-bs-placement="left"
                                        data-bs-title="<?php echo e(__('Click here to see products')); ?>">
                                        <?php echo e($shop->products->count()); ?>

                                    </a>
                                </div>
                                <?php endif; ?>

                                <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'admin.shop.orders')): ?>
                                <div class="item">
                                    <strong><?php echo e(__('Orders')); ?></strong>
                                    <a href="<?php echo e(route('admin.shop.orders', $shop->id)); ?>" class="btn btn-primary btn-sm"
                                        data-bs-toggle="tooltip" data-bs-placement="left"
                                        data-bs-title="<?php echo e(__('Click here to see orders')); ?>">
                                        <?php echo e($shop->orders->count()); ?>

                                    </a>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="overlay">
                            <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'admin.shop.edit')): ?>
                            <a class="icons btn-outline-info" href="<?php echo e(route('admin.shop.edit', $shop->id)); ?>" data-bs-toggle="tooltip"
                                data-bs-placement="top" data-bs-title="Edit">
                                <img src="<?php echo e(asset('assets/icons-admin/edit.svg')); ?>" alt="edit" loading="lazy" />
                            </a>
                            <?php endif; ?>
                            <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'admin.shop.show')): ?>
                            <a class="icons svg-bg" href="<?php echo e(route('admin.shop.show', $shop->id)); ?>" data-bs-toggle="tooltip"
                                data-bs-placement="top" data-bs-title="View">
                                <img src="<?php echo e(asset('assets/icons-admin/eye.svg')); ?>" alt="view" loading="lazy" />
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <div class="mb-4 d-none" id="listItem">
            <div class="table-responsive">

                <table class="table shopTable table-striped table-responsive-lg">
                    <thead>
                        <tr>
                            <th><?php echo e(__('SL')); ?></th>
                            <th><?php echo e(__('Logo')); ?></th>
                            <th><?php echo e(__('Name')); ?></th>
                            <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'admin.shop.status.toggle')): ?>
                            <th><?php echo e(__('Status')); ?></th>
                            <?php endif; ?>
                            <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'admin.shop.products')): ?>
                            <th class="text-center"><?php echo e(__('Products')); ?></th>
                            <?php endif; ?>
                            <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'admin.shop.orders')): ?>
                            <th class="text-center"><?php echo e(__('Orders')); ?></th>
                            <?php endif; ?>
                            <th class="text-center"><?php echo e(__('Action')); ?></th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php $__currentLoopData = $shops; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $shop): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e(++$key); ?></td>
                                <td>
                                    <div class="payment-image">
                                        <img class="img-fit" src="<?php echo e($shop->logo); ?>" />
                                    </div>
                                </td>
                                <td><?php echo e($shop->name); ?></td>
                                <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'admin.shop.status.toggle')): ?>
                                <td>
                                    <label class="switch mb-0" data-bs-toggle="tooltip" data-bs-placement="top" title="<?php echo e(__('Click here to change status')); ?>">
                                        <a href="<?php echo e(route('admin.shop.status.toggle', $shop->id)); ?>">
                                            <input type="checkbox" <?php echo e($shop->user?->is_active ? 'checked' : ''); ?>>
                                            <span class="slider round"></span>
                                        </a>
                                    </label>
                                </td>
                                <?php endif; ?>
                                <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'admin.shop.products')): ?>
                                <td class="text-center">
                                    <a href="<?php echo e(route('admin.shop.products', $shop->id)); ?>" class="badge badge-square badge-primary" data-bs-toggle="tooltip"
                                        data-bs-placement="top" title="<?php echo e(__('Click here to view total products')); ?>">
                                        <?php echo e($shop->products->count()); ?>

                                    </a>
                                </td>
                                <?php endif; ?>
                                <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'admin.shop.orders')): ?>
                                <td class="text-center">
                                    <a href="<?php echo e(route('admin.shop.orders', $shop->id)); ?>"
                                        class="badge badge-square badge-info" data-bs-toggle="tooltip"
                                        data-bs-placement="top" title="<?php echo e(__('Click here to view total orders')); ?>">
                                        <?php echo e($shop->orders->count()); ?>

                                    </a>
                                </td>
                                <?php endif; ?>
                                <td class="text-center">
                                    <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'admin.shop.show')): ?>
                                    <a class="svg-bg circleIcon"
                                        href="<?php echo e(route('admin.shop.show', $shop->id)); ?>" data-bs-toggle="tooltip"
                                        data-bs-placement="top" title="View">
                                        <img src="<?php echo e(asset('assets/icons-admin/eye.svg')); ?>" alt="edit" loading="lazy" />
                                    </a>
                                    <?php endif; ?>
                                    <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'admin.shop.edit')): ?>
                                    <a href="<?php echo e(route('admin.shop.edit', $shop->id)); ?>"
                                        class="btn-outline-info circleIcon" data-bs-toggle="tooltip"
                                        data-bs-placement="top" title="Edit">
                                        <img src="<?php echo e(asset('assets/icons-admin/edit.svg')); ?>" alt="edit" loading="lazy" />
                                    </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>

            </div>
        </div>

        <div class="my-3">
            <?php echo e($shops->links()); ?>

        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/u939461333/domains/janmitram.com/public_html/app/resources/views/admin/shop/index.blade.php ENDPATH**/ ?>