@extends('layouts.app')

@section('title', __('Warehouse Transfer Details'))

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.warehouse-transfer.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="fas fa-arrow-left me-1"></i> {{ __('Back to Transfers') }}
    </a>
</div>

@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="card shadow-sm mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">{{ __('Stock Transfer') }} #{{ $warehouseTransfer->id }}</h5>
        <div>
            @if($warehouseTransfer->status === 'pending')
                <span class="badge bg-warning text-dark fs-6">{{ __('Pending Execution') }}</span>
            @elseif($warehouseTransfer->status === 'completed')
                <span class="badge bg-success fs-6">{{ __('Completed') }}</span>
            @else
                <span class="badge bg-secondary fs-6">{{ __('Cancelled') }}</span>
            @endif
        </div>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-4">
                <span class="text-muted d-block">{{ __('From Source Warehouse') }}</span>
                <strong class="text-danger fs-5">{{ $warehouseTransfer->fromWarehouse?->name }}</strong>
            </div>
            <div class="col-md-4">
                <span class="text-muted d-block">{{ __('To Destination Warehouse') }}</span>
                <strong class="text-success fs-5">{{ $warehouseTransfer->toWarehouse?->name }}</strong>
            </div>
            <div class="col-md-4">
                <span class="text-muted d-block">{{ __('Created Date') }}</span>
                <span>{{ $warehouseTransfer->created_at?->format('Y-m-d H:i') }}</span>
            </div>
        </div>

        @if($warehouseTransfer->notes)
            <div class="p-3 bg-light rounded">
                <strong>{{ __('Notes:') }}</strong> {{ $warehouseTransfer->notes }}
            </div>
        @endif
    </div>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-header bg-white">
        <h5 class="card-title mb-0">{{ __('Transfer Line Items') }}</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>{{ __('Product') }}</th>
                        <th>{{ __('Color') }}</th>
                        <th>{{ __('Size') }}</th>
                        <th>{{ __('Transfer Quantity') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($warehouseTransfer->items as $item)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    @if($item->product?->thumbnail)
                                        <img src="{{ $item->product->thumbnail }}" width="45" height="45" class="rounded object-fit-cover shadow-sm">
                                    @endif
                                    <div>
                                        <div class="fw-bold text-dark fs-6">{{ $item->product?->name }}</div>
                                        <span class="small text-muted font-monospace">{{ __('ID:') }} #{{ $item->product_id }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($item->color)
                                    <span class="badge bg-light text-dark border"><i class="fas fa-palette me-1 text-primary"></i> {{ $item->color->name }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if($item->size)
                                    <span class="badge bg-light text-dark border"><i class="fas fa-ruler me-1 text-info"></i> {{ $item->size->name }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td><span class="badge bg-primary fs-6 px-3 py-2 rounded-pill">{{ $item->quantity }} {{ __('units') }}</span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@if($warehouseTransfer->status === 'pending')
    <div class="d-flex justify-content-end gap-2">
        <form action="{{ route('admin.warehouse-transfer.cancel', $warehouseTransfer->id) }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-outline-secondary">{{ __('Cancel Transfer') }}</button>
        </form>

        <form action="{{ route('admin.warehouse-transfer.complete', $warehouseTransfer->id) }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-success">
                <i class="fas fa-check me-1"></i> {{ __('Execute Transfer (Deduct Source & Add Target)') }}
            </button>
        </form>
    </div>
@endif
@endsection
