<?php $__env->startSection('header-title', __('Product List')); ?>

<?php $__env->startSection('content'); ?>
    <div>
        <h4><?php echo e(__('Product List')); ?></h4>
    </div>

    <form action="" method="GET" class="card card-body">

        <?php if(request('approve')): ?>
            <input type="hidden" name="approve" value="<?php echo e(request('approve')); ?>">
        <?php else: ?>
            <input type="hidden" name="status" value="<?php echo e(request('status')); ?>">
        <?php endif; ?>

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
            <a href="<?php echo e(route('admin.product.index', [
                'status' => request()->query('status'),
                'approve' => request()->query('approve'),
            ])); ?>"
                class="btn btn-light py-2 px-4">
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
                                <th><?php echo e(__('Thumbnail')); ?></th>
                                <th style="min-width: 150px"><?php echo e(__('Product Name')); ?></th>
                                <th style="min-width: 100px"><?php echo e(__('Shop')); ?></th>
                                <th class="text-center"><?php echo e(__('Price')); ?></th>
                                <th class="text-center" style="min-width: 120px"><?php echo e(__('Discount Price')); ?></th>
                                <th class="text-center"><?php echo e(__('Action')); ?></th>
                            </tr>
                        </thead>
                        <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td class="text-center"><?php echo e(++$key); ?></td>

                                <td>
                                    <img src="<?php echo e($product->thumbnail); ?>" width="50">
                                </td>

                                <td><?php echo e(Str::limit($product->name, 50, '...')); ?></td>

                                <td>
                                    <a class="text-decoration-none text-dark"
                                        href="<?php echo e(route('admin.shop.show', $product->shop_id)); ?>">
                                        <?php echo e($product->shop->name); ?>

                                    </a>
                                </td>

                                <td class="text-center">
                                    <?php echo e(showCurrency($product->price)); ?>

                                </td>

                                <td class="text-center">
                                    <?php echo e(showCurrency($product->discount_price)); ?>

                                </td>

                                <td class="text-center">
                                    <?php if(!$product->is_approve): ?>
                                        <div class="d-flex gap-3 justify-content-center">
                                            <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'admin.product.approve')): ?>
                                                <a href="<?php echo e(route('admin.product.approve', $product->id)); ?>"
                                                    class="btn btn-success btn-sm confirmApprove"><?php echo e(__('Approved')); ?></a>
                                            <?php endif; ?>
                                            <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'admin.product.destroy')): ?>
                                                <button class="btn btn-danger btn-sm"
                                                    onclick="confirmDeny(<?php echo e($product->id); ?>)">
                                                    <?php echo e(__('Denied')); ?>

                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="d-flex gap-3 justify-content-center">
                                            <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'admin.product.show')): ?>
                                                <a href="<?php echo e(route('admin.product.show', $product->id)); ?>"
                                                    class="circleIcon btn-outline-primary">
                                                    <img src="<?php echo e(asset('assets/icons-admin/eye.svg')); ?>" alt="icon"
                                                        loading="lazy" />
                                                </a>
                                            <?php endif; ?>
                                        </div>
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
            <?php echo e($products->withQueryString()->links()); ?>

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
                text: "You want to approve this product",
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

        const confirmDeny = (id) => {
            const form = document.getElementById('deleteForm');
            form.action = `<?php echo e(route('admin.product.destroy', ':id')); ?>`.replace(':id', id);
            Swal.fire({
                title: "Are you sure?",
                text: "You want to delete this product! If you confirm, it will be deleted permanently.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#ef4444",
                cancelButtonColor: "#64748b",
                confirmButtonText: "Yes, Delete it!",
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/u939461333/domains/janmitram.com/public_html/app/resources/views/admin/product/index.blade.php ENDPATH**/ ?>