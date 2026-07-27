@extends('shop.layouts.app')

@section('title', __('My Shop Inventory'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <a href="{{ route('shop.stock-request.index') }}" class="btn btn-outline-secondary btn-sm mb-2">
            <i class="fas fa-arrow-left me-1"></i> {{ __('Back to Stock Requests') }}
        </a>
        <h1 class="h3 mb-0">{{ __('My Shop Inventory') }}</h1>
        <p class="text-muted small mb-0">{{ __('Physical products dispatched from central warehouses into your shop sellable stock.') }}</p>
    </div>
    <a href="{{ route('shop.stock-request.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i> {{ __('Request More Stock') }}
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white">
        <h5 class="card-title mb-0">{{ __('Dispatched Shop Stock Items') }}</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 70px;">{{ __('SL') }}</th>
                        <th>{{ __('Thumbnail') }}</th>
                        <th>{{ __('Product Name') }}</th>
                        <th>{{ __('Brand') }}</th>
                        <th class="text-center">{{ __('Price') }}</th>
                        <th class="text-center">{{ __('Available Stock') }}</th>
                        <th class="text-center">{{ __('Status') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $key => $product)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>
                                <img src="{{ $product->thumbnail }}" width="45" height="45" class="rounded object-fit-cover">
                            </td>
                            <td class="fw-bold">
                                {{ $product->name }}
                                @if($product->masterProduct)
                                    <div class="small text-muted font-monospace">{{ __('Master ID:') }} #{{ $product->master_product_id }}</div>
                                @endif
                            </td>
                            <td>{{ $product->brand?->name ?? '—' }}</td>
                            <td class="text-center fw-bold">{{ showCurrency($product->price) }}</td>
                            <td class="text-center">
                                <span class="badge {{ $product->quantity > 10 ? 'bg-success' : ($product->quantity > 0 ? 'bg-warning text-dark' : 'bg-danger') }} fs-6">
                                    {{ $product->quantity }} {{ __('units') }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($product->quantity > 0)
                                    <span class="badge bg-success">{{ __('In Stock') }}</span>
                                @else
                                    <span class="badge bg-danger">{{ __('Out of Stock') }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                {{ __('No stock inventory allocated to your shop yet. Click "Request More Stock" to order inventory.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($products->hasPages())
        <div class="card-footer">
            {{ $products->links() }}
        </div>
    @endif
</div>
@endsection
