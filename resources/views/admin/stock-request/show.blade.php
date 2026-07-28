@extends('layouts.app')

@section('header-title', __('Review Stock Request') . ' #' . $stockRequest->id)
@section('header-subtitle', __('Inspect requested inventory line items, verify warehouse stock availability, and approve dispatches.'))

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.stock-request.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="fas fa-arrow-left me-1"></i> {{ __('Back to Stock Requests') }}
    </a>
</div>

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4">{{ session('error') }}</div>
@endif
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4">{{ session('success') }}</div>
@endif

@if($stockRequest->status === 'completed')
    <div class="card border-0 shadow-sm rounded-12 mb-4 bg-light border-start border-success border-4">
        <div class="card-body p-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h6 class="fw-bold text-success mb-1"><i class="fas fa-file-invoice-dollar me-2"></i>{{ __('Stock Dispatch Invoice Available') }}</h6>
                <p class="small text-muted mb-0">{{ __('An official dispatch invoice note #INV-SR-') }}{{ str_pad((string)$stockRequest->id, 5, '0', STR_PAD_LEFT) }}{{ __(' is available for printing or PDF download.') }}</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.stock-request.invoice', [$stockRequest->id, 'download' => 'pdf']) }}" target="_blank" class="btn btn-sm btn-danger fw-bold">
                    <i class="fas fa-file-pdf me-1"></i> {{ __('Download PDF Invoice') }}
                </a>
                <a href="{{ route('admin.stock-request.invoice', $stockRequest->id) }}" target="_blank" class="btn btn-sm btn-outline-primary fw-bold">
                    <i class="fas fa-print me-1"></i> {{ __('Print Dispatch Note') }}
                </a>
            </div>
        </div>
    </div>
@endif

<!-- Overview Card -->
<div class="card border-0 shadow-sm rounded-12 mb-4 bg-white">
    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0 fw-bold">{{ __('Stock Request Summary') }} #{{ $stockRequest->id }}</h5>
        <div>
            @if($stockRequest->status === 'pending')
                <span class="badge bg-warning-subtle text-dark fs-6 px-3 py-2 rounded-pill fw-semibold">
                    <i class="fas fa-hourglass-half me-1"></i> {{ __('Pending Admin Review') }}
                </span>
            @elseif($stockRequest->status === 'completed')
                <span class="badge bg-success-subtle text-success fs-6 px-3 py-2 rounded-pill fw-semibold">
                    <i class="fas fa-check-circle me-1"></i> {{ __('Approved & Stock Dispatched') }}
                </span>
            @else
                <span class="badge bg-danger-subtle text-danger fs-6 px-3 py-2 rounded-pill fw-semibold">
                    <i class="fas fa-times-circle me-1"></i> {{ __('Rejected') }}
                </span>
            @endif
        </div>
    </div>
    <div class="card-body p-4">
        <div class="row g-3">
            <div class="col-md-4 border-end">
                <div class="text-muted small fw-semibold text-uppercase">{{ __('Requesting Vendor Shop') }}</div>
                <div class="fw-bold text-dark fs-5 mt-1"><i class="fas fa-store text-primary me-2"></i>{{ $stockRequest->shop?->name }}</div>
                <div class="small text-muted">{{ __('Shop ID:') }} #{{ $stockRequest->shop_id }}</div>
            </div>
            <div class="col-md-4 border-end">
                <div class="text-muted small fw-semibold text-uppercase">{{ __('Source Linked Warehouse') }}</div>
                <div class="fw-bold text-dark fs-5 mt-1"><i class="fas fa-warehouse text-secondary me-2"></i>{{ $stockRequest->warehouse?->name }}</div>
                <div class="small text-muted">{{ __('Warehouse ID:') }} #{{ $stockRequest->warehouse_id }}</div>
            </div>
            <div class="col-md-4">
                <div class="text-muted small fw-semibold text-uppercase">{{ __('Date & Time Submitted') }}</div>
                <div class="text-dark fs-5 mt-1"><i class="fas fa-calendar-alt text-info me-2"></i>{{ $stockRequest->created_at?->format('Y-m-d H:i') }}</div>
                <div class="small text-muted">{{ $stockRequest->created_at?->diffForHumans() }}</div>
            </div>
        </div>
        @if($stockRequest->notes)
            <div class="mt-4 p-3 bg-light rounded-12 border">
                <strong class="text-dark"><i class="fas fa-sticky-note me-1 text-warning"></i> {{ __('Vendor Notes:') }}</strong> 
                <span class="text-muted ms-1">{{ $stockRequest->notes }}</span>
            </div>
        @endif
    </div>
