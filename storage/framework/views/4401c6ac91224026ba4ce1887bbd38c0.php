<?php $__env->startSection('header-title', __('Profile Details')); ?>

<?php $__env->startSection('content'); ?>
    <div>
        <h4>
            <?php echo e(__('Profile Details')); ?>

        </h4>
    </div>

    <div class="row mb-3">
        <div class="col-lg-8 mt-3">
            <div class="card rounded-12 position-relative overflow-hidden">
                <div class="card-body shop details p-2 border-bottom pb-3">
                    <div class="banner position-relative">
                        <img class="img-fit" src="<?php echo e($shop->banner); ?>" />
                    </div>
                    <a href="<?php echo e(route('shop.profile.edit', $shop->id)); ?>" class="editBtn svg-bg">
                        <img src="<?php echo e(asset('assets/icons-admin/edit.svg')); ?>" alt="edit" loading="lazy" />
                        <span><?php echo e(__('Edit')); ?></span>
                    </a>
                    <div class="main-content d-flex align-items-start align-items-md-center flex-column flex-md-row gap-2">
                        <div class="logo">
                            <img class="img-fit" src="<?php echo e($shop->logo); ?>" />
                        </div>
                        <div class="personal">
                            <span class="name h4 mb-1"><?php echo e($shop->name); ?></span>
                            <div class="d-flex gap-2 align-items-center ">
                                <div>
                                    <?php $__currentLoopData = range(1, 5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rating): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php if($shop->averageRating >= $rating): ?>
                                            <i class="fa-solid fa-star text-warning"></i>
                                        <?php else: ?>
                                            <i class="fa-regular fa-star text-secondary"></i>
                                        <?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                                <div>
                                    <span class="fw-bold"><?php echo e($shop->averageRating); ?></span>
                                    (<?php echo e($shop->reviews->count()); ?> <?php echo e(__('Reviews')); ?>)
                                </div>
                            </div>
                            <div class="mt-2">
                                <a href="/shops/<?php echo e($shop->id); ?>" target="blank"
                                    class="btn btn-outline-primary btn-sm">
                                    <?php echo e(__('View Live')); ?>

                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <h4 class="m-0 p-3 border-bottom"><?php echo e(__('User Information')); ?></h4>
                <div class="card-body pt-0" style="overflow-x:auto">
                    <table class="table mb-0 table-responsive">
                        <tr>
                            <td style="width: 180px"><?php echo e(__('Name')); ?>:</td>
                            <td><?php echo e($shop->user?->name); ?></td>
                        </tr>
                        <tr>
                            <td style="width: 180px"><?php echo e(__('Phone')); ?>:</td>
                            <td><?php echo e($shop->user?->phone); ?></td>
                        </tr>
                        <tr>
                            <td style="width: 180px"><?php echo e(__('Email')); ?>:</td>
                            <td><?php echo e($shop->user?->email); ?></td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="card mt-3">
                <h4 class="m-0 p-3 border-bottom"><?php echo e(__('Shop Information')); ?></h4>
                <div class="card-body pt-0" style="overflow-x:auto">
                    <table class="table mb-0 table-responsive">
                        <tr>
                            <td style="width: 180px"><?php echo e(__('Name')); ?>:</td>
                            <td><?php echo e($shop->name); ?></td>
                        </tr>
                        <tr>
                            <td style="width: 180px"><?php echo e(__('Estimated Delivery')); ?>:</td>
                            <td><?php echo e($shop->estimated_delivery_time); ?></td>
                        </tr>
                        <tr>
                            <td style="width: 180px"><?php echo e(__('Shop Description')); ?>:</td>
                            <td><?php echo e($shop->description); ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-4 mt-3">
            <div class="card h-100">
                <h4 class="m-0 p-3 border-bottom"><?php echo e(__('Product Information')); ?></h4>
                <div class="card-body pt-0" style="overflow-x:auto">
                    <table class="table mb-0 table-responsive">
                        <tr>
                            <td style="width: 180px"><?php echo e(__('Total Products')); ?>:</td>
                            <td>
                                <span class="fw-bold"><?php echo e($shop->products->count()); ?></span>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 180px"><?php echo e(__('Total Orders')); ?>:</td>
                            <td>
                                <span class="fw-bold"><?php echo e($shop->orders->count()); ?></span>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 180px; text-transform: capitalize"><?php echo e(__('reviews')); ?></td>
                            <td>
                                <span class="fw-bold"><?php echo e($shop->reviews->count()); ?></span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/u939461333/domains/janmitram.com/public_html/app/resources/views/shop/profile/index.blade.php ENDPATH**/ ?>