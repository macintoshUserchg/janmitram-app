@extends('layouts.app')

@section('header-title', __('Project Guide'))
@section('header-subtitle', __('Comprehensive system documentation, architectural overview, and operational workflows.'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div class="d-flex align-items-center gap-3">
        <a href="{{ route('admin.generale-setting.index') }}" class="btn btn-outline-secondary shadow-sm d-flex align-items-center gap-1">
            <i class="fas fa-arrow-left"></i> {{ __('Back') }}
        </a>
        <div>
            <h4 class="fw-bold mb-1 text-dark">{{ __('Janmitram Master Project Guide') }}</h4>
            <p class="text-muted small mb-0">{{ __('Complete technical documentation, architectural specifications, and administration protocols.') }}</p>
        </div>
    </div>
    <div class="d-flex gap-2">
        @hasPermission('admin.payout.network')
            <a href="{{ route('admin.payout.network') }}" class="btn btn-outline-primary shadow-sm">
                <i class="fas fa-sitemap me-1"></i> {{ __('Payout Network') }}
            </a>
        @endhasPermission
        @hasPermission('admin.warehouse.index')
            <a href="{{ route('admin.warehouse.index') }}" class="btn btn-outline-secondary shadow-sm">
                <i class="fas fa-warehouse me-1"></i> {{ __('Warehouses') }}
            </a>
        @endhasPermission
    </div>
</div>

<!-- Banner Card -->
<div class="card border-0 shadow-sm rounded-12 mb-4 bg-dark text-white overflow-hidden position-relative">
    <div class="card-body p-4">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <span class="badge bg-primary text-white px-3 py-2 rounded-pill fw-bold mb-2">{{ __('System Architecture v2.5') }}</span>
                <h3 class="fw-bold mb-2 text-white">{{ __('Janmitram E-Commerce, MLM & Logistics Platform') }}</h3>
                <p class="mb-0 text-white-50 leading-relaxed">
                    {{ __('Janmitram is a multi-vendor e-commerce platform integrated with automated nearest-shop fulfillment routing (Haversine formula), a multi-tier MLM compensation engine, central warehouse logistics hubs, and real-time stock transfers.') }}
                </p>
            </div>
            <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                <i class="fas fa-cubes fa-5x text-white-50 opacity-25"></i>
            </div>
        </div>
    </div>
</div>

<!-- Technology Stack Grid -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-12 h-100">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="p-3 bg-danger-subtle text-danger rounded-12">
                    <i class="fab fa-laravel fs-2"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold">{{ __('Backend Framework') }}</div>
                    <h5 class="fw-bold mb-0 text-dark">Laravel v11 (PHP 8.2)</h5>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-12 h-100">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="p-3 bg-primary-subtle text-primary rounded-12">
                    <i class="fas fa-database fs-2"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold">{{ __('Database Engine') }}</div>
                    <h5 class="fw-bold mb-0 text-dark">MySQL (janmitram)</h5>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-12 h-100">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="p-3 bg-success-subtle text-success rounded-12">
                    <i class="fab fa-vuejs fs-2"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold">{{ __('Frontend Bundle') }}</div>
                    <h5 class="fw-bold mb-0 text-dark">Vue 3 + Vite SPA</h5>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-12 h-100">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="p-3 bg-info-subtle text-info rounded-12">
                    <i class="fas fa-user-shield fs-2"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold">{{ __('Security & Auth') }}</div>
                    <h5 class="fw-bold mb-0 text-dark">Spatie ACL Permissions</h5>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Core Architectural Modules -->
<div class="row g-4 mb-4">
    <!-- Module 1: Nearest Shop Order Fulfillment Routing -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-12 h-100">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <div class="p-2 bg-primary-subtle text-primary rounded-3">
                        <i class="fas fa-route fs-5"></i>
                    </div>
                    <h5 class="card-title mb-0 fw-bold">{{ __('Nearest Shop Fulfillment Routing Engine') }}</h5>
                </div>
                <span class="badge bg-primary text-white">{{ __('Smart Fulfillment') }}</span>
            </div>
            <div class="card-body p-4">
                <p class="text-muted small mb-3">
                    {{ __('Automatically routes customer orders to the geographically closest active shop with sufficient stock using spherical distance calculations.') }}
                </p>
                <ul class="list-group list-group-flush small">
                    <li class="list-group-item px-0 py-2 border-0 d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-check-circle text-primary me-2"></i><strong>{{ __('Haversine Distance Formula') }}:</strong> {{ __('Calculates exact kilometer distance between customer delivery (lat, lng) and active shop coordinates.') }}</span>
                    </li>
                    <li class="list-group-item px-0 py-2 border-0 d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-check-circle text-primary me-2"></i><strong>{{ __('Configurable Max Radius') }}:</strong> {{ __('Configurable via shop_allocation_max_radius setting (default: 25 km).') }}</span>
                    </li>
                    <li class="list-group-item px-0 py-2 border-0 d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-check-circle text-primary me-2"></i><strong>{{ __('Order Splitting') }}:</strong> {{ __('Splits multi-item cart items across optimal fulfilling shops when items are closest to different locations.') }}</span>
                    </li>
                    <li class="list-group-item px-0 py-2 border-0 d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-check-circle text-primary me-2"></i><strong>{{ __('Manual Shop Picker Override') }}:</strong> {{ __('Allows customers to manually select a fulfilling shop if preferred.') }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Module 2: MLM Payout & Commission Engine -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-12 h-100">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <div class="p-2 bg-success-subtle text-success rounded-3">
                        <i class="fas fa-wallet fs-5"></i>
                    </div>
                    <h5 class="card-title mb-0 fw-bold">{{ __('MLM Payout & Compensation Engine') }}</h5>
                </div>
                @hasPermission('admin.payout.guide')
                    <a href="{{ route('admin.payout.guide') }}" class="btn btn-sm btn-outline-success">
                        {{ __('Payout Guide') }}
                    </a>
                @endhasPermission
            </div>
            <div class="card-body p-4">
                <p class="text-muted small mb-3">
                    {{ __('Calculates monthly shop owner commissions via a Dual-Phase compensation structure based on personal sales and downline network tiers.') }}
                </p>
                <ul class="list-group list-group-flush small">
                    <li class="list-group-item px-0 py-2 border-0 d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-check-circle text-success me-2"></i><strong>{{ __('Phase 1 (Personal Sales)') }}:</strong> {{ __('10% commission on Delivered personal shop sales.') }}</span>
                    </li>
                    <li class="list-group-item px-0 py-2 border-0 d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-check-circle text-success me-2"></i><strong>{{ __('Phase 2 (Group Tiers)') }}:</strong> {{ __('5 Achievement Tiers (Level 0 through Level 4 based on group sales).') }}</span>
                    </li>
                    <li class="list-group-item px-0 py-2 border-0 d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-check-circle text-success me-2"></i><strong>{{ __('Hierarchical Tree') }}:</strong> {{ __('Interactive visual downline tree with node search and sponsor referral links.') }}</span>
                    </li>
                    <li class="list-group-item px-0 py-2 border-0 d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-check-circle text-success me-2"></i><strong>{{ __('Atomic Wallet Credits') }}:</strong> {{ __('Payouts credited directly to shop wallet ledger with is_commission = false flag.') }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Module 3: Central Warehouse Logistics -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-12 h-100">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <div class="p-2 bg-info-subtle text-info rounded-3">
                        <i class="fas fa-warehouse fs-5"></i>
                    </div>
                    <h5 class="card-title mb-0 fw-bold">{{ __('Central Warehouse & Stock Dispatch') }}</h5>
                </div>
                @hasPermission('admin.warehouse.index')
                    <a href="{{ route('admin.warehouse.index') }}" class="btn btn-sm btn-outline-info">
                        {{ __('Manage Warehouses') }}
                    </a>
                @endhasPermission
            </div>
            <div class="card-body p-4">
                <p class="text-muted small mb-3">
                    {{ __('Controls central inventory hubs, shop stock requests, inter-warehouse transfers, and audit ledgers.') }}
                </p>
                <ul class="list-group list-group-flush small">
                    <li class="list-group-item px-0 py-2 border-0 d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-check-circle text-info me-2"></i><strong>{{ __('Central Hubs') }}:</strong> {{ __('Centralized stock repositories managing master product inventory.') }}</span>
                    </li>
                    <li class="list-group-item px-0 py-2 border-0 d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-check-circle text-info me-2"></i><strong>{{ __('Stock Requests') }}:</strong> {{ __('Shops request inventory dispatches from central warehouses.') }}</span>
                    </li>
                    <li class="list-group-item px-0 py-2 border-0 d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-check-circle text-info me-2"></i><strong>{{ __('Inter-Warehouse Transfers') }}:</strong> {{ __('Dispatch and transfer stock between central hubs.') }}</span>
                    </li>
                    <li class="list-group-item px-0 py-2 border-0 d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-check-circle text-info me-2"></i><strong>{{ __('Stock Audit Ledger') }}:</strong> {{ __('Complete ledger tracking for every stock movement.') }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Module 4: Interactive Geolocation & Map Controls -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-12 h-100">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <div class="p-2 bg-warning-subtle text-warning rounded-3">
                        <i class="fas fa-map-marked-alt fs-5"></i>
                    </div>
                    <h5 class="card-title mb-0 fw-bold">{{ __('Interactive Map & Geolocation Controls') }}</h5>
                </div>
                <span class="badge bg-warning text-dark">{{ __('Leaflet + GPS') }}</span>
            </div>
            <div class="card-body p-4">
                <p class="text-muted small mb-3">
                    {{ __('Provides precise coordinate detection and manual Latitude/Longitude editing across all address forms.') }}
                </p>
                <ul class="list-group list-group-flush small">
                    <li class="list-group-item px-0 py-2 border-0 d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-check-circle text-warning me-2"></i><strong>{{ __('Leaflet OpenStreetMap') }}:</strong> {{ __('Interactive map with marker drag, click pin placement, and Nominatim search.') }}</span>
                    </li>
                    <li class="list-group-item px-0 py-2 border-0 d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-check-circle text-warning me-2"></i><strong>{{ __('GPS Auto-Location') }}:</strong> {{ __('1-click browser geolocation ("Use My Location") filling Lat/Lng fields.') }}</span>
                    </li>
                    <li class="list-group-item px-0 py-2 border-0 d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-check-circle text-warning me-2"></i><strong>{{ __('Bi-Directional Input Sync') }}:</strong> {{ __('Pasting or typing coordinates moves the map marker; dragging the marker updates Lat/Lng inputs.') }}</span>
                    </li>
                    <li class="list-group-item px-0 py-2 border-0 d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-check-circle text-warning me-2"></i><strong>{{ __('Address Management') }}:</strong> {{ __('Fully integrated across Vue SPA checkout, address edit, and modal forms.') }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Deployment & Server Synchronization Guide (Collapsible) -->
<div class="card border-0 shadow-sm rounded-12 mb-4">
    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center" data-bs-toggle="collapse" data-bs-target="#prodSyncProtocol" aria-expanded="false" aria-controls="prodSyncProtocol" style="cursor: pointer;">
        <h5 class="card-title mb-0 fw-bold d-flex align-items-center gap-2">
            <i class="fas fa-server text-warning"></i>
            <span>{{ __('Production Server Synchronization Protocol') }}</span>
            <i class="fas fa-chevron-down text-muted fs-6 ms-2"></i>
        </h5>
        <span class="badge bg-light text-dark border">{{ __('Hostinger Live Environment') }}</span>
    </div>
    <div class="collapse" id="prodSyncProtocol">
        <div class="card-body p-4">
            <div class="row g-4">
                <div class="col-lg-6">
                    <h6 class="fw-bold text-dark mb-2">{{ __('SSH Connection Details') }}</h6>
                    <div class="bg-dark text-light p-3 rounded-12 font-monospace small mb-3">
                        ssh -p 65002 u939461333@195.35.44.154<br>
                        # App Directory: /home/u939461333/domains/janmitram.com/public_html/app<br>
                        # PHP 8.2 Binary: /opt/alt/php82/usr/bin/php
                    </div>
                </div>
                <div class="col-lg-6">
                    <h6 class="fw-bold text-dark mb-2">{{ __('Standard Deployment Commands') }}</h6>
                    <div class="bg-dark text-light p-3 rounded-12 font-monospace small mb-3">
                        cd /home/u939461333/domains/janmitram.com/public_html/app<br>
                        git pull origin main<br>
                        /opt/alt/php82/usr/bin/php artisan migrate --force<br>
                        /opt/alt/php82/usr/bin/php artisan db:seed --class=PermissionSeeder --force<br>
                        /opt/alt/php82/usr/bin/php artisan cache:clear && /opt/alt/php82/usr/bin/php artisan config:clear
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Shortcuts -->
<div class="card border-0 shadow-sm rounded-12">
    <div class="card-header bg-white py-3 border-bottom">
        <h5 class="card-title mb-0 fw-bold">{{ __('Quick System Shortcuts') }}</h5>
    </div>
    <div class="card-body p-4">
        <div class="row g-3">
            @hasPermission('admin.payout.network')
                <div class="col-md-3">
                    <a href="{{ route('admin.payout.network') }}" class="btn btn-outline-primary w-100 p-3 text-start shadow-sm rounded-12">
                        <i class="fas fa-sitemap fs-4 mb-2 d-block text-primary"></i>
                        <strong class="d-block text-dark">{{ __('Payout Network') }}</strong>
                        <small class="text-muted">{{ __('Inspect MLM tree') }}</small>
                    </a>
                </div>
            @endhasPermission
            @hasPermission('admin.payout.index')
                <div class="col-md-3">
                    <a href="{{ route('admin.payout.index') }}" class="btn btn-outline-secondary w-100 p-3 text-start shadow-sm rounded-12">
                        <i class="fas fa-history fs-4 mb-2 d-block text-secondary"></i>
                        <strong class="d-block text-dark">{{ __('Payout History') }}</strong>
                        <small class="text-muted">{{ __('Audit credited payouts') }}</small>
                    </a>
                </div>
            @endhasPermission
            @hasPermission('admin.payout.run')
                <div class="col-md-3">
                    <a href="{{ route('admin.payout.run.form') }}" class="btn btn-outline-success w-100 p-3 text-start shadow-sm rounded-12">
                        <i class="fas fa-play fs-4 mb-2 d-block text-success"></i>
                        <strong class="d-block text-dark">{{ __('Run Payout') }}</strong>
                        <small class="text-muted">{{ __('Execute monthly run') }}</small>
                    </a>
                </div>
            @endhasPermission
            @hasPermission('admin.role.index')
                <div class="col-md-3">
                    <a href="{{ route('admin.role.index') }}" class="btn btn-outline-dark w-100 p-3 text-start shadow-sm rounded-12">
                        <i class="fas fa-user-shield fs-4 mb-2 d-block text-dark"></i>
                        <strong class="d-block text-dark">{{ __('Roles & Permissions') }}</strong>
                        <small class="text-muted">{{ __('ACL user access') }}</small>
                    </a>
                </div>
            @endhasPermission
        </div>
    </div>
</div>
@endsection
