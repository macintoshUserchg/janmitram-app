<?php $__env->startSection('header-title', __('Push Notification')); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid mt-4">

        <?php if(!$hasConfig): ?>
            <div class="alert alert-danger d-flex align-items-center justify-content-between flex-wrap gap-4 p-3 rounded-3 shadow-sm mb-3" role="alert" id="alertBox">
                <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-3">
                    <div class="icon-container d-flex justify-content-center align-items-center rounded-circle bg-danger text-white"
                        style="width: 40px; height: 40px;">
                        <i class="fa-solid fa-exclamation-circle"></i>
                    </div>
                    <div>
                        <strong class="h5 mb-1"><?php echo e(__('Firebase Configuration Incomplete')); ?></strong>
                        <p class="mb-0">
                            <?php echo e(__('Please complete Firebase configuration to enable notifications. Notifications will not be sent without it.')); ?>

                        </p>
                    </div>
                </div>
                <div class="d-flex align-items-center">
                    <a href="<?php echo e(route('admin.firebase.index')); ?>"
                        class="btn btn-danger btn-sm ">
                        <?php echo e(__('Go to Config')); ?>

                    </a>
                </div>
            </div>
        <?php endif; ?>

        <form action="<?php echo e(route('admin.customerNotification.send')); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <div class="card">
                <div class="card-header bg-custom">
                    <h4 class="card-title m-0 py-2">
                        <i class="bi bi-bell"></i> <?php echo e(__('Push Notification')); ?>

                    </h4>
                </div>
                <div class="card-body">

                    <?php if (isset($component)) { $__componentOriginal786b6632e4e03cdf0a10e8880993f28a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal786b6632e4e03cdf0a10e8880993f28a = $attributes; } ?>
<?php $component = App\View\Components\Input::resolve(['name' => 'title','type' => 'text','label' => 'Title','placeholder' => 'Notification Title','required' => 'true'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\Input::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal786b6632e4e03cdf0a10e8880993f28a)): ?>
<?php $attributes = $__attributesOriginal786b6632e4e03cdf0a10e8880993f28a; ?>
<?php unset($__attributesOriginal786b6632e4e03cdf0a10e8880993f28a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal786b6632e4e03cdf0a10e8880993f28a)): ?>
<?php $component = $__componentOriginal786b6632e4e03cdf0a10e8880993f28a; ?>
<?php unset($__componentOriginal786b6632e4e03cdf0a10e8880993f28a); ?>
<?php endif; ?>

                    <div class="mt-3">
                        <label class="mb-1">
                            <?php echo e(__('Message')); ?>

                            <span class="text-danger">*</span>
                        </label>
                        <textarea name="message" class="form-control" rows="4" placeholder="<?php echo e(__('Notification Message...')); ?>"><?php echo e(old('message')); ?></textarea>
                        <?php $__errorArgs = ['message'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="text-danger"><?php echo e($message); ?></span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'admin.customerNotification.send')): ?>
                        <div class="d-flex justify-content-end mt-3">
                            <button type="submit" class="btn btn-primary py-2 px-4">
                                <?php echo e(__('Send Message')); ?>

                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>


            <div class="card mt-3">
                <div class="card-body">

                    <div class="d-flex justify-content-start align-items-end flex-wrap mb-3" style="gap: 10px">
                        <div style="width: 200px">
                            <label class="font-weight-normal font-14 m-0">
                                <?php echo e(__('Filter by Device Type')); ?>

                            </label>
                            <select id="deviceType" class="form-control">
                                <option value="all">
                                    <?php echo e(__('All')); ?>

                                </option>
                                <option value="android">
                                    <?php echo e(__('Android')); ?>

                                </option>
                                <option value="ios">
                                    <?php echo e(__('IOS')); ?>

                                </option>
                            </select>
                        </div>

                    </div>

                    <?php $__errorArgs = ['user'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <small class="text-danger"><?php echo e($message); ?></small>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

                    <div class="table-responsive-md maxScroll mt-2">
                        <table class="table table-bordered table-striped" id="myTable">
                            <thead>
                                <tr>
                                    <th class="px-0 text-center" style="width: 42px">
                                        <input type="checkbox" onclick="toggle(this);" />
                                    </th>
                                    <th><?php echo e(__('Thumbnail')); ?></th>
                                    <th><?php echo e(__('Name')); ?></th>
                                    <th><?php echo e(__('Email Address')); ?></th>
                                    <th><?php echo e(__('Phone Number')); ?></th>
                                </tr>
                            </thead>
                            <tbody id="notificationUsers">
                                <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td class="py-2 px-0 text-center">
                                            <input type="checkbox" name="user[]" value="<?php echo e($user->id); ?>">
                                        </td>
                                        <td>
                                            <img src="<?php echo e($user->thumbnail); ?>" alt="" width="40" height="40"
                                                loading="lazy" class="rounded" />
                                        </td>
                                        <td class="py-2"><?php echo e($user->name); ?></td>
                                        <td><?php echo e($user->email ?? '-'); ?></td>
                                        <td><?php echo e($user->phone ?? '-'); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </form>

    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        function toggle(source) {
            var checkboxes = document.querySelectorAll('input[type="checkbox"]');
            for (var i = 0; i < checkboxes.length; i++) {
                if (checkboxes[i] != source)
                    checkboxes[i].checked = source.checked;
            }
        };

        $(document).ready(function() {
            $("#deviceType").change(function() {
                var deviceType = $('#deviceType').val();
                if (deviceType) {
                    $.ajax({
                        type: 'GET',
                        url: "<?php echo e(route('admin.customerNotification.filter')); ?>",
                        dataType: 'json',
                        data: {
                            device_type: deviceType
                        },
                        success: function(response) {
                            $('#notificationUsers').empty()
                            $.each(response.data.users, function(key, value) {
                                $('#notificationUsers').append(
                                    "<tr style='display: table-row;'>\
                                        <td> <input type='checkbox' name='user[]' value='" + value.id + "'></td>\
                                            <td><img src='" + value.profile_photo + "' width='40' height='40' loading='lazy' class='rounded'/></td>\
                                        <td>" + value.name + "</td>\
                                        <td>" + (value.email ?? '-') + "</td>\
                                        <td>" + (value.phone ?? '-') + "</td>\
                                    </tr>"
                                );
                            });
                            if (!response.data.users.length) {
                                $('#notificationUsers').append(
                                    "<tr>\
                                        <td colspan='100%'> User list is empty</td>\
                                    </tr>"
                                );
                            }
                        },
                        error: function(e) {
                            $('#notificationUsers').empty()
                            $('#notificationUsers').append(
                                "<tr>\
                                    <td colspan='100%'>" + e.responseText + "</td>\
                                </tr>"
                            );
                        }
                    });
                }
            });
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/u939461333/domains/janmitram.com/public_html/app/resources/views/admin/notification/index.blade.php ENDPATH**/ ?>