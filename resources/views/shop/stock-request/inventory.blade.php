@extends('layouts.app')

@section('title', __('My Shop Inventory'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <a href="{{ route('shop.stock-request.index') }}" class="btn btn-outline-secondary btn-sm mb-2">
            <i class="fas fa-arrow-left me-1"></i> {{ __('Back to Stock Requests') }}
        </a>
        <h1 class="h3 mb-0 fw-bold">{{ __('My Shop Inventory & Stock Audit') }}</h1>
        <p class="text-muted small mb-0">{{ __('Physical products allocated and dispatched from your linked Central Warehouse into sellable shop inventory.') }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('shop.stock-request.create') }}" class="btn btn-primary fw-bold">
            <i class="fas fa-plus-circle me-1"></i> {{ __('Request Warehouse Stock') }}
        </a>
    </div>
</div>

<!-- Linked Warehouse Hub Card -->
@if($warehouse)
<div class="card border-0 shadow-sm rounded-12 mb-4 bg-gradient bg-primary text-white">
    <div class="card-body p-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <span class="badge bg-light text-primary fw-bold mb-2">{{ __('Linked Logistics Fulfillment Hub') }}</span>
            <h4 class="mb-1 text-white fw-bold"><i class="fas fa-warehouse me-2"></i>{{ $warehouse->name }}</h4>
            <p class="mb-0 text-white-50 small">{{ $warehouse->address ?? __('Primary Central Fulfillment Warehouse') }}</p>
        </div>
        <div class="text-end">
            <a href="{{ route('shop.stock-request.create') }}" class="btn btn-warning text-dark fw-bold">
                <i class="fas fa-dolly me-1"></i> {{ __('Submit Stock Request') }}
            </a>
        </div>
    </div>
</div>
@endif

<!-- Summary Metric Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-12 p-3 bg-white h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small text-uppercase fw-semibold d-block">{{ __('Allocated SKU Lines') }}</span>
                    <h3 class="fw-bold mb-0 text-dark">{{ number_format($totalSkuLines) }}</h3>
                </div>
                <div class="bg-primary-subtle text-primary p-3 rounded-circle fs-4">
                    <i class="fas fa-cubes"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-12 p-3 bg-white h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small text-uppercase fw-semibold d-block">{{ __('Total Available Units') }}</span>
                    <h3 class="fw-bold mb-0 text-success">{{ number_format($totalStockUnits) }} {{ __('units') }}</h3>
                </div>
                <div class="bg-success-subtle text-success p-3 rounded-circle fs-4">
                    <i class="fas fa-boxes"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-12 p-3 bg-white h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small text-uppercase fw-semibold d-block">{{ __('Total Inventory Value') }}</span>
                    <h3 class="fw-bold mb-0 text-info">{{ showCurrency($totalInventoryValue) }}</h3>
                </div>
                <div class="bg-info-subtle text-info p-3 rounded-circle fs-4">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-12 p-3 bg-white h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small text-uppercase fw-semibold d-block">{{ __('Low / Out of Stock') }}</span>
                    <h3 class="fw-bold mb-0 {{ $lowStockCount > 0 ? 'text-danger' : 'text-secondary' }}">{{ number_format($lowStockCount) }} {{ __('SKUs') }}</h3>
                </div>
                <div class="bg-warning-subtle text-warning p-3 rounded-circle fs-4">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filters and Search Toolbar -->
<div class="card border-0 shadow-sm rounded-12 mb-4 bg-white">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('shop.shop-inventory.index') }}" class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="{{ __('Search by product name or code...') }}" value="{{ request('search') }}">
                </div>
            </div>

            <div class="col-md-4">
                <select name="status" class="form-select" onchange="this.form.submit()">
                    <option value="">{{ __('All Stock Statuses') }}</option>
                    <option value="in_stock" {{ request('status') === 'in_stock' ? 'selected' : '' }}>{{ __('In Stock (>10 units)') }}</option>
                    <option value="low_stock" {{ request('status') === 'low_stock' ? 'selected' : '' }}>{{ __('Low Stock (<=10 units)') }}</option>
                    <option value="out_of_stock" {{ request('status') === 'out_of_stock' ? 'selected' : '' }}>{{ __('Out of Stock (0 units)') }}</option>
                </select>
            </div>

            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-secondary w-100"><i class="fas fa-filter me-1"></i> {{ __('Filter') }}</button>
                @if(request('search') || request('status'))
                    <a href="{{ route('shop.shop-inventory.index') }}" class="btn btn-light"><i class="fas fa-redo"></i></a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Detailed Inventory Table -->
