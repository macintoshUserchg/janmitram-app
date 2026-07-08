<?php $__env->startSection('header-title', __('Orders')); ?>

<?php $__env->startSection('content'); ?>
    <div class="admin-order-index">
        <div class="card">
            <div class="card-body">
                <ul class="nav nav-tabs order-tabs">
                <?php
                    use App\Enums\OrderStatus;
                    $orderStatuses = OrderStatus::cases();
                ?>


                        <li class="nav-item">
                        <a href="<?php echo e(route('admin.order.index')); ?>"
                            class="nav-link <?php echo e(request()->url() === route('admin.order.index') ? 'active' : ''); ?>">
                            <?php echo e(__('All')); ?>

                            
                        </a>
                        </li>

                        <?php $__currentLoopData = $orderStatuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                        <li class="nav-item">
                            <a href="<?php echo e(route('admin.order.index', str_replace(' ', '_', $status->value))); ?>"
                                class="nav-link <?php echo e(request()->url() === route('admin.order.index', str_replace(' ', '_', $status->value)) ? 'active' : ''); ?>">
                                <span><?php echo e(__($status->value)); ?></span>
                            </a>
                        </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                </ul>
                <div class="table-responsive">

                    <table class="table border-left-right table-responsive-lg order-index-table">
                        <thead>
                            <tr>
                                <th style="min-width: 85px"><?php echo e(__('Order ID')); ?></th>
                                <th><?php echo e(__('Order Date')); ?></th>
                                <th><?php echo e(__('Customer')); ?></th>
                                <?php if($businessModel == 'multi'): ?>
                                    <th><?php echo e(__('Shop')); ?></th>
                                <?php endif; ?>
                                <th><?php echo e(__('Total Amount')); ?></th>
                                <th><?php echo e(__('Payment Method')); ?></th>
                                <th><?php echo e(__('Action')); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td class="w-auto order-code-cell"><?php echo e($order->prefix . $order->order_code); ?></td>
                                    <td class="w-min order-date-cell"><?php echo e($order->created_at->format('d M Y, h:i A')); ?></td>
                                    <td class="w-min order-customer-cell"><?php echo e($order->customer?->user?->name); ?></td>

                                    <?php if($businessModel == 'multi'): ?>
                                        <td class="w-min order-shop-cell">
                                            <?php echo e($order->shop?->name); ?>

                                        </td>
                                    <?php endif; ?>
                                    <td class="w-min order-amount-cell">
                                        <?php echo e(showCurrency($order->payable_amount)); ?>

                                        <br>
                                        <span class="badge rounded-pill text-bg-primary order-payment-badge"><?php echo e($order->payment_status); ?></span>
                                    </td>
                                    <td class="w-min order-method-cell"><?php echo e($order->payment_method); ?></td>
                                    <td class="w-min order-action-cell">
                                        <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'admin.order.show')): ?>
                                            <a href="<?php echo e(route('admin.order.show', $order->id)); ?>" data-bs-toggle="tooltip"
                                                data-bs-placement="top" data-bs-title="<?php echo e(__('view details')); ?>"
                                                class="circleIcon svg-bg">
                                                <img src="<?php echo e(asset('assets/icons-admin/eye.svg')); ?>" alt="icon"
                                                    loading="lazy" />
                                            </a>
                                        <?php endif; ?>
                                        <a href="<?php echo e(route('shop.download-invoice', $order->id)); ?>" data-bs-toggle="tooltip"
                                            data-bs-placement="left" data-bs-title="<?php echo e(__('Download Invoice')); ?>"
                                            class="circleIcon btn-outline-secondary">
                                            <img src="<?php echo e(asset('assets/icons-admin/download-alt.svg')); ?>" alt="icon"
                                                loading="lazy" />
                                        </a>
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

        <div class="my-3 order-pagination">
            <?php echo e($orders->links()); ?>

        </div>
    </div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('css'); ?>
    <style>
        .admin-order-index .order-tabs {
            gap: 10px;
            margin-bottom: 1rem;
            border-bottom: 0;
            flex-wrap: wrap;
        }

        .admin-order-index .order-tabs .nav-item {
            flex: 0 0 auto;
        }

        .admin-order-index .order-tabs .nav-link {
            border: 1px solid #d7dae0;
            border-radius: 999px;
            padding: 0.55rem 0.9rem;
            white-space: nowrap;
        }

        .admin-order-index .order-index-table {
            min-width: 760px;
        }

        .admin-order-index .order-index-table td,
        .admin-order-index .order-index-table th {
            vertical-align: middle;
        }

        .admin-order-index .order-code-cell,
        .admin-order-index .order-date-cell,
        .admin-order-index .order-customer-cell,
        .admin-order-index .order-shop-cell,
        .admin-order-index .order-method-cell {
            white-space: nowrap;
        }

        .admin-order-index .order-amount-cell {
            min-width: 140px;
        }

        .admin-order-index .order-payment-badge {
            margin-top: 6px;
            display: inline-flex;
        }

        .admin-order-index .order-action-cell {
            white-space: nowrap;
        }

        .admin-order-index .order-action-cell .circleIcon,
        .admin-order-index .order-action-cell .btn-outline-secondary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .admin-order-index .order-pagination .pagination {
            flex-wrap: wrap;
            gap: 8px;
        }

        @media (max-width: 991.98px) {
            .admin-order-index .card-body {
                padding: 1rem;
            }

            .admin-order-index .order-tabs {
                gap: 8px;
            }

            .admin-order-index .order-index-table {
                min-width: 700px;
            }
        }

        @media (max-width: 767.98px) {
            .admin-order-index .order-tabs {
                flex-wrap: nowrap;
                overflow-x: auto;
                padding-bottom: 4px;
                scrollbar-width: thin;
            }

            .admin-order-index .order-tabs .nav-link {
                padding: 0.5rem 0.85rem;
                font-size: 14px;
            }

            .admin-order-index .order-index-table {
                min-width: 640px;
            }

            .admin-order-index .order-action-cell {
                display: flex;
                flex-wrap: wrap;
                gap: 8px;
            }

            .admin-order-index .order-payment-badge {
                white-space: normal;
            }
        }

        @media (max-width: 575.98px) {
            .admin-order-index .card-body {
                padding: 0.875rem;
            }

            .admin-order-index .order-tabs {
                margin-bottom: 0.875rem;
            }

            .admin-order-index .order-tabs .nav-link {
                font-size: 13px;
            }

            .admin-order-index .order-index-table {
                min-width: 560px;
            }

            .admin-order-index .order-amount-cell {
                min-width: 120px;
            }
        }
    </style>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/u939461333/domains/janmitram.com/public_html/app/resources/views/admin/order/index.blade.php ENDPATH**/ ?>