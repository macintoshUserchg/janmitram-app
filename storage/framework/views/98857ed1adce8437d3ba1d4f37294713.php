<?php
    $generaleSetting = App\Models\GeneraleSetting::first();

    $title = $generaleSetting?->title ?? config('app.name', 'ReadyEcommerce');
    $favicon = $generaleSetting?->favicon ?? asset('assets/favicon.png');
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <meta name="base-url" content="<?php echo e(url('/')); ?>">
    <meta name="app-url" content="<?php echo e(url('/')); ?>">
    <!-- description -->
    <meta name="description" content="ecommerce website">

    <title><?php echo e($title); ?></title>
    <link rel="shortcut icon" href="<?php echo e($favicon); ?>" type="image/x-icon">

    <?php echo app('Illuminate\Foundation\Vite')('resources/css/app.css'); ?>
</head>

<body>
    <div id="app"></div>

    <?php echo app('Illuminate\Foundation\Vite')('resources/js/app.js'); ?>
</body>

</html>
<?php /**PATH /home/u939461333/domains/janmitram.com/public_html/app/resources/views/app.blade.php ENDPATH**/ ?>