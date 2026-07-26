@extends('shop.layouts.app')

@section('title', __('Stock Request Detail'))

@section('content')
<div class="mb-4">
    <a href="{{ route('shop.stock-request.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="fas fa-arrow-left me-1"></i> {{ __('Back to Requests') }}
    </a>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">{{ __('Stock Request') }} #{{ $stockRequest->id }}</h5>
        <div>
            @if($stockRequest->status === 'pending')
                <span class="badge bg-warning text-dark fs-6">{{ __('Pending Admin Approval') }}</span>
            @elseif($stockRequest->status === 'completed')
                <span class="badge bg-success fs-6">{{ __('Approved & Fulfilled') }}</span>
            @else
                <span class="badge bg-danger fs-6">{{ __('Rejected') }}</span>
            @endif
        </div>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-6">
                <span class="text-muted d-block">{{ __('Target Warehouse') }}</span>
                <strong>{{ $stockRequest->warehouse?->name }}</strong>
            </div>
            <div class="col-md-6">
                <span class="text-muted d-block">{{ __('Submitted Date') }}</span>
                <span>{{ $stockRequest->created_at?->format('Y-m-d H:i') }}</span>
            </div>
        </div>

        @if($stockRequest->notes)
            <div class="p-3 bg-light rounded">
                <strong>{{ __('Notes:') }}</strong> {{ $stockRequest->notes }}
            </div>
        @endif
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white">
        <h5 class="card-title mb-0">{{ __('Requested Items') }}</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>{{ __('Product') }}</th>
                        <th>{{ __('Variant') }}</th>
                        <th>{{ __('Requested Quantity') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($stockRequest->items as $item)
                        <tr>
                            <td class="fw-bold">{{ $item->product?->name }}</td>
                            <td>{{ $item->color?->name ?? '' }} {{ $item->size?->name ?? '—' }}</td>
                            <td><span class="badge bg-primary fs-6">{{ $item->quantity }}</span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