</div>

<div class="card border-0 shadow-sm rounded-12 mb-4">
    <div class="card-header bg-white py-3 border-bottom">
        <h5 class="card-title mb-0 fw-bold">{{ __('Requested Products & Warehouse Inventory Audit') }}</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">{{ __('Product Specification') }}</th>
                        <th>{{ __('Variants') }}</th>
                        <th class="text-center">{{ __('Requested Qty') }}</th>
                        <th class="text-center">{{ __('Warehouse Stock Available') }}</th>
                        <th class="text-center">{{ __('Inventory Status') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($itemDetails as $detail)
                        @php
                            $item = $detail['item'];
                            $available = $detail['available'];
                            $shortfall = $detail['shortfall'];
                        @endphp
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-3">
                                    @if($item->product?->thumbnail)
                                        <img src="{{ $item->product->thumbnail }}" width="45" height="45" class="rounded object-fit-cover shadow-sm">
                                    @endif
                                    <div>
                                        <div class="fw-bold text-dark fs-6">{{ $item->product?->name }}</div>
                                        <span class="small text-muted font-monospace">{{ __('Master ID:') }} #{{ $item->product_id }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($item->color || $item->size)
                                    <span class="badge bg-light text-dark border">
                                        {{ $item->color?->name }} {{ $item->size?->name }}
                                    </span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge bg-primary fs-6 px-3 py-2 rounded-pill">{{ $item->quantity }} {{ __('units') }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-info fs-6 px-3 py-2 rounded-pill">{{ $available }} {{ __('units') }}</span>
                            </td>
                            <td class="text-center">
                                @if($shortfall > 0)
                                    <span class="badge bg-danger-subtle text-danger px-3 py-2 rounded-pill fw-semibold">
                                        <i class="fas fa-exclamation-circle me-1"></i> {{ __('Shortfall:') }} {{ $shortfall }}
                                    </span>
                                @else
                                    <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill fw-semibold">
                                        <i class="fas fa-check-circle me-1"></i> {{ __('Sufficient Stock') }}
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@if($stockRequest->status === 'pending')
    <div class="card border-0 shadow-sm rounded-12 bg-white p-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h5 class="fw-bold mb-1">{{ __('Fulfillment Decision') }}</h5>
                <p class="text-muted small mb-0">{{ __('Approving will immediately decrement Central Warehouse stock and dispatch shop product copy inventory.') }}</p>
            </div>
            <div class="d-flex gap-2">
                <form action="{{ route('admin.stock-request.reject', $stockRequest->id) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to reject this stock request?') }}')">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger px-4">{{ __('Reject Request') }}</button>
                </form>

                <form action="{{ route('admin.stock-request.approve', $stockRequest->id) }}" method="POST" onsubmit="return confirm('{{ __('Approve and dispatch physical stock to shop now?') }}')">
                    @csrf
                    <button type="submit" class="btn btn-success px-4" {{ $hasShortfall ? 'disabled' : '' }}>
                        <i class="fas fa-check me-1"></i> {{ __('Approve & Fulfill Dispatch') }}
                    </button>
                </form>
            </div>
        </div>
        @if($hasShortfall)
            <div class="alert alert-warning border-0 mt-3 mb-0">
                <i class="fas fa-exclamation-triangle me-1"></i> {{ __('Cannot approve request due to inventory shortfall. Please restock Central Warehouse stock first.') }}
            </div>
        @endif
    </div>
@endif
@endsection