<div class="card border-0 shadow-sm rounded-12 bg-white">
    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0 fw-bold"><i class="fas fa-list-check me-2 text-primary"></i>{{ __('Shop Allocated Inventory Audit Table') }}</h5>
        <span class="badge bg-secondary-subtle text-secondary px-3 py-2 rounded-pill">{{ $products->total() }} {{ __('Items') }}</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-uppercase small text-muted">
                    <tr>
                        <th style="width: 60px;" class="text-center">#</th>
                        <th>{{ __('Item & SKU') }}</th>
                        <th>{{ __('Category / Brand') }}</th>
                        <th class="text-center">{{ __('Unit Price') }}</th>
                        <th class="text-center">{{ __('Inventory Value') }}</th>
                        <th class="text-center">{{ __('Sellable Stock') }}</th>
                        <th class="text-center">{{ __('Status') }}</th>
                        <th class="text-center">{{ __('Action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $key => $product)
                        <tr>
                            <td class="text-center text-muted fw-bold">{{ $products->firstItem() + $key }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <img src="{{ $product->thumbnail }}" width="50" height="50" class="rounded border object-fit-cover">
                                    <div>
                                        <div class="fw-bold text-dark">{{ $product->name }}</div>
                                        <div class="small text-muted font-monospace">
                                            <span>{{ __('SKU:') }} {{ $product->code ?? 'N/A' }}</span>
                                            @if($product->master_product_id)
                                                <span class="ms-2 badge bg-light text-dark border">{{ __('Master #') }}{{ $product->master_product_id }}</span>
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
                            <td class="text-center fw-bold">{{ showCurrency($product->price) }}</td>
                            <td class="text-center fw-bold text-primary">{{ showCurrency($product->quantity * $product->price) }}</td>
                            <td class="text-center">
                                @if ($product->quantity > 10)
                                    <span class="badge bg-success fs-6 px-3 py-2">{{ $product->quantity }} {{ __('units') }}</span>
                                @elseif ($product->quantity > 0)
                                    <span class="badge bg-warning text-dark fs-6 px-3 py-2">{{ $product->quantity }} {{ __('units') }}</span>
                                @else
                                    <span class="badge bg-danger fs-6 px-3 py-2">{{ __('Stock Out') }}</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($product->quantity > 10)
                                    <span class="badge bg-success-subtle text-success px-2 py-1"><i class="fas fa-check-circle me-1"></i>{{ __('In Stock') }}</span>
                                @elseif($product->quantity > 0)
                                    <span class="badge bg-warning-subtle text-dark px-2 py-1"><i class="fas fa-exclamation-circle me-1"></i>{{ __('Low Stock Alert') }}</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger px-2 py-1"><i class="fas fa-times-circle me-1"></i>{{ __('Depleted') }}</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('shop.stock-request.create') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-bold" data-bs-toggle="tooltip" title="{{ __('Request Central Warehouse Restock') }}">
                                    <i class="fas fa-plus me-1"></i> {{ __('Request Restock') }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <div class="mb-2"><i class="fas fa-boxes fs-1 text-muted"></i></div>
                                <h5>{{ __('No Stock Allocated to Your Shop') }}</h5>
                                <p class="small mb-3">{{ __('Submit a stock request to your linked Central Warehouse to receive inventory dispatches.') }}</p>
                                <a href="{{ route('shop.stock-request.create') }}" class="btn btn-primary fw-bold">
                                    <i class="fas fa-plus-circle me-1"></i> {{ __('Submit Stock Request Now') }}
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($products->hasPages())
        <div class="card-footer bg-white border-top py-3">
            {{ $products->links() }}
        </div>
    @endif
</div>
@endsection
