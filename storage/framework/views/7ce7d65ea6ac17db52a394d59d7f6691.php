<div>
    <?php if($label): ?>
        <label for="<?php echo e($name); ?>" class="form-label"><?php echo e(__($label)); ?> <?php if($required): ?> <span class="text-danger">*</span> <?php endif; ?></label>
    <?php endif; ?>
    <input type="<?php echo e($type ?? 'text'); ?>" name="<?php echo e($name); ?>" id="<?php echo e($id ?? $name); ?>" class="form-control <?php $__errorArgs = [$name];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old($name) ?? $value); ?>" placeholder="<?php echo e(__($placeholder) ?? ''); ?>" <?php if($onlyNumber): ?> oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" <?php endif; ?>  <?php if($required): ?> required <?php endif; ?> <?php if($readonly): ?> readonly <?php endif; ?> maxlength="255" <?php if($autocomplete): ?> autocomplete="<?php echo e($autocomplete); ?>" <?php endif; ?> <?php if($notAllowLetter): ?> onkeypress="return [43, 46, 32].includes(event.charCode) || (event.charCode >= 48 && event.charCode <= 57);" <?php endif; ?>>
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
<?php /**PATH /home/u939461333/domains/janmitram.com/public_html/app/resources/views/components/input.blade.php ENDPATH**/ ?>