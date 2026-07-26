@extends('layouts.app')

@section('title', __('Review Stock Request'))

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.stock-request.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="fas fa-arrow-left me-1"></i> {{ __('Back to Stock Requests') }}
    </a>
</div>

@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="card shadow-sm mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">{{ __('Stock Request') }} #{{ $stockRequest->id }}</h5>
        <div>
            @if($stockRequest->status === 'pending')
                <span class="badge bg-warning text-dark fs-6">{{ __('Pending Review') }}</span>
            @elseif($stockRequest->status === 'completed')
                <span class="badge bg-success fs-6">{{ __('Completed / Fulfilled') }}</span>
            @else
                <span class="badge bg-danger fs-6">{{ __('Rejected') }}</span>
            @endif
        </div>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-4">
                <span class="text-muted d-block">{{ __('Requesting Shop') }}</span>
                <strong>{{ $stockRequest->shop?->name }}</strong>
            </div>
            <div class="col-md-4">
                <span class="text-muted d-block">{{ __('Source Warehouse') }}</span>
                <strong>{{ $stockRequest->warehouse?->name }}</strong>
            </div>
            <div class="col-md-4">
                <span class="text-muted d-block">{{ __('Requested Date') }}</span>
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

<div class="card shadow-sm mb-4">
    <div class="card-header bg-white">
        <h5 class="card-title mb-0">{{ __('Requested Items & Availability Check') }}</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>{{ __('Product') }}</th>
                        <th>{{ __('Variant') }}</th>
                        <th>{{ __('Requested Qty') }}</th>
                        <th>{{ __('Warehouse Stock') }}</th>
                        <th>{{ __('Status / Shortfall') }}</th>
                        <th>{{ __('Action If Short') }}</th>
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
                            <td class="fw-bold">{{ $item->product?->name }}</td>
                            <td>
                                @if($item->color || $item->size)
                                    {{ $item->color?->name }} {{ $item->size?->name }}
                                @else
                                    —
                                @endif
                            </td>
                            <td><span class="badge bg-primary fs-6">{{ $item->quantity }}</span></td>
                            <td><span class="badge bg-info fs-6">{{ $available }}</span></td>
                            <td>
                                @if($shortfall > 0)
                                    <span class="badge bg-danger">{{ __('Shortfall:') }} {{ $shortfall }}</span>
                                @else
                                    <span class="badge bg-success">{{ __('Sufficient') }}</span>
                                @endif
                            </td>
                            <td>
                                @if($shortfall > 0 && $stockRequest->status === 'pending')
                                    <a href="{{ route('admin.warehouse-transfer.create') }}?to_warehouse_id={{ $stockRequest->warehouse_id }}&product_id={{ $item->product_id }}&quantity={{ $shortfall }}" class="btn btn-sm btn-outline-warning">
                                        <i class="fas fa-exchange-alt me-1"></i> {{ __('Transfer Stock First') }}
                                    </a>
                                @else
                                    —
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
    <div class="d-flex justify-content-end gap-2">
        <form action="{{ route('admin.stock-request.reject', $stockRequest->id) }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-outline-danger">{{ __('Reject Request') }}</button>
        </form>

        <form action="{{ route('admin.stock-request.approve', $stockRequest->id) }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-success" {{ $hasShortfall ? 'disabled' : '' }}>
                <i class="fas fa-check me-1"></i> {{ __('Approve & Fulfill Request') }}
            </button>
        </form>
    </div>
    @if($hasShortfall)
        <p class="text-danger text-end small mt-2">
            <i class="fas fa-exclamation-triangle me-1"></i> {{ __('Cannot approve while stock shortfall exists. Transfer stock to warehouse first.') }}
        </p>
    @endif
@endif
@endsection
