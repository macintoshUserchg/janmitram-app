<?php $__env->startSection('header-title', __('Welcome Back,') . ' ' . Str::limit(auth()->user()?->name, 20)); ?>
<?php $__env->startSection('header-subtitle', __('Monitor your business analytics and statistics.')); ?>

<?php $__env->startSection('content'); ?>
    <div class="admin-dashboard">

        <!-- Alert Box -->
        <?php if(app()->environment('local')): ?>
            <div id="alertBox" class="alert alert-danger align-items-center gap-1 justify-content-between mb-3" role="alert"
                style="display: flex">
                <div class="d-flex align-items-center gap-2">
                    <i class="fa-solid fa-bell"></i>
                    <div>
                        <strong><?php echo e(__('Note')); ?></strong> <?php echo e(__('Every 3 hours all data will be cleared')); ?>

                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

    <?php
        $text = 'Total ' . ($businessModel == 'single' ? 'Categories' : 'Shops');
    ?>

        <!-- Flash Deal Alert -->
        <?php if($flashSale): ?>
            <div>
                <div class="alert flash-deal-alert d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div class="d-flex flex-column">
                        <div class="deal-text"><?php echo e($flashSale->name); ?></div>
                        <div class="deal-title"><?php echo e(__('Coming Soon')); ?></div>
                    </div>
                    <div class="countdown d-flex align-items-center">
                        <!-- Days -->
                        <div class="countdown-section">
                            <div class="countdown-label"><?php echo e(__('Days')); ?></div>
                            <div id="days" class="countdown-time">00</div>
                        </div>
                        <!-- Hours -->
                        <div class="countdown-section">
                            <div class="countdown-label"><?php echo e(__('Hours')); ?></div>
                            <div id="hours" class="countdown-time">00</div>
                        </div>
                        <!-- Minutes -->
                        <div class="countdown-section">
                            <div class="countdown-label"><?php echo e(__('Minutes')); ?></div>
                            <div id="minutes" class="countdown-time">00</div>
                        </div>
                        <!-- Seconds -->
                        <div class="countdown-section">
                            <div class="countdown-label"><?php echo e(__('Seconds')); ?></div>
                            <div id="seconds" class="countdown-time">00</div>
                        </div>
                    </div>
                    <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'shop.flashSale.show')): ?>
                        <a href="<?php echo e(route('shop.flashSale.show', $flashSale->id)); ?>" class="btn btn-primary py-2.5 addBtn">
                            Add Product
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
        <!-- End Flash Deal Alert -->

    <div class="card">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6 col-lg-4 col-xl-3">
                    <div class="dashboard-box item-1">
                        <h2 class="count"><?php echo e($businessModel == 'single' ? $totalCategories : $totalShop); ?></h2>
                        <h3 class="title"><?php echo e(__($text)); ?></h3>
                        <div class="icon">
                            <img src="<?php echo e(asset('assets/icons-admin/dashboard-shop.svg')); ?>" alt="icon" loading="lazy" />
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4 col-xl-3">
                    <div class="dashboard-box item-2">
                        <h2 class="count"><?php echo e($totalProduct); ?></h2>
                        <h3 class="title"><?php echo e(__('Total Products')); ?></h3>
                        <div class="icon">
                            <img src="<?php echo e(asset('assets/icons-admin/dashboard-product.svg')); ?>" alt="icon"
                                loading="lazy" />
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4 col-xl-3">
                    <div class="dashboard-box item-3">
                        <h2 class="count"><?php echo e($totalOrder); ?></h2>
                        <h3 class="title"><?php echo e(__('Total Orders')); ?></h3>
                        <div class="icon">
                            <img src="<?php echo e(asset('assets/icons-admin/dashboard-order.svg')); ?>" alt="icon"
                                loading="lazy" />
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4 col-xl-3">
                    <div class="dashboard-box item-4">
                        <h2 class="count"><?php echo e($totalCustomer); ?></h2>
                        <h3 class="title"><?php echo e(__('Total Customers')); ?></h3>
                        <div class="icon">
                            <img src="<?php echo e(asset('assets/icons-admin/dashboard-customer.svg')); ?>" alt="icon"
                                loading="lazy" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if (\Illuminate\Support\Facades\Blade::check('hasPermission', 'admin.order.index')): ?>
        <!---- Order Analytics -->
        <div class="card mt-3">
            <div class="card-body">
                <div class="cardTitleBox">
                    <h5 class="card-title chartTitle">
                        <?php echo e(__('Order Analytics')); ?>

                    </h5>
                </div>

                <?php
                    $icons = [
                        'pending' => asset('assets/icons-admin/clock.svg'),
                        'confirm' => asset('assets/icons-admin/shopping-cart-check.svg'),
                        'processing' => asset('assets/icons-admin/rotate-circle.svg'),
                        'pickup' => asset('assets/icons-admin/delivery-cart-arrow-up.svg'),
                        'delivered' => asset('assets/icons-admin/box-check.svg'),
                        'onTheWay' => asset('assets/icons-admin/truck.svg'),
                        'cancelled' => asset('assets/icons-admin/shopping-cart-times.svg'),
                    ];
                ?>

                <div class="d-flex flex-wrap gap-3 orderStatus">
                    <?php $__currentLoopData = $orderStatuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="<?php echo e(route('admin.order.index', str_replace(' ', '_', $status->value))); ?>"
                            class="d-flex status flex-grow-1 <?php echo e(Str::camel($status->value)); ?>">
                            <div class="d-flex align-items-center gap-2 justify-content-between w-100">
                                <div class="d-flex align-items-center gap-2">
                                    <img src="<?php echo e($icons[Str::camel($status->value)]); ?>" alt="icon" loading="lazy" />
                                    <span><?php echo e(__($status->value)); ?></span>
                                </div>
                                <div class="icon">
                                    <img src="<?php echo e(asset('assets/icons-admin/arrow-export.svg')); ?>" alt="icon"
                                        loading="lazy" />
                                </div>
                            </div>
                            <span class="count"><?php echo e(${Str::camel($status->value)}); ?></span>
                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!---- Shop Wallet -->
    <div class="card mt-4">
        <div class="card-body">
            <div class="cardTitleBox">
                <h5 class="card-title chartTitle">
                    <?php echo e(__('Admin Wallet')); ?>

                </h5>
            </div>

            <div class="row">
                <div class="col-lg-5">
                    <div class="wallet h-100">
                        <h3 class="balance"><?php echo e(showCurrency(auth()->user()?->wallet?->balance)); ?></h3>
                        <div class="d-flex align-items-center justify-content-between gap-2 flex-wrap w-100">
                            <div>
                                <div class="d-flex align-items-center gap-1 percentUp">
                                    <span>+18.53%</span>
                                    <img src="<?php echo e(asset('assets/icons-admin/arrow.svg')); ?>" alt="icon" loading="lazy" />
                                </div>
                                <div class="title"><?php echo e(__('Total Earning')); ?></div>
                            </div>
                            <div class="wallet-icon svg-bg">
                                <img src="<?php echo e(asset('assets/icons-admin/wallet.svg')); ?>" alt="" width="100%">
                            </div>
                        </div>

                    </div>
                </div>

                <div class="col-lg-7">
                    <div class="row g-3">

                        <div class="col-md-6">
                            <div class="wallet-others">
                                <div class="amount"><?php echo e(showCurrency($alreadyWithdraw)); ?></div>
                                <div class="d-flex align-items-center gap-2 justify-content-between">
                                    <div class="title"><?php echo e(__('Already Withdraw')); ?></div>
                                    <div class="icon svg-bg">
                                        <img src="<?php echo e(asset('assets/icons-admin/withdraw.svg')); ?>" alt="icon"
                                            loading="lazy" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="wallet-others">
                                <div class="amount"><?php echo e(showCurrency($pendingWithdraw)); ?></div>
                                <div class="d-flex align-items-center gap-2 justify-content-between">
                                    <div class="title"><?php echo e(__('Pending Withdraw')); ?></div>
                                    <div class="icon">
                                        <img src="<?php echo e(asset('assets/icons-admin/credit-card-orange.svg')); ?>"
                                            alt="icon" loading="lazy" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="wallet-others">
                                <div class="amount"><?php echo e(showCurrency($totalCommission)); ?></div>
                                <div class="d-flex align-items-center gap-2 justify-content-between">
                                    <div class="title"><?php echo e(__('Total Commission')); ?></div>
                                    <div class="icon">
                                        <img src="<?php echo e(asset('assets/icons-admin/chart-trend-up-green.svg')); ?>"
                                            alt="icon" loading="lazy" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="wallet-others">
                                <div class="amount"><?php echo e(showCurrency($deniedWithdraw)); ?></div>
                                <div class="d-flex align-items-center gap-2 justify-content-between">
                                    <div class="title"><?php echo e(__('Rejected Withdraw')); ?></div>
                                    <div class="icon">
                                        <img src="<?php echo e(asset('assets/icons-admin/withdraw-reject.svg')); ?>" alt="icon"
                                            loading="lazy" />
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- statistics Overview -->
    <div class="card mt-3">
        <div class="card-body">
            <div class="cardTitleBox d-flex align-items-center justify-content-between flex-wrap gap-2">
                <h5 class="card-title chartTitle mb-0"><?php echo e(__('Statistics')); ?></h5>
                <div class="dashboard-statistics-toolbar d-flex align-items-center gap-3 flex-wrap">
                    <div class="dashboard-statistics-filters d-flex align-items-center flex-wrap gap-2">
                        <button class="statisticsBtn " data-value="daily">
                            <?php echo e(__('Daily')); ?>

                        </button>
                        <button class="statisticsBtn" data-value="monthly">
                            <?php echo e(__('Monthly')); ?>

                        </button>
                        <button class="statisticsBtn active" data-value="yearly">
                            <?php echo e(__('Yearly')); ?>

                        </button>
                    </div>

                    <div class="statisticsDivder"></div>

                    <div class="dashboard-statistics-date">
                        <input type="date" name="date" id="dateStatistic" class="statisticsInput">
                    </div>
                    <div class="dashboard-statistics-reset">
                        <button class="btn btn-sm btn-outline-secondary resetBtn">Reset</button>
                    </div>

                </div>
            </div>

            <div class="row g-3">
                <div class="col-12 col-lg-8">

                    <div class="card theme-dark">
                        <div class="card-body">
                            <div class="border-bottom pb-3">
                                <h3 id="totalOrder"><?php echo e($totalOrder); ?></h3>
                                <p><?php echo e(__('Total Orders')); ?></p>
                            </div>
                            <canvas id="myChart" width="400" height="200"></canvas>
                        </div>
                    </div>

                </div>

                <div class="col-lg-4">
                    <div class="card h-100 border theme-dark">
                        <div class="card-body d-flex flex-column justify-content-between">
                            <div class="border-bottom pb-3">
                                <h3><?php echo e($totalCustomer + $totalShop + $totalRider); ?></h3>
                                <p><?php echo e(__('User Overview')); ?></p>
                            </div>

                            <div class="mt-auto colorDark">
                                <canvas id="myPieChart" width="200" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-body">
            <div class="cardTitleBox">
                <h5 class="card-title chartTitle">
                    <?php echo e(__('Order Summary')); ?> <span style="color: #687387">(<?php echo e(__('Latest 5 Order')); ?>)</span>
                </h5>
            </div>

            <div class="table-responsive">
                <table class="table dashboard">
                    <thead>
                        <tr>
                            <th><strong><?php echo e(__('Order ID')); ?></strong></th>
                            <th><strong><?php echo e(__('Qty')); ?></strong></th>
                            <?php if($businessModel == 'multi'): ?>
                                <th><strong><?php echo e(__('Shop')); ?></strong></th>
                            <?php endif; ?>
                            <th><strong><?php echo e(__('Date')); ?></strong></th>
                            <th><strong><?php echo e(__('Status')); ?></strong></th>
                            <th><strong><?php echo e(__('Action')); ?></strong></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $latestOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td class="tableId">#<?php echo e($order->prefix . $order->order_code); ?></td>
                                <td class="tableId">
                                    <?php echo e($order->products->count()); ?>

                                </td>
                                <?php if($businessModel == 'multi'): ?>
                                    <td class="tablecustomer">
                                        <?php echo e($order->shop?->name); ?>

                                    </td>
                                <?php endif; ?>
                                <td class="tableId">
                                    <?php echo e($order->created_at->format('d M, Y')); ?>

                                </td>
                                <?php
                                    $status = Str::ucfirst(str_replace(' ', '', $order->order_status->value));
                                ?>
                                <td class="tableStatus">
                                    <div class="statusItem">
                                        <div class="circleDot animated<?php echo e($status); ?>"></div>
                                        <div class="statusText">
                                            <span class="status<?php echo e($status); ?>">
                                                <?php echo e($order->order_status->value); ?>

                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="tableAction">
                                    <a href="<?php echo e(route('admin.order.show', $order->id)); ?>" data-bs-toggle="tooltip"
                                        data-bs-placement="left" data-bs-title="Order details"
                                        class="circleIcon btn-sm btn-outline-primary svg-bg">
                                        <img src="<?php echo e(asset('assets/icons-admin/eye.svg')); ?>" alt="icon" loading="lazy">
                                    </a>
                                    <a href="<?php echo e(route('shop.download-invoice', $order->id)); ?>" data-bs-toggle="tooltip"
                                        data-bs-placement="left" data-bs-title="Download Invoice"
                                        class="circleIcon btn-outline-secondary btn-sm">
                                        <img src="<?php echo e(asset('assets/icons-admin/download-alt.svg')); ?>" alt="icon" loading="lazy">
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

        <div class="row mb-4">
        <!-- Top Shops -->
        <?php if($businessModel == 'multi'): ?>
            <div class="col-xxl-4 col-lg-6 mt-3">
                <div class="card">
                    <div class="card-body">
                        <div class="cardTitleBox">
                            <h5 class="card-title chartTitle">
                                <?php echo e(__('Top Trending Shops')); ?>

                            </h5>
                        </div>

                        <div class="d-flex flex-column gap-1">
                            <?php $__currentLoopData = $topShops; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $shop): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <a href="<?php echo e(route('admin.shop.show', $shop->id)); ?>" class="customer-section">
                                    <div class="customer-details">
                                        <div class="customer-image">
                                            <img src="<?php echo e($shop->logo); ?>" alt="logo" loading="lazy"/>
                                        </div>
                                        <div class="customer-about">
                                            <p class="name text-dark">
                                                <?php echo e(Str::limit($shop->name, 30, '...')); ?>

                                            </p>
                                            <p class="order">
                                                <i class="bi bi-star-fill text-warning"></i>
                                                <i class="bi bi-star-fill text-warning"></i>
                                                <i class="bi bi-star-fill text-warning"></i>
                                                <i class="bi bi-star-half text-warning"></i>
                                                <?php echo e($shop->average_rating); ?>

                                            </p>
                                        </div>
                                    </div>
                                    <div class="border text-black px-2 py-1 flex-shrink-0"
                                        style="font-size: 13px; border-radius: 25px;">
                                        <div><?php echo e(__('Order')); ?>: <?php echo e($shop->orders_count); ?></div>
                                    </div>
                                </a>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Most Favorite Products -->
        <div class="col-xxl-4 col-lg-6 mt-3">
            <div class="card">
                <div class="card-body">
                    <div class="cardTitleBox">
                        <h5 class="card-title chartTitle">
                            <?php echo e(__('Most Favorite Products')); ?>

                        </h5>
                    </div>

                    <div class="d-flex flex-column gap-1">
                        <?php $__currentLoopData = $topFavorites; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <a href="<?php echo e(route('admin.product.show', $product->id)); ?>" class="customer-section">
                                <div class="customer-details">
                                    <div class="customer-image">
                                        <img src="<?php echo e($product->thumbnail); ?>" alt="icon" loading="lazy"/>
                                    </div>
                                    <div class="customer-about">
                                        <p class="name text-dark"><?php echo e(Str::limit($product->name, 30, '...')); ?></p>
                                        <div class="d-flex gap-1 align-items-center text-black">
                                            <i class="bi bi-heart-fill text-danger"></i> <?php echo e($product->favorites_count); ?>

                                        </div>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Selling Products -->
        <div class="col-xxl-4 col-lg-6 mt-3">
            <div class="card">
                <div class="card-body">
                    <div class="cardTitleBox">
                        <h5 class="card-title chartTitle">
                            <?php echo e(__('Top Selling Products')); ?>

                        </h5>
                    </div>

                    <div class="d-flex flex-column gap-1">
                        <?php $__currentLoopData = $topSellingProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <a href="<?php echo e(route('admin.product.show', $product->id)); ?>" class="customer-section">
                                <div class="customer-details">
                                    <div class="customer-image">
                                        <img src="<?php echo e($product->thumbnail); ?>" alt="icon" loading="lazy" />
                                    </div>
                                    <div class="customer-about">
                                        <p class="text-dark name">
                                            <?php echo e(Str::limit($product->name, 30, '...')); ?>

                                        </p>
                                        <p class="order">
                                            <i class="bi bi-star-fill text-warning"></i>
                                            <i class="bi bi-star-fill text-warning"></i>
                                            <i class="bi bi-star-fill text-warning"></i>
                                            <i class="bi bi-star-fill text-warning"></i>
                                            <i class="bi bi-star-half text-warning"></i>
                                            <span class="text-black ms-1"><?php echo e(number_format($product->reviews->avg('rating'), 1)); ?></span>
                                            <span class="text-secondary">(<?php echo e($product->reviews->count()); ?>)</span>
                                        </p>
                                    </div>
                                </div>
                                <div class="border text-black px-2 py-1 flex-shrink-0" style="font-size: 13px; border-radius: 25px;">
                                    <div><?php echo e(__('Sold')); ?>: <?php echo e($product->orders_count); ?></div>
                                </div>
                            </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <!-- CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // get the value of --theme-color and --theme-hover-color
        var themeColor = "<?php echo e($generaleSetting?->primary_color ?? '#EE456B'); ?>";
        var themeHoverColor = "<?php echo e($generaleSetting?->secondary_color ?? '#FEE5E8'); ?>";

        var currentSitatics = '';
        var date = '';

        $('.statisticsBtn').on('click', function () {
            $('.statisticsBtn').removeClass('active');
            $(this).addClass('active');
            var sitatics = $(this).data('value');

            if (sitatics != currentSitatics) {
                currentSitatics = sitatics;
                fetchOrdersChart();
            }
        });
        $('#dateStatistic').on('change', function () {
             date = $(this).val();
            if (date) {
                fetchOrdersChart();
            }
        });
        $('.resetBtn').on('click', function () {
             date = '';
            $('#dateStatistic').val('');
            fetchOrdersChart();
        });

        const fetchOrdersChart = () => {
            $.ajax({
                url: "<?php echo e(route('admin.dashboard.statistics')); ?>",
                method: 'POST',
                data: {
                    _token: '<?php echo e(csrf_token()); ?>',
                    type: currentSitatics,
                    date: date
                },
                success: (response) => {
                   var chartLabels = response.data.labels;
                   var chartData = response.data.values;
                   loadChart(chartLabels, chartData);

                   $('#totalOrder').text(response.data.total);
                }
            });
        }

        fetchOrdersChart();

        var isDarkMode = document.getElementById('appContent').classList.contains('app-theme-dark');
        var chartLabelColor = isDarkMode ? "#fff" : '#24262D';
        var chartBgColor = isDarkMode ? "#5a5a5b63" : themeHoverColor;

        const ctx = document.getElementById('myChart').getContext('2d');
        let myChart;

        function loadChart(chartLabels, chartData) {

            if (myChart) {
                myChart.destroy();
            }

            // Define your chart data
            const data = {
                labels: chartLabels,
                datasets: [{
                        type: 'bar',
                        label: 'Orders',
                        data: chartData,
                        backgroundColor: '#FAA7B5',
                        borderRadius: {
                            topLeft: 12,
                            topRight: 12,
                            bottomLeft: 0,
                            bottomRight: 0
                        },
                        borderColor: themeHoverColor,
                        borderSkipped: false
                    },
                    {
                        type: 'line',
                        label: 'Orders',
                        data: chartData,
                        borderColor: themeColor,
                        backgroundColor: chartBgColor,
                        fill: true,
                        tension: 0.5,
                        pointBackgroundColor: 'white',
                        pointBorderColor: 'rgba(255, 99, 132, 1)',
                        pointRadius: 5
                    }
                ]
            };

            // Chart configuration
            const config = {
                type: 'bar',
                data: data,
                options: {
                    responsive: true,
                    scales: {
                        x: {
                            stacked: false,
                            grid: {
                                display: false
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                borderDash: [5, 5],
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            };

            // Initialize the chart
            myChart = new Chart(ctx, config);
        }

        const shopType = "<?php echo e($generaleSetting?->shop_type); ?>";
        const ismultiShop = shopType != 'single' ? true : false;
        const labelsData = ismultiShop ? ["<?php echo e(__('Customer')); ?>", "<?php echo e(__('Shop')); ?>", "<?php echo e(__('Rider')); ?>"] : ["<?php echo e(__('Customer')); ?>", "<?php echo e(__('Rider')); ?>"];
        const chartData = ismultiShop ? ["<?php echo e($totalCustomer); ?>", "<?php echo e($totalShop); ?>", "<?php echo e($totalRider); ?>"] : ["<?php echo e($totalCustomer); ?>", "<?php echo e($totalRider); ?>"];
        const chartDataBg = isDarkMode ? ['#EE456B', '#318E55', '#067BFF'] : ['#EE456B', '#067BFF'];

        // customer, shop, rider chart
        const cutOut = document.getElementById('myPieChart').getContext('2d');
        new Chart(cutOut, {
            type: 'doughnut',
            data: {
                labels: labelsData,
                datasets: [{
                    data: chartData,
                    backgroundColor: chartDataBg,
                    hoverOffset: 4,
                    borderWidth: 0,
                }]
            },
            options: {
                cutout: '50%',
                rotation: -90,
                circumference: 180,
                responsive: true,
                maintainAspectRatio: true,
                aspectRatio: 1.5,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            boxWidth: 14,
                            font: {
                                size: 14
                            },
                            color: chartLabelColor,
                            padding: 20
                        }
                    }
                },
            }
        });

        // Hide the alert box after 5 seconds
        const hideAlert = () => {
            setTimeout(() => {
                $('#alertBox').slideUp();
            }, 5000);

            setTimeout(() => {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            }, 100);
        }
        hideAlert();
    </script>
    <?php if($flashSale): ?>
        <script>
            // Set the start and end date/time
            var startDateAndTime = "<?php echo e($flashSale->start_date); ?>T<?php echo e($flashSale->start_time); ?>";
            var endDateAndTime = "<?php echo e($flashSale->end_date); ?>T<?php echo e($flashSale->end_time); ?>";
            let startDate = new Date(startDateAndTime).getTime();
            let endDate = new Date(endDateAndTime).getTime();

            // Update the countdown every 1 second
            let countdownInterval = setInterval(() => {
                let now = new Date().getTime();

                // If current time is before the start date, show "Deal Coming" message
                if (now < startDate) {
                    let distanceToStart = startDate - now;

                    // Time calculations for days, hours, minutes, and seconds
                    let days = Math.floor(distanceToStart / (1000 * 60 * 60 * 24));
                    let hours = Math.floor((distanceToStart % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    let minutes = Math.floor((distanceToStart % (1000 * 60 * 60)) / (1000 * 60));
                    let seconds = Math.floor((distanceToStart % (1000 * 60)) / 1000);

                    // Display the countdown with a "Deal Coming" message
                    document.getElementById("days").innerHTML = String(days).padStart(2, '0');
                    document.getElementById("hours").innerHTML = String(hours).padStart(2, '0');
                    document.getElementById("minutes").innerHTML = String(minutes).padStart(2, '0');
                    document.getElementById("seconds").innerHTML = String(seconds).padStart(2, '0');
                    return;
                }

                // Once the current time is after the start date and before the end date, show the active countdown
                let distance = endDate - now;

                // If the deal has ended, stop the countdown and show the message
                if (distance < 0) {
                    clearInterval(countdownInterval);
                    document.getElementById("days").innerHTML = "00";
                    document.getElementById("hours").innerHTML = "00";
                    document.getElementById("minutes").innerHTML = "00";
                    document.getElementById("seconds").innerHTML = "00";
                    document.querySelector(".deal-text").innerHTML = "Deal Ended!";
                    return;
                }

                // Time calculations for days, hours, minutes, and seconds
                let days = Math.floor(distance / (1000 * 60 * 60 * 24));
                let hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                let minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                let seconds = Math.floor((distance % (1000 * 60)) / 1000);

                // Display the result
                document.getElementById("days").innerHTML = String(days).padStart(2, '0');
                document.getElementById("hours").innerHTML = String(hours).padStart(2, '0');
                document.getElementById("minutes").innerHTML = String(minutes).padStart(2, '0');
                document.getElementById("seconds").innerHTML = String(seconds).padStart(2, '0');
            }, 1000);
        </script>
    <?php endif; ?>
<?php $__env->stopPush(); ?>
<?php $__env->startPush('css'); ?>
    <style>
        .admin-dashboard {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .admin-dashboard .card,
        .admin-dashboard .alert {
            margin-top: 0 !important;
        }

        /* Flash Deal Alert Styles */
        .flash-deal-alert {
            background: url("<?php echo e(asset('assets/images/flash-sale.png')); ?>");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            border-radius: 8px;
            color: white;
            border-radius: 8px;
            padding: 16px 32px;
        }

        .deal-title,
        .deal-text {
            font-size: 24px;
            font-weight: 600;
            color: white;
            margin: 0;
            line-height: 32px;
        }

        /* Countdown Timer Styles */
        .countdown {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
        }
        .countdown-section {
            text-align: center;
            padding: 4px 8px;
            border-radius: 8px;
            background-color: white;
            min-width: 68px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .countdown-label {
            font-size: 12px;
            color: #000;
        }
        .countdown-time {
            font-size: 20px;
            font-weight: bold;
            color: var(--theme-color);
        }
        .addBtn{
            border-radius: 25px;
            padding: 10px 20px;
        }

        .admin-dashboard .dashboard-statistics-toolbar {
            justify-content: flex-end;
        }

        .admin-dashboard .dashboard-statistics-filters,
        .admin-dashboard .dashboard-statistics-date,
        .admin-dashboard .dashboard-statistics-reset {
            flex-shrink: 0;
        }

        .admin-dashboard .statisticsInput {
            min-width: 180px;
        }

        .admin-dashboard .tableAction {
            white-space: nowrap;
        }

        .admin-dashboard .tableAction .circleIcon,
        .admin-dashboard .tableAction .btn-outline-secondary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .admin-dashboard .customer-section {
            gap: 12px;
        }

        .admin-dashboard .customer-section .customer-details {
            min-width: 0;
            flex: 1 1 auto;
        }

        .admin-dashboard .customer-section .customer-about {
            min-width: 0;
        }

        .admin-dashboard .customer-section .name,
        .admin-dashboard .customer-section .order {
            overflow-wrap: anywhere;
        }

        @media (max-width: 1199.98px) {
            .admin-dashboard .flash-deal-alert {
                padding: 16px 20px;
            }

            .admin-dashboard .dashboard-statistics-toolbar {
                justify-content: flex-start;
            }
        }

        @media (max-width: 991.98px) {
            .admin-dashboard .flash-deal-alert {
                justify-content: center !important;
                text-align: center;
            }

            .admin-dashboard .wallet,
            .admin-dashboard .wallet-others,
            .admin-dashboard .dashboard-box,
            .admin-dashboard .orderStatus > .status {
                height: 100%;
            }

            .admin-dashboard .orderStatus > .status {
                min-width: calc(50% - 0.75rem);
            }
        }

        @media (max-width: 767.98px) {
            .admin-dashboard .flash-deal-alert {
                padding: 16px;
            }

            .admin-dashboard .deal-title,
            .admin-dashboard .deal-text {
                font-size: 20px;
                line-height: 28px;
            }

            .admin-dashboard .countdown {
                width: 100%;
            }

            .admin-dashboard .countdown-section {
                flex: 1 1 calc(50% - 8px);
                min-width: 0;
            }

            .admin-dashboard .dashboard-statistics-toolbar {
                width: 100%;
                gap: 12px !important;
            }

            .admin-dashboard .dashboard-statistics-filters,
            .admin-dashboard .dashboard-statistics-date,
            .admin-dashboard .dashboard-statistics-reset {
                width: 100%;
            }

            .admin-dashboard .dashboard-statistics-filters {
                justify-content: space-between;
            }

            .admin-dashboard .statisticsBtn {
                flex: 1 1 0;
                text-align: center;
            }

            .admin-dashboard .statisticsDivder {
                display: none;
            }

            .admin-dashboard .statisticsInput,
            .admin-dashboard .resetBtn,
            .admin-dashboard .addBtn {
                width: 100%;
            }

            .admin-dashboard .orderStatus > .status {
                min-width: 100%;
            }

            .admin-dashboard .tableAction {
                display: flex;
                gap: 8px;
                flex-wrap: wrap;
            }
        }

        @media (max-width: 575.98px) {
            .admin-dashboard .countdown-section {
                flex-basis: calc(50% - 8px);
                padding: 8px;
            }

            .admin-dashboard .dashboard-box .count,
            .admin-dashboard .wallet .balance {
                font-size: 24px;
            }

            .admin-dashboard .wallet .wallet-icon {
                width: 56px;
                height: 56px;
            }

            .admin-dashboard .wallet-others .amount {
                font-size: 20px;
            }

            .admin-dashboard .customer-section {
                align-items: flex-start;
                flex-wrap: wrap;
            }
        }
    </style>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/u939461333/domains/janmitram.com/public_html/app/resources/views/admin/dashboard.blade.php ENDPATH**/ ?>