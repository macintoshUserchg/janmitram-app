@extends('layouts.app')

@section('header-title', __('Warehouse Management'))
@section('header-subtitle', __('Control central logistics hubs, physical stock allocation, and dispatch nodes.'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h1 class="h3 mb-0 text-gray-800 fw-bold">{{ __('Warehouse Logistics Hubs') }}</h1>
        <p class="text-muted small mb-0">{{ __('Manage physical inventory locations and linked distribution channels.') }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.stock-request.index') }}" class="btn btn-outline-primary shadow-sm">
            <i class="fas fa-boxes me-1"></i> {{ __('Shop Stock Requests') }}
        </a>
        <a href="{{ route('admin.warehouse.create') }}" class="btn btn-primary shadow-sm">
            <i class="fas fa-plus me-1"></i> {{ __('Add New Warehouse') }}
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4">{{ session('error') }}</div>
@endif

<!-- Logistics Metrics Bar -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-12 bg-white">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="p-3 bg-primary-subtle text-primary rounded-12">
                    <i class="fas fa-warehouse fs-3"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold">{{ __('Total Warehouses') }}</div>
                    <h3 class="fw-bold mb-0 text-dark">{{ $warehouses->count() }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-12 bg-white">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="p-3 bg-success-subtle text-success rounded-12">
                    <i class="fas fa-cubes fs-3"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold">{{ __('Central Master Hubs') }}</div>
                    <h3 class="fw-bold mb-0 text-success">{{ $warehouses->where('is_default', true)->count() }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-12 bg-white">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="p-3 bg-info-subtle text-info rounded-12">
                    <i class="fas fa-truck-loading fs-3"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold">{{ __('Sub-Distribution Hubs') }}</div>
                    <h3 class="fw-bold mb-0 text-info">{{ $warehouses->where('is_default', false)->count() }}</h3>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-12">
    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0 fw-bold">{{ __('Registered Warehouse Nodes') }}</h5>
        <span class="badge bg-light text-dark border">{{ $warehouses->total() }} {{ __('Total Nodes') }}</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">{{ __('ID') }}</th>
                        <th>{{ __('Warehouse Details') }}</th>
                        <th>{{ __('Owner / Shop') }}</th>
                        <th>{{ __('Classification') }}</th>
                        <th class="text-center">{{ __('Physical Stock Items') }}</th>
                        <th class="text-center pe-4">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($warehouses as $warehouse)
                        <tr>
                            <td class="ps-4 fw-bold text-secondary">#{{ $warehouse->id }}</td>
                            <td>
                                <div class="fw-bold text-dark fs-6">{{ $warehouse->name }}</div>
                                <div class="text-muted small"><i class="fas fa-map-marker-alt me-1"></i> {{ $warehouse->address ?? __('Main Logistics Hub') }}</div>
                            </td>
                            <td>
                                @if($warehouse->shop)
                                    <span class="badge bg-light text-dark border">
                                        <i class="fas fa-store me-1"></i> {{ $warehouse->shop->name }}
                                    </span>
                                @else
                                    <span class="badge bg-secondary">
                                        <i class="fas fa-building me-1"></i> {{ __('Root Central System') }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($warehouse->is_default)
                                    <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill fw-semibold">
                                        <i class="fas fa-star me-1"></i> {{ __('Central Hub') }}
                                    </span>
                                @else
                                    <span class="badge bg-light text-secondary border px-3 py-2 rounded-pill">
                                        <i class="fas fa-network-wired me-1"></i> {{ __('Sub Warehouse') }}
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge bg-primary fs-6 px-3 py-2 rounded-pill">
                                    {{ $warehouse->stocks_count ?? $warehouse->stocks->count() }} {{ __('skus') }}
                                </span>
                            </td>
                            <td class="text-center pe-4">
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="{{ route('admin.warehouse.show', $warehouse->id) }}" class="btn btn-sm btn-outline-info rounded-circle p-2" style="width:34px; height:34px;" data-bs-toggle="tooltip" title="{{ __('View Stock Inventory') }}">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.warehouse.edit', $warehouse->id) }}" class="btn btn-sm btn-outline-primary rounded-circle p-2" style="width:34px; height:34px;" data-bs-toggle="tooltip" title="{{ __('Edit Warehouse') }}">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if(!$warehouse->is_default)
                                        <form action="{{ route('admin.warehouse.destroy', $warehouse->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Are you sure you want to delete this warehouse?') }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger rounded-circle p-2" style="width:34px; height:34px;" data-bs-toggle="tooltip" title="{{ __('Delete Warehouse') }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-warehouse fs-1 mb-3 d-block text-secondary"></i>
                                {{ __('No warehouses found. Click "Add New Warehouse" to create one.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($warehouses->hasPages())
        <div class="card-footer bg-white border-top py-3">
            {{ $warehouses->links() }}
        </div>
    @endif
</div>
@endsection
