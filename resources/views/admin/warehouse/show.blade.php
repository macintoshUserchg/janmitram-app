@extends('layouts.app')

@section('title', __('Warehouse Stock Details'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="{{ route('admin.warehouse.index') }}" class="btn btn-outline-secondary btn-sm mb-2">
            <i class="fas fa-arrow-left me-1"></i> {{ __('Back') }}
        </a>
        <h1 class="h3 mb-0">{{ $warehouse->name }} — {{ __('Stock Inventory') }}</h1>
    </div>
    <div class="d-flex gap-2">
        @if($stocks->count() > 0)
            <form action="{{ route('admin.warehouse.stock.clear', $warehouse->id) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to remove ALL stock items from this warehouse?') }}')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger">
                    <i class="fas fa-trash-alt me-1"></i> {{ __('Clear All Stock') }}
                </button>
            </form>
        @endif
        <a href="{{ route('admin.warehouse.stock', $warehouse->id) }}" class="btn btn-success">
            <i class="fas fa-plus me-1"></i> {{ __('Add Stock') }}
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <span class="text-muted d-block">{{ __('Owner Shop') }}</span>
                <strong>{{ $warehouse->shop?->name ?? __('Root System Central') }}</strong>
            </div>
            <div class="col-md-4">
                <span class="text-muted d-block">{{ __('Type') }}</span>
                @if($warehouse->is_default)
                    <span class="badge bg-success">{{ __('Central Hub') }}</span>
                @else
                    <span class="badge bg-secondary">{{ __('Sub Warehouse') }}</span>
                @endif
            </div>
            <div class="col-md-4">
                <span class="text-muted d-block">{{ __('Address') }}</span>
                <span>{{ $warehouse->address ?? __('N/A') }}</span>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white">
        <h5 class="card-title mb-0">{{ __('Stock Inventory Items') }}</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>{{ __('Product') }}</th>
                        <th>{{ __('Color') }}</th>
                        <th>{{ __('Size') }}</th>
                        <th>{{ __('Quantity') }}</th>
                        <th>{{ __('Last Updated') }}</th>
                        <th class="text-center">{{ __('Action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stocks as $stock)
                        <tr>
                            <td class="fw-bold">{{ $stock->product?->name }}</td>
                            <td>{{ $stock->color?->name ?? '—' }}</td>
                            <td>{{ $stock->size?->name ?? '—' }}</td>
                            <td>
                                <span class="badge {{ $stock->quantity > 10 ? 'bg-success' : ($stock->quantity > 0 ? 'bg-warning text-dark' : 'bg-danger') }} fs-6">
                                    {{ $stock->quantity }}
                                </span>
                            </td>
                            <td>{{ $stock->updated_at?->diffForHumans() }}</td>
                            <td class="text-center">
                                <form action="{{ route('admin.warehouse-stock.destroy', $stock->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Remove this item from warehouse stock?') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">{{ __('No stock inventory records in this warehouse.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($stocks->hasPages())
        <div class="card-footer">
            {{ $stocks->links() }}
        </div>
    @endif
</div>
@endsection
