<?php $__env->startSection('title', __('Admin Settings')); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-title">
        <div class="d-flex gap-2 align-items-center">
            <i class="bi bi-gear-fill"></i> <?php echo e(__('Admin Settings')); ?>

            <button class="btn btn-primary btn-sm ms-3" id="runUpdateScript">
                <?php echo e(__('Run Latest Update Script')); ?>

            </button>
        </div>
    </div>
    <form action="<?php echo e(route('admin.generale-setting.update')); ?>" method="POST" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
        <div class="card mt-3">
            <div class="card-body">

                <div class="row">
                    <div class="col-lg-6">
                        <div class="">
                            <?php if (isset($component)) { $__componentOriginal786b6632e4e03cdf0a10e8880993f28a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal786b6632e4e03cdf0a10e8880993f28a = $attributes; } ?>
<?php $component = App\View\Components\Input::resolve(['type' => 'text','label' => 'Website Name','name' => 'name','placeholder' => 'Enter Website Name','value' => $generaleSetting?->name] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
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
                        </div>

                        <div class="mt-4">
                            <?php if (isset($component)) { $__componentOriginal786b6632e4e03cdf0a10e8880993f28a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal786b6632e4e03cdf0a10e8880993f28a = $attributes; } ?>
<?php $component = App\View\Components\Input::resolve(['label' => 'Website Title','name' => 'title','type' => 'text','placeholder' => 'Enter Website Title for title bar','value' => $generaleSetting?->title] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
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
                        </div>

                        <div class="row mt-4">
                            <div class="col-sm-6">
                                <?php if (isset($component)) { $__componentOriginalbf566fc26595b9cc6779e170beef8a5a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalbf566fc26595b9cc6779e170beef8a5a = $attributes; } ?>
<?php $component = App\View\Components\Select::resolve(['label' => 'Default Currency','name' => 'currency'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\Select::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
                                    <option value="">
                                        <?php echo e(__('Select Currency')); ?>

                                    </option>
                                    <?php $__currentLoopData = $currencies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $currency): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($currency->id); ?>"
                                            <?php echo e($generaleSetting?->currency_id == $currency->id ? 'selected' : ''); ?>>
                                            <?php echo e($currency->name); ?> (<?php echo e($currency->symbol); ?>)
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

                            <div class="col-sm-6 mt-4 mt-sm-0">
                                <?php if (isset($component)) { $__componentOriginalbf566fc26595b9cc6779e170beef8a5a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalbf566fc26595b9cc6779e170beef8a5a = $attributes; } ?>
<?php $component = App\View\Components\Select::resolve(['label' => 'Currency Position','name' => 'currency_position'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\Select::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
                                    <option value="prefix"
                                        <?php echo e($generaleSetting?->currency_position == 'prefix' ? 'selected' : ''); ?>>
                                        <?php echo e(__('Prefix')); ?>

                                    </option>
                                    <option value="suffix"
                                        <?php echo e($generaleSetting?->currency_position == 'suffix' ? 'selected' : ''); ?>>
                                        <?php echo e(__('Suffix')); ?>

                                    </option>
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
                    </div>
                    <div class="col-lg-6">
                        <div class="row">
                            <div class="col-sm-6 mt-4 mt-sm-5">
                                <div class="mt-3 d-flex align-items-center justify-content-center">
                                    <div class="logoRatio">
                                        <img id="previewLogo"
                                            src="<?php echo e($generaleSetting?->logo ?? 'https://placehold.co/200x50/png'); ?>"
                                            alt="" width="100%" loading="lazy" />
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <?php if (isset($component)) { $__componentOriginal27a9ac474dd5f2a4b21bde3c2c880dc3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal27a9ac474dd5f2a4b21bde3c2c880dc3 = $attributes; } ?>
<?php $component = App\View\Components\File::resolve(['name' => 'logo','label' => 'Logo Ratio4:1 (200x50)','preview' => 'previewLogo'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('file'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\File::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal27a9ac474dd5f2a4b21bde3c2c880dc3)): ?>
<?php $attributes = $__attributesOriginal27a9ac474dd5f2a4b21bde3c2c880dc3; ?>
<?php unset($__attributesOriginal27a9ac474dd5f2a4b21bde3c2c880dc3); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal27a9ac474dd5f2a4b21bde3c2c880dc3)): ?>
<?php $component = $__componentOriginal27a9ac474dd5f2a4b21bde3c2c880dc3; ?>
<?php unset($__componentOriginal27a9ac474dd5f2a4b21bde3c2c880dc3); ?>
<?php endif; ?>
                                </div>
                            </div>

                            <div class="col-sm-6 mt-4">
                                <div class="mt-3 d-flex align-items-center justify-content-center">
                                    <div class="logoFav">
                                        <img id="previewFavicon"
                                            src="<?php echo e($generaleSetting?->favicon ?? 'https://placehold.co/300x300/png'); ?>"
                                            alt="" width="100%" loading="lazy" />
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <?php if (isset($component)) { $__componentOriginal27a9ac474dd5f2a4b21bde3c2c880dc3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal27a9ac474dd5f2a4b21bde3c2c880dc3 = $attributes; } ?>
<?php $component = App\View\Components\File::resolve(['name' => 'favicon','label' => 'Favicon (300x300)','preview' => 'previewFavicon'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('file'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\File::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal27a9ac474dd5f2a4b21bde3c2c880dc3)): ?>
<?php $attributes = $__attributesOriginal27a9ac474dd5f2a4b21bde3c2c880dc3; ?>
<?php unset($__attributesOriginal27a9ac474dd5f2a4b21bde3c2c880dc3); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal27a9ac474dd5f2a4b21bde3c2c880dc3)): ?>
<?php $component = $__componentOriginal27a9ac474dd5f2a4b21bde3c2c880dc3; ?>
<?php unset($__componentOriginal27a9ac474dd5f2a4b21bde3c2c880dc3); ?>
<?php endif; ?>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="col-sm-6 mt-4">
                        <div class="mt-3 d-flex align-items-center justify-content-center">
                            <div class="logoFav">
                                <img id="previewAppIcon"
                                    src="<?php echo e($generaleSetting?->appLogo ?? 'https://placehold.co/300x300/png'); ?>"
                                    alt="" width="100%" loading="lazy" />
                            </div>
                        </div>
                        <div class="mt-3">
                            <?php if (isset($component)) { $__componentOriginal27a9ac474dd5f2a4b21bde3c2c880dc3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal27a9ac474dd5f2a4b21bde3c2c880dc3 = $attributes; } ?>
<?php $component = App\View\Components\File::resolve(['name' => 'app_logo','label' => 'App Logo (300x300)','preview' => 'previewAppIcon'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('file'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\File::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal27a9ac474dd5f2a4b21bde3c2c880dc3)): ?>
<?php $attributes = $__attributesOriginal27a9ac474dd5f2a4b21bde3c2c880dc3; ?>
<?php unset($__attributesOriginal27a9ac474dd5f2a4b21bde3c2c880dc3); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal27a9ac474dd5f2a4b21bde3c2c880dc3)): ?>
<?php $component = $__componentOriginal27a9ac474dd5f2a4b21bde3c2c880dc3; ?>
<?php unset($__componentOriginal27a9ac474dd5f2a4b21bde3c2c880dc3); ?>
<?php endif; ?>
                        </div>
                    </div>

                </div>

            </div>
        </div>

        <!--######## Others Information ##########-->
        <div class="card mt-4">
            <div class="card-header d-flex align-items-center gap-2 py-3">
                <i class="bi bi-app-indicator"></i>
                <h5 class="mb-0">
                    <?php echo e(__('Others Information')); ?>

                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-4 col-md-6">
                        <label for="">Mobile Number</label>
                        <input type="text" name="mobile" class="form-control" oninput="this.value = this.value.replace(/[^0-9+]/g, '').replace(/(?!^)\+/g, '')" value="<?php echo e($generaleSetting?->mobile); ?>" placeholder="Enter Mobile Number">
                    </div>

                    <div class="col-lg-4 col-md-6 mt-4 mt-lg-0">
                        <?php if (isset($component)) { $__componentOriginal786b6632e4e03cdf0a10e8880993f28a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal786b6632e4e03cdf0a10e8880993f28a = $attributes; } ?>
<?php $component = App\View\Components\Input::resolve(['type' => 'email','name' => 'email','label' => 'Email Address','placeholder' => 'Enter Email Address','value' => $generaleSetting?->email] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
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
                    </div>

                    <div class="col-lg-4 col-md-6 mt-4 mt-lg-0">
                        <?php if (isset($component)) { $__componentOriginal786b6632e4e03cdf0a10e8880993f28a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal786b6632e4e03cdf0a10e8880993f28a = $attributes; } ?>
<?php $component = App\View\Components\Input::resolve(['type' => 'text','name' => 'address','label' => 'Address','placeholder' => 'Enter Address','value' => $generaleSetting?->address] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
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
                    </div>

                </div>
            </div>
        </div>

        <!--######## download app link ##########-->
        <div class="card mt-4">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2 py-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-app-indicator"></i>
                    <h5 class="mb-0">
                        <?php echo e(__('Download App Link')); ?>

                    </h5>
                </div>

                <div>
                    <label class="m-0 fw-bold" for="toggle">
                        <?php echo e(__('Show/Hide Website Navigation Download App')); ?>

                    </label>
                    <label class="switch mb-0" data-bs-toggle="tooltip" data-bs-placement="left"
                        data-bs-title="Show/Hide">
                        <input id="toggle" type="checkbox" <?php echo e($generaleSetting?->show_download_app ? 'checked' : ''); ?>

                            name="show_download_app">
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
            <div class="card-body">
                <div class="row gy-3">
                    <div class="col-md-6">
                        <label for="" class="mb-1">
                            <?php echo e(__('Google PlayStore App Link')); ?>

                        </label>
                        <textarea name="google_playstore_url" class="form-control" rows="3"
                            placeholder="Enter Google PlayStore App Link"><?php echo e($generaleSetting?->google_playstore_url); ?></textarea>
                    </div>

                    <div class="col-md-6">
                        <label for="" class="mb-1">
                            <?php echo e(__('Apple Store App Link')); ?>

                        </label>
                        <textarea name="app_store_url" class="form-control" rows="3" placeholder="Enter Apple Store App Link"><?php echo e($generaleSetting?->app_store_url); ?></textarea>
                    </div>

                </div>
            </div>
        </div>

        <!--######## Footer Information ##########-->
        <div class="card mt-4">
            <div class="card-header d-flex align-items-center justify-content-between gap-2 flex-wrap py-3">
                <div class="d-flex align-items-center gap-1">
                    <i class="bi bi-align-bottom"></i>
                    <h5 class="mb-0">
                        <?php echo e(__('Footer Section Info')); ?>

                    </h5>
                </div>

                <div>
                    <label class="m-0 fw-bold" for="toggle">
                        <?php echo e(__('Show/Hide Admin Bottom Footer Section')); ?>

                    </label>
                    <label class="switch mb-0" data-bs-toggle="tooltip" data-bs-placement="left"
                        data-bs-title="Show/Hide">
                        <input id="toggle" type="checkbox" <?php echo e($generaleSetting?->show_footer ? 'checked' : ''); ?>

                            name="show_footer">
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <label for="">Hotline Number</label>
                        <input type="text" name="footer_phone" class="form-control" oninput="this.value = this.value.replace(/[^0-9+]/g, '').replace(/(?!^)\+/g, '')" value="<?php echo e($generaleSetting?->footer_phone); ?>" placeholder="Enter Hotline Number">
                    </div>

                    <div class="col-md-6 mt-4 mt-lg-0">
                        <?php if (isset($component)) { $__componentOriginal786b6632e4e03cdf0a10e8880993f28a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal786b6632e4e03cdf0a10e8880993f28a = $attributes; } ?>
<?php $component = App\View\Components\Input::resolve(['type' => 'text','name' => 'footer_text','label' => 'Footer Text','placeholder' => 'Enter Footer Text','value' => $generaleSetting?->footer_text ?? 'All right reserved by company'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
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
                    </div>

                    <div class="col-md-6 mt-4">
                        <div class="mt-4 d-flex align-items-center justify-content-center">
                            <div class="logoRatio">
                                <img id="previewFooterLogo"
                                    src="<?php echo e($generaleSetting?->footerLogo ?? 'https://placehold.co/200x50/png'); ?>"
                                    alt="" width="100%" loading="lazy" />
                            </div>
                        </div>
                        <div class="mt-3">
                            <?php if (isset($component)) { $__componentOriginal27a9ac474dd5f2a4b21bde3c2c880dc3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal27a9ac474dd5f2a4b21bde3c2c880dc3 = $attributes; } ?>
<?php $component = App\View\Components\File::resolve(['name' => 'footer_logo','label' => 'Frontend Footer Logo Ratio4:1','preview' => 'previewFooterLogo'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('file'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\File::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal27a9ac474dd5f2a4b21bde3c2c880dc3)): ?>
<?php $attributes = $__attributesOriginal27a9ac474dd5f2a4b21bde3c2c880dc3; ?>
<?php unset($__attributesOriginal27a9ac474dd5f2a4b21bde3c2c880dc3); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal27a9ac474dd5f2a4b21bde3c2c880dc3)): ?>
<?php $component = $__componentOriginal27a9ac474dd5f2a4b21bde3c2c880dc3; ?>
<?php unset($__componentOriginal27a9ac474dd5f2a4b21bde3c2c880dc3); ?>
<?php endif; ?>
                        </div>
                    </div>

                    <div class="col-md-6 mt-4">
                        <div class="mt-2 d-flex align-items-center justify-content-center">
                            <div class="logoFav">
                                <img id="footerQrCode"
                                    src="<?php echo e($generaleSetting?->footerQr ?? 'https://placehold.co/200x200/png'); ?>"
                                    alt="" width="100%" loading="lazy" />
                            </div>
                        </div>
                        <div class="mt-3">
                            <?php if (isset($component)) { $__componentOriginal27a9ac474dd5f2a4b21bde3c2c880dc3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal27a9ac474dd5f2a4b21bde3c2c880dc3 = $attributes; } ?>
<?php $component = App\View\Components\File::resolve(['name' => 'footer_qrcode','label' => 'Frontend Scan the QR (200x200)','preview' => 'footerQrCode'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('file'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\File::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal27a9ac474dd5f2a4b21bde3c2c880dc3)): ?>
<?php $attributes = $__attributesOriginal27a9ac474dd5f2a4b21bde3c2c880dc3; ?>
<?php unset($__attributesOriginal27a9ac474dd5f2a4b21bde3c2c880dc3); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal27a9ac474dd5f2a4b21bde3c2c880dc3)): ?>
<?php $component = $__componentOriginal27a9ac474dd5f2a4b21bde3c2c880dc3; ?>
<?php unset($__componentOriginal27a9ac474dd5f2a4b21bde3c2c880dc3); ?>
<?php endif; ?>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'admin.generale-setting.update')): ?>
            <div class="d-flex justify-content-end mt-4 mb-3">
                <button type="submit" class="btn btn-primary py-2.5 px-3">
                    <?php echo e(__('Save And Update')); ?>

                </button>
            </div>
        <?php endif; ?>

    </form>

    <form action="<?php echo e(route('admin.generale-setting.update.command')); ?>" method="POST" id="scriptRunForm">
        <?php echo csrf_field(); ?>
    </form>



<?php $__env->stopSection(); ?>
<?php $__env->startPush('scripts'); ?>
    <script>
        $('#runUpdateScript').click(function() {
            Swal.fire({
                title: "<?php echo e(__('Are you sure? want to run update script')); ?>",
                text: "When you run this script, all data related to the latest version (v<?php echo e(config('app.version')); ?>) will be reset. Are you sure?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "<?php echo e(__('Yes, Run!')); ?>",
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById("scriptRunForm").submit();
                }
            });
        })
    </script>
    <?php if(session('runUpdateScriptError')): ?>
        <script>
            Swal.fire({
                icon: "error",
                title: "Oops...",
                html: `<?php $__currentLoopData = session('runUpdateScriptError'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php echo e($error); ?> <br><br>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>`,
            });
        </script>
    <?php endif; ?>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/u939461333/domains/janmitram.com/public_html/app/resources/views/admin/generale-setting.blade.php ENDPATH**/ ?>