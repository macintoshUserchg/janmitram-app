<?php $__env->startSection('content'); ?>
    <div class="d-flex align-items-center flex-wrap gap-3 justify-content-between px-3">
        <h4><?php echo e(__('All Customers')); ?></h4>

        <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'admin.customer.create')): ?>
            <a href="<?php echo e(route('admin.customer.create')); ?>" class="btn py-2 btn-primary">
                <i class="bi bi-patch-plus"></i>
                <?php echo e(__('Add Customer')); ?>

            </a>
        <?php endif; ?>
    </div>

    <div class="container-fluid mt-3">

        <div class="mb-3 card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table border table-responsive-lg">
                        <thead>
                            <tr>
                                <th class="text-center"><?php echo e(__('SL')); ?>.</th>
                                <th><?php echo e(__('Profile')); ?></th>
                                <th style="min-width: 150px"><?php echo e(__('Name')); ?></th>
                                <th style="min-width: 100px"><?php echo e(__('Phone')); ?></th>
                                <th><?php echo e(__('Email')); ?></th>
                                <th class="text-center"><?php echo e(__('Gender')); ?></th>
                                <th class="text-center"><?php echo e(__('Date of Birth')); ?></th>
                                <th class="text-center"><?php echo e(__('Action')); ?></th>
                            </tr>
                        </thead>
                        <?php $__empty_1 = true; $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td class="text-center"><?php echo e(++$key); ?></td>

                                <td>
                                    <img src="<?php echo e($customer->thumbnail); ?>" width="50">
                                </td>

                                <td><?php echo e(Str::limit($customer->fullName, 50, '...')); ?></td>

                                <td>
                                    <?php echo e($customer->phone ?? '--'); ?>

                                </td>

                                <td>
                                    <?php echo e($customer->email ?? '--'); ?>

                                </td>

                                <td class="text-center">
                                    <?php echo e($customer->gender ?? '--'); ?>

                                </td>

                                <td class="text-center">
                                    <?php echo e($customer->date_of_birth ?? '--'); ?>

                                </td>

                                <td class="text-center">
                                    <div class="d-flex gap-2 justify-content-center">
                                        <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'admin.customer.edit')): ?>
                                            <a href="<?php echo e(route('admin.customer.edit', $customer->id)); ?>"
                                                class="btn btn-outline-primary circleIcon" data-bs-toggle="tooltip"
                                                data-bs-placement="left" data-bs-title="<?php echo e(__('Edit')); ?>">
                                                <img src="<?php echo e(asset('assets/icons-admin/edit.svg')); ?>" alt="edit"
                                                    loading="lazy" />
                                            </a>
                                        <?php endif; ?>

                                        <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'admin.customer.destroy')): ?>
                                            <a href="<?php echo e(route('admin.customer.destroy', $customer->id)); ?>"
                                                class="btn btn-outline-danger circleIcon deleteConfirm" data-bs-toggle="tooltip"
                                                data-bs-placement="left" data-bs-title="<?php echo e(__('Delete')); ?>">
                                                <img src="<?php echo e(asset('assets/icons-admin/trash.svg')); ?>" alt="delete"
                                                    loading="lazy" />
                                            </a>
                                        <?php endif; ?>

                                        <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'admin.customer.reset-password')): ?>
                                            <button type="button" class="btn btn-outline-info circleIcon"
                                                data-bs-toggle="tooltip" data-bs-placement="left"
                                                data-bs-title="<?php echo e(__('Reset Password')); ?>"
                                                onclick="openResetPasswordModal('<?php echo e($customer->id); ?>','<?php echo e($customer->fullName); ?>')">
                                                <img src="<?php echo e(asset('assets/icons-admin/role-permission.svg')); ?>" alt="key"
                                                    loading="lazy" />
                                            </button>
                                        <?php endif; ?>
                                    </div>
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
            <?php echo e($customers->withQueryString()->links()); ?>

        </div>

        <form action="" method="POST" id="resetPasswordForm">
            <?php echo csrf_field(); ?>
            <div class="modal fade" id="resetPasswordModal" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title fs-5"><?php echo e(__('Reset Password')); ?> <span id="userName"></span></h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="password1" class="form-label">
                                    <?php echo e(__('Password')); ?>

                                </label>
                                <div class="position-relative passwordInput">
                                    <input type="password" name="password" id="password1" class="form-control"
                                        required="true" placeholder="Enter Password">
                                    <span class="eye" onclick="showHidePassword(1)">
                                        <i class="fa fa-eye-slash" id="togglePassword1"></i>
                                    </span>
                                </div>
                                <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="text text-danger m-0"><?php echo e($message); ?></p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <div class="mb-3">
                                <label for="password2" class="form-label">
                                    <?php echo e(__('Confirm Password')); ?>

                                </label>
                                <div class="position-relative passwordInput">
                                    <input type="password" name="password_confirmation" id="password2" class="form-control"
                                        required="true" placeholder="Enter Password again">
                                    <span class="eye" onclick="showHidePassword(2)">
                                        <i class="fa fa-eye-slash" id="togglePassword2"></i>
                                    </span>
                                </div>
                                <span id="passwordMatch" class="text text-danger d-none"></span>
                                <?php $__errorArgs = ['password_confirmation'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="text text-danger m-0"><?php echo e($message); ?></p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <?php echo e(__('Close')); ?>

                            </button>
                            <button type="submit" id="submit" class="btn btn-primary">
                                <?php echo e(__('Save changes')); ?>

                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startPush('scripts'); ?>
    <script>
        function openResetPasswordModal(userId, userName) {
            $('#resetPasswordModal').modal('show');
            $('#userName').html('(' + userName + ')');
            $('#resetPasswordForm').attr('action', `<?php echo e(route('admin.customer.reset-password', ':id')); ?>`.replace(':id',
                userId));
        }

        function showHidePassword(num) {
            const toggle = document.getElementById("togglePassword" + num);
            const password = document.getElementById("password" + num);

            // toggle the type attribute
            const type = password.getAttribute("type") === "password" ? "text" : "password";
            password.setAttribute("type", type);
            // toggle the icon
            toggle.classList.toggle("fa-eye");
            toggle.classList.toggle("fa-eye-slash");
        }

        document.getElementById('password2').addEventListener('keyup', function(e) {
            $password1 = document.getElementById('password1').value;
            $password2 = document.getElementById('password2').value;

            $message = document.getElementById('passwordMatch');

            if ($password1 == $password2) {
                document.getElementById('password2').classList.remove('is-invalid');
                $message.classList.add('d-none');
                document.getElementById('submit').disabled = false;
            } else {
                document.getElementById('password2').classList.add('is-invalid');
                $message.classList.remove('d-none');
                $message.innerHTML = "Password doesn't match";
                document.getElementById('submit').disabled = true;
            }
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/u939461333/domains/janmitram.com/public_html/app/resources/views/admin/customer/index.blade.php ENDPATH**/ ?>