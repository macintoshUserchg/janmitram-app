<div>
    <?php if(isset($label) && $label): ?>
        <label for="<?php echo e($name); ?>" class="form-label">
            <?php echo e(__($label)); ?>

            <?php if($required): ?> <span class="text-danger">*</span> <?php endif; ?>
        </label>
    <?php endif; ?>
    <select <?php if($placeholder): ?> data-placeholder="<?php echo e($placeholder); ?>" <?php endif; ?> name="<?php echo e($name); ?>" id="<?php echo e($id ?? $name); ?>" class="form-control select2 <?php $__errorArgs = [$name];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" <?php if($multiselect): ?> multiple <?php endif; ?> style="width: 100%;">
        <?php echo e($slot); ?>

    </select>
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
<?php /**PATH /home/u939461333/domains/janmitram.com/public_html/app/resources/views/components/select.blade.php ENDPATH**/ ?>