<div>
    <?php if(isset($label)): ?>
        <label for="<?php echo e($name); ?>" class="form-label">
            <?php echo e(__($label)); ?>

            <?php if($required): ?> <span class="text-danger">*</span> <?php endif; ?>
        </label>
    <?php endif; ?>
    <input type="file" name="<?php echo e($name); ?>" id="<?php echo e($name); ?>" class="form-control <?php $__errorArgs = [$name];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid
    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" <?php if($preview): ?> onchange="previewFile(event,'<?php echo e($preview); ?>')" <?php endif; ?> <?php if($required): ?> required <?php endif; ?>/>
    <?php $__errorArgs = [$name];
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
<?php /**PATH /home/u939461333/domains/janmitram.com/public_html/app/resources/views/components/file.blade.php ENDPATH**/ ?>