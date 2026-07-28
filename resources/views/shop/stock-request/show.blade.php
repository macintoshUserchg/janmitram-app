@extends('layouts.app')

@section('title', __('Stock Request Detail'))

@section('content')
<!-- Integrated Header & Linked Logistics Hub Banner Card -->
<div class="card border-0 shadow-sm rounded-12 mb-4 bg-gradient bg-primary text-white">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-3">
            <div>
                <a href="{{ route('shop.stock-request.index') }}" class="btn btn-sm btn-light text-primary fw-bold mb-2">
                    <i class="fas fa-arrow-left me-1"></i> {{ __('Back to Stock Requests') }}
                </a>
                <h1 class="h3 mb-1 text-white fw-bold">{{ __('Stock Request Detail') }} #{{ $stockRequest->id }}</h1>
                <p class="text-white-50 small mb-0">{{ __('Audit details for stock request submitted to your assigned Central Warehouse.') }}</p>
            </div>
            <div>
                @if($stockRequest->status === 'pending')
                    <span class="badge bg-warning text-dark fs-6 px-3 py-2 fw-bold"><i class="fas fa-clock me-1"></i>{{ __('Pending Approval') }}</span>
                @elseif($stockRequest->status === 'completed')
                    <span class="badge bg-success fs-6 px-3 py-2 fw-bold"><i class="fas fa-check-circle me-1"></i>{{ __('Approved & Stocked') }}</span>
                @else
                    <span class="badge bg-danger fs-6 px-3 py-2 fw-bold"><i class="fas fa-times-circle me-1"></i>{{ __('Rejected') }}</span>
                @endif
            </div>
        </div>

        @if($stockRequest->warehouse)
            <div class="pt-3 border-top border-white-20 d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-light text-primary fw-bold">{{ __('Linked Logistics Fulfillment Hub') }}</span>
                    <span class="fw-bold text-white fs-5"><i class="fas fa-warehouse me-1 text-warning"></i> {{ $stockRequest->warehouse->name }}</span>
                    @if($stockRequest->warehouse->address)
                        <span class="text-white-50 small">({{ $stockRequest->warehouse->address }})</span>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>

<div class="card border-0 shadow-sm rounded-12 mb-4 bg-white">
    <div class="card-header bg-white py-3 border-bottom">
        <h5 class="card-title mb-0 fw-bold"><i class="fas fa-info-circle me-2 text-primary"></i>{{ __('Request Summary') }}</h5>
    </div>
    <div class="card-body p-4">
        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <span class="text-muted small text-uppercase d-block fw-semibold">{{ __('Target Warehouse') }}</span>
                <strong class="text-dark fs-5">{{ $stockRequest->warehouse?->name }}</strong>
            </div>
            <div class="col-md-6">
                <span class="text-muted small text-uppercase d-block fw-semibold">{{ __('Submitted Date & Time') }}</span>
                <span class="text-dark fs-6">{{ $stockRequest->created_at?->format('Y-m-d H:i:s') }}</span>
            </div>
        </div>

        @if($stockRequest->notes)
            <div class="p-3 bg-light rounded-12 border">
                <strong class="text-dark"><i class="fas fa-sticky-note me-1 text-warning"></i> {{ __('Request Notes:') }}</strong> {{ $stockRequest->notes }}
            </div>
        @endif
    </div>
</div>

<div class="card border-0 shadow-sm rounded-12 bg-white">
    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0 fw-bold"><i class="fas fa-list me-2 text-primary"></i>{{ __('Requested Items') }}</h5>
        <span class="badge bg-secondary-subtle text-secondary px-3 py-2 rounded-pill">{{ $stockRequest->items->count() }} {{ __('Items') }}</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-uppercase small text-muted">
                    <tr>
                        <th>{{ __('Product') }}</th>
                        <th>{{ __('Variant / Specifications') }}</th>
                        <th class="text-center">{{ __('Requested Quantity') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($stockRequest->items as $item)
                        <tr>
                            <td class="fw-bold text-dark">{{ $item->product?->name }}</td>
                            <td>{{ $item->color?->name ?? '' }} {{ $item->size?->name ?? '—' }}</td>
                            <td class="text-center"><span class="badge bg-primary fs-6 px-3 py-2">{{ $item->quantity }} {{ __('units') }}</span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
