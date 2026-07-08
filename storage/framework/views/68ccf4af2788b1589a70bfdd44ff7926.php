<?php $__env->startSection('header-title', __('Orders')); ?>

<?php $__env->startSection('content'); ?>
    <div class="admin-return-order-index">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive return-order-table-wrap">

                    <table class="table border-left-right return-order-table w-100">
                        <thead>
                            <tr>
                                <th style="min-width: 85px"><?php echo e(__('Order ID')); ?></th>
                                <th><?php echo e(__('Return Date')); ?></th>
                                <th><?php echo e(__('Customer')); ?></th>
                                <?php if($businessModel == 'multi'): ?>
                                    <th><?php echo e(__('Shop')); ?></th>
                                <?php endif; ?>
                                <th><?php echo e(__('Amount')); ?></th>
                                <th><?php echo e(__('Status')); ?></th>
                                <th><?php echo e(__('Payment Status')); ?></th>
                                <th><?php echo e(__('Action')); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $returnOrder; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td class="w-auto return-order-code-cell"><?php echo e($order->order->prefix . $order->order->order_code); ?></td>
                                    <td class="w-min return-order-date-cell"><?php echo e($order->created_at->format('d M Y, h:i A')); ?></td>
                                    <td class="w-min return-order-customer-cell"><?php echo e($order->customer?->user?->name); ?></td>

                                    <?php if($businessModel == 'multi'): ?>
                                        <td class="w-min return-order-shop-cell">
                                            <?php echo e($order->shop?->name); ?>

                                        </td>
                                    <?php endif; ?>
                                    <td class="w-min return-order-amount-cell">
                                        <?php echo e(showCurrency($order->amount)); ?>

                                    </td>
                                    <td class="w-min return-order-status-cell">
                                        <?php echo e($order->status); ?>

                                    </td>
                                    <td class="return-order-payment-cell">
                                        <button class="badge rounded-pill text-bg-<?php echo e($order->payment_status ? 'success' : 'danger'); ?>"><?php echo e($order->payment_status ? 'Paid' : 'Unpaid'); ?></button>
                                    </td>
                                    <td class="w-min return-order-action-cell">
                                        <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'admin.returnOrder.show')): ?>
                                            <a href="<?php echo e(route('admin.returnOrder.show', $order->id)); ?>" data-bs-toggle="tooltip"
                                                data-bs-placement="top" data-bs-title="<?php echo e(__('view details')); ?>"
                                                class="circleIcon svg-bg">
                                                <img src="<?php echo e(asset('assets/icons-admin/eye.svg')); ?>" alt="icon"
                                                    loading="lazy" />
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="100%" class="text-center">
                                        <?php echo e(__('No order found')); ?>

                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>

                </div>

            </div>
        </div>

        <div class="my-3 return-order-pagination">
            <?php echo e($returnOrder->links()); ?>

        </div>
    </div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('css'); ?>
    <style>
        .admin-return-order-index .return-order-table {
            width: max-content;
            min-width: 820px;
        }

        .admin-return-order-index .return-order-table-wrap {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .admin-return-order-index .return-order-table-wrap::-webkit-scrollbar {
            height: 8px;
        }

        .admin-return-order-index .return-order-table-wrap::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 999px;
        }

        .admin-return-order-index .return-order-table-wrap::-webkit-scrollbar-track {
            background: transparent;
        }

        .admin-return-order-index .return-order-table {
            min-width: 820px;
        }

        .admin-return-order-index .return-order-table td,
        .admin-return-order-index .return-order-table th {
            vertical-align: middle;
        }

        .admin-return-order-index .return-order-code-cell,
        .admin-return-order-index .return-order-date-cell,
        .admin-return-order-index .return-order-customer-cell,
        .admin-return-order-index .return-order-shop-cell,
        .admin-return-order-index .return-order-amount-cell,
        .admin-return-order-index .return-order-status-cell {
            white-space: nowrap;
        }

        .admin-return-order-index .return-order-payment-cell .badge {
            white-space: nowrap;
        }

        .admin-return-order-index .return-order-action-cell {
            white-space: nowrap;
        }

        .admin-return-order-index .return-order-action-cell .circleIcon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .admin-return-order-index .return-order-pagination .pagination {
            flex-wrap: wrap;
            gap: 8px;
        }

        
        @media (max-width: 991.98px) {
            .admin-return-order-index .card-body {
                padding: 1rem;
            }

            .admin-return-order-index .return-order-table {
                min-width: 760px;
            }
        }

        @media (max-width: 767.98px) {
            .admin-return-order-index .return-order-table {
                min-width: 680px;
            }

            .admin-return-order-index .return-order-payment-cell .badge {
                font-size: 12px;
            }
        }

        @media (max-width: 575.98px) {
            .admin-return-order-index .card-body {
                padding: 0.875rem;
            }

            .admin-return-order-index .return-order-table {
                min-width: 620px;
            }
        }
    </style>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/u939461333/domains/janmitram.com/public_html/app/resources/views/admin/returnOrder/index.blade.php ENDPATH**/ ?>