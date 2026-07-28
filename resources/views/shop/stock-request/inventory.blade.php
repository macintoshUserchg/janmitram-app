@extends('layouts.app')

@section('title', __('My Shop Inventory'))

@section('content')
<div class="container-fluid px-0">
    <!-- Header Banner & Actions -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="{{ route('shop.stock-request.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                    <i class="fas fa-arrow-left me-1"></i> {{ __('Stock Requests') }}
                </a>
                <span class="text-muted small">/</span>
                <span class="badge bg-primary-subtle text-primary fw-semibold px-3 py-1 rounded-pill">{{ __('Shop Inventory Audit') }}</span>
            </div>
            <h2 class="h3 mb-0 fw-bold text-dark">{{ __('My Shop Inventory & Stock Ledger') }}</h2>
            <p class="text-muted small mb-0">{{ __('Live view of physical inventory allocated to your shop from Central Logistics Fulfillment Hubs.') }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('shop.stock-request.create') }}" class="btn btn-primary fw-bold rounded-pill px-4 shadow-sm">
                <i class="fas fa-dolly me-1"></i> {{ __('Request Central Restock') }}
            </a>
        </div>
    </div>

    <!-- Linked Warehouse Fulfillment Node Banner -->
    @if($warehouse)
    <div class="card border-0 shadow-sm rounded-16 mb-4 bg-gradient bg-primary text-white position-relative overflow-hidden">
        <div class="card-body p-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 position-relative z-1">
                <div>
                    <span class="badge bg-white text-primary fw-bold mb-2 px-3 py-1 rounded-pill">
                        <i class="fas fa-link me-1"></i> {{ __('Linked Central Warehouse Node') }}
                    </span>
                    <h3 class="mb-1 text-white fw-bold"><i class="fas fa-warehouse me-2"></i>{{ $warehouse->name }}</h3>
                    <p class="mb-0 text-white-50 small"><i class="fas fa-map-marker-alt me-1"></i> {{ $warehouse->address ?? __('Primary Logistics Central Hub #') . $warehouse->id }}</p>
                </div>
                <div class="text-end">
                    <span class="badge bg-success text-white fw-bold px-3 py-2 fs-6 rounded-pill">
                        <i class="fas fa-check-circle me-1"></i> {{ __('Active Dispatch Link') }}
                    </span>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Summary Metrics Grid -->
    <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-16 p-3 bg-white h-100 border-start border-4 border-primary">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small text-uppercase fw-semibold d-block mb-1">{{ __('Allocated SKU Lines') }}</span>
                        <h3 class="fw-bold mb-0 text-dark">{{ number_format($totalSkuLines) }}</h3>
                        <span class="small text-muted">{{ __('Physical catalog items') }}</span>
                    </div>
                    <div class="bg-primary-subtle text-primary p-3 rounded-circle fs-3">
                        <i class="fas fa-cubes"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-16 p-3 bg-white h-100 border-start border-4 border-success">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small text-uppercase fw-semibold d-block mb-1">{{ __('Total Available Units') }}</span>
                        <h3 class="fw-bold mb-0 text-success">{{ number_format($totalStockUnits) }}</h3>
                        <span class="small text-success fw-semibold"><i class="fas fa-box me-1"></i>{{ __('Ready for retail sale') }}</span>
                    </div>
                    <div class="bg-success-subtle text-success p-3 rounded-circle fs-3">
                        <i class="fas fa-boxes"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-16 p-3 bg-white h-100 border-start border-4 border-info">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small text-uppercase fw-semibold d-block mb-1">{{ __('Total Inventory Value') }}</span>
                        <h3 class="fw-bold mb-0 text-info">{{ showCurrency($totalInventoryValue) }}</h3>
                        <span class="small text-muted">{{ __('Calculated sellable value') }}</span>
                    </div>
                    <div class="bg-info-subtle text-info p-3 rounded-circle fs-3">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-16 p-3 bg-white h-100 border-start border-4 {{ $lowStockCount > 0 ? 'border-danger' : 'border-secondary' }}">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small text-uppercase fw-semibold d-block mb-1">{{ __('Low / Out of Stock') }}</span>
                        <h3 class="fw-bold mb-0 {{ $lowStockCount > 0 ? 'text-danger' : 'text-secondary' }}">{{ number_format($lowStockCount) }}</h3>
                        <span class="small {{ $lowStockCount > 0 ? 'text-danger' : 'text-muted' }} fw-semibold">
                            @if($lowStockCount > 0)
                                <i class="fas fa-exclamation-triangle me-1"></i>{{ __('Requires warehouse restock') }}
                            @else
                                <i class="fas fa-check-circle me-1"></i>{{ __('All items healthy') }}
                            @endif
                        </span>
                    </div>
                    <div class="bg-warning-subtle text-warning p-3 rounded-circle fs-3">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Status Navigation Toolbar -->
    <div class="card border-0 shadow-sm rounded-16 mb-4 bg-white">
        <div class="card-body p-3">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <!-- Status Pills -->
                <div class="nav nav-pills gap-1">
                    <a href="{{ route('shop.shop-inventory.index', array_merge(request()->query(), ['status' => ''])) }}"
                       class="nav-link rounded-pill px-3 py-1 text-sm fw-semibold {{ !request('status') ? 'active bg-primary text-white' : 'text-secondary bg-light' }}">
                        {{ __('All SKUs') }} <span class="badge bg-white text-dark ms-1 rounded-pill">{{ $totalSkuLines }}</span>
                    </a>
                    <a href="{{ route('shop.shop-inventory.index', array_merge(request()->query(), ['status' => 'in_stock'])) }}"
                       class="nav-link rounded-pill px-3 py-1 text-sm fw-semibold {{ request('status') === 'in_stock' ? 'active bg-success text-white' : 'text-secondary bg-light' }}">
                        {{ __('In Stock') }}
                    </a>
                    <a href="{{ route('shop.shop-inventory.index', array_merge(request()->query(), ['status' => 'low_stock'])) }}"
                       class="nav-link rounded-pill px-3 py-1 text-sm fw-semibold {{ request('status') === 'low_stock' ? 'active bg-warning text-dark' : 'text-secondary bg-light' }}">
                        {{ __('Low Stock (<=10)') }}
                    </a>
                    <a href="{{ route('shop.shop-inventory.index', array_merge(request()->query(), ['status' => 'out_of_stock'])) }}"
                       class="nav-link rounded-pill px-3 py-1 text-sm fw-semibold {{ request('status') === 'out_of_stock' ? 'active bg-danger text-white' : 'text-secondary bg-light' }}">
                        {{ __('Out of Stock (0)') }}
                    </a>
                </div>

                <!-- Search Input -->
                <form method="GET" action="{{ route('shop.shop-inventory.index') }}" class="d-flex align-items-center gap-2">
                    @if(request('status'))
                        <input type="hidden" name="status" value="{{ request('status') }}">
                    @endif
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="{{ __('Search product name or SKU...') }}" value="{{ request('search') }}">
                        @if(request('search'))
                            <a href="{{ route('shop.shop-inventory.index', ['status' => request('status')]) }}" class="btn btn-light border-start-0 text-muted"><i class="fas fa-times"></i></a>
                        @endif
                    </div>
                    <button type="submit" class="btn btn-primary rounded-pill px-3"><i class="fas fa-search"></i></button>
                </form>
            </div>
        </div>
    </div>

    <!-- Main Inventory Audit Table -->
    <div class="card border-0 shadow-sm rounded-16 bg-white overflow-hidden">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0 fw-bold text-dark">
                <i class="fas fa-list-check me-2 text-primary"></i>{{ __('Shop Allocated Inventory Audit Table') }}
            </h5>
            <span class="badge bg-secondary-subtle text-secondary px-3 py-2 rounded-pill fw-semibold">
                {{ __('Showing') }} {{ $products->firstItem() ?? 0 }} - {{ $products->lastItem() ?? 0 }} {{ __('of') }} {{ $products->total() }} {{ __('SKUs') }}
            </span>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-uppercase small text-muted">
                        <tr>
                            <th style="width: 50px;" class="text-center">#</th>
                            <th>{{ __('Product & SKU') }}</th>
                            <th>{{ __('Brand & Categories') }}</th>
                            <th class="text-center">{{ __('Unit Price') }}</th>
                            <th class="text-center">{{ __('Inventory Valuation') }}</th>
                            <th class="text-center">{{ __('Sellable Stock') }}</th>
                            <th class="text-center">{{ __('Stock Status') }}</th>
                            <th class="text-center" style="width: 140px;">{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $key => $product)
                            <tr>
                                <td class="text-center text-muted fw-bold">{{ $products->firstItem() + $key }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="position-relative">
                                            <img src="{{ $product->thumbnail }}" width="48" height="48" class="rounded-12 border object-fit-cover shadow-sm">
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark mb-1">{{ $product->name }}</div>
                                            <div class="small text-muted font-monospace d-flex align-items-center gap-2">
                                                <span class="badge bg-light text-secondary border"><i class="fas fa-barcode me-1"></i>{{ $product->code ?? 'N/A' }}</span>
                                                @if($product->master_product_id)
                                                    <span class="badge bg-info-subtle text-info border border-info-subtle">{{ __('Master #') }}{{ $product->master_product_id }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <span class="fw-semibold text-dark">{{ $product->brand?->name ?? '—' }}</span>
                                        @if($product->categories->isNotEmpty())
                                            <div class="small text-muted">{{ $product->categories->pluck('name')->implode(', ') }}</div>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-center fw-bold text-dark">{{ showCurrency($product->price) }}</td>
                                <td class="text-center fw-bold text-primary">{{ showCurrency($product->quantity * $product->price) }}</td>
                                <td class="text-center">
                                    @if ($product->quantity > 10)
                                        <span class="badge bg-success fs-6 px-3 py-2 rounded-pill shadow-xs">{{ $product->quantity }} {{ __('units') }}</span>
                                    @elseif ($product->quantity > 0)
                                        <span class="badge bg-warning text-dark fs-6 px-3 py-2 rounded-pill shadow-xs">{{ $product->quantity }} {{ __('units') }}</span>
                                    @else
                                        <span class="badge bg-danger fs-6 px-3 py-2 rounded-pill shadow-xs">{{ __('Depleted') }}</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($product->quantity > 10)
                                        <span class="badge bg-success-subtle text-success px-3 py-1 rounded-pill"><i class="fas fa-check-circle me-1"></i>{{ __('In Stock') }}</span>
                                    @elseif($product->quantity > 0)
                                        <span class="badge bg-warning-subtle text-dark px-3 py-1 rounded-pill"><i class="fas fa-exclamation-circle me-1"></i>{{ __('Low Stock Alert') }}</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger px-3 py-1 rounded-pill"><i class="fas fa-times-circle me-1"></i>{{ __('Out of Stock') }}</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('shop.stock-request.create') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold" title="{{ __('Request Warehouse Restock') }}">
                                        <i class="fas fa-plus me-1"></i> {{ __('Restock') }}
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <div class="mb-3"><i class="fas fa-boxes fs-1 text-muted opacity-50"></i></div>
                                    <h5 class="fw-bold">{{ __('No Inventory Found') }}</h5>
                                    <p class="small text-muted mb-3">{{ __('No allocated physical stock records match your current search or status filter.') }}</p>
                                    <a href="{{ route('shop.stock-request.create') }}" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                                        <i class="fas fa-plus-circle me-1"></i> {{ __('Submit Stock Request Now') }}
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Incorporated Responsive Pagination Footer -->
        <div class="card-footer bg-white border-top py-3 px-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div class="small text-muted">
                    {{ __('Showing') }} <span class="fw-bold text-dark">{{ $products->firstItem() ?? 0 }}</span>
                    {{ __('to') }} <span class="fw-bold text-dark">{{ $products->lastItem() ?? 0 }}</span>
                    {{ __('of') }} <span class="fw-bold text-dark">{{ $products->total() }}</span> {{ __('entries') }}
                </div>
                <div>
                    {{ $products->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
