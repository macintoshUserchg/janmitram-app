<?php $__env->startSection('header-title', __('Subscription List')); ?>

<?php $__env->startSection('content'); ?>
    <div>
        <h4><?php echo e(__('Subscription List')); ?></h4>
    </div>

    <form action="" method="GET" class="card card-body">

        <div class="row">
            <div class="col-lg-4 col-md-6 mb-3">
                <?php if (isset($component)) { $__componentOriginalbf566fc26595b9cc6779e170beef8a5a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalbf566fc26595b9cc6779e170beef8a5a = $attributes; } ?>
<?php $component = App\View\Components\Select::resolve(['label' => 'Shop','name' => 'shop'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\Select::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
                    <option value="">
                        <?php echo e(__('All Shop')); ?>

                    </option>
                    <?php $__currentLoopData = $shops; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $shop): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($shop->id); ?>" <?php echo e(request('shop') == $shop->id ? 'selected' : ''); ?>>
                            <?php echo e($shop->name); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalbf566fc26595b9cc6779e170beef8a5a)): ?>
<?php $attributes = $__attributesOriginalbf566fc26595b9cc6779e170beef8a5a; ?>
<?php unset($__attributesOriginalbf566fc26595b9cc6779e170beef8a5a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbf566fc26595b9cc6779e170beef8a5a)): ?>
<?php $component = $__componentOriginalbf566fc26595b9cc6779e170beef8a5a; ?>
<?php unset($__componentOriginalbf566fc26595b9cc6779e170beef8a5a); ?>
<?php endif; ?>
            </div>
        </div>

        <div class="mt-2 d-flex gap-2 justify-content-end flex-wrap">
            <a href="<?php echo e(route('admin.subscription-plan.subscription.list')); ?>" class="btn btn-light py-2 px-4">
                <?php echo e(__('Reset')); ?>

            </a>
            <button type="submit" class="btn btn-primary py-2 px-4">
                <?php echo e(__('Filter Data')); ?>

            </button>
        </div>
    </form>

    <div class="container-fluid mt-3">

        <div class="mb-3 card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table border-left-right table-responsive-lg">
                        <thead>
                            <tr>
                                <th class="text-center"><?php echo e(__('SL')); ?>.</th>
                                <th><?php echo e(__('Plan')); ?></th>
                                <th class="text-center"><?php echo e(__('Shop')); ?></th>
                                <th class="text-center"><?php echo e(__('Price')); ?></th>
                                <th class="text-center"><?php echo e(__('Duration')); ?></th>
                                <th class="text-center" style="min-width: 120px"><?php echo e(__('Sale Limit')); ?></th>
                                <th class="text-center" style="min-width: 120px"><?php echo e(__('Remaining Sales')); ?></th>
                                <th class="text-center" style="min-width: 120px"><?php echo e(__('Payment Method')); ?></th>
                                <th class="text-center"><?php echo e(__('Status')); ?></th>
                            </tr>
                        </thead>
                        <?php $__empty_1 = true; $__currentLoopData = $subscriptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $subscription): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td class="text-center"><?php echo e(++$key); ?></td>

                                <td>
                                    <?php echo e($subscription->plan?->name ?? ''); ?>

                                </td>

                                <td><?php echo e($subscription->shop->name); ?></td>

                                <td class="text-center">
                                    <?php echo e(showCurrency($subscription->price)); ?>

                                </td>

                                <td class="text-center">
                                    <?php echo e($subscription->duration); ?>

                                </td>
                                <td class="text-center">
                                    <?php echo e($subscription->sale_limit); ?>

                                </td>
                                <td class="text-center">
                                    <?php echo e($subscription->remaining_sales); ?>

                                </td>
                                <td class="text-center">
                                    <?php echo e($subscription->payment->payment_method); ?>

                                </td>

                                <td class="text-center">
                                    <?php if($subscription->status == 'pending'): ?>
                                        <div class="d-flex gap-3 justify-content-center">
                                            <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'admin.subscription-plan.subscription.status')): ?>
                                                <a href="<?php echo e(route('admin.subscription-plan.subscription.status', $subscription->id)); ?>"
                                                    class="btn btn-danger btn-sm confirmApprove"><?php echo e(__('Pending')); ?></a>
                                            <?php endif; ?>
                                        </div>
                                    <?php else: ?>
                                        <a class="btn btn-success btn-sm "><?php echo e(__('Approved')); ?></a>
                                    <?php endif; ?>

                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td class="text-center" colspan="100%"><?php echo e(__('No Data Found')); ?></td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="my-3">
            <?php echo e($subscriptions->withQueryString()->links()); ?>

        </div>

        <form action="" method="POST" class="d-none" id="deleteForm">
            <?php echo csrf_field(); ?>
            <?php echo method_field('DELETE'); ?>
        </form>

    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        $(".confirmApprove").on("click", function(e) {
            e.preventDefault();
            const url = $(this).attr("href");
            Swal.fire({
                title: "Are you sure?",
                text: "You want to approve this Subscription",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, Approve it!",
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/u939461333/domains/janmitram.com/public_html/app/resources/views/admin/subscription-plan/list.blade.php ENDPATH**/ ?>