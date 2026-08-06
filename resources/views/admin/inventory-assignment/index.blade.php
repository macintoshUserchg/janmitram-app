@extends('layouts.app')

@section('header-title', __('Shop Inventory Assignment'))
@section('header-subtitle', __('Assign and push central warehouse physical inventory directly to a selected shop.'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h1 class="h3 mb-0 text-gray-800 fw-bold">{{ __('Shop Inventory Assignment') }}</h1>
        <p class="text-muted small mb-0">{{ __('Push stock from a warehouse to a shop instantly, with a generated dispatch invoice.') }}</p>
    </div>
    <a href="{{ route('admin.inventory-assignment.create') }}" class="btn btn-primary shadow-sm">
        <i class="fas fa-plus me-1"></i> {{ __('New Assignment') }}
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4">{{ session('error') }}</div>
@endif

<div class="card border-0 shadow-sm rounded-12">
    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0 fw-bold">{{ __('Inventory Assignment History') }}</h5>
        <span class="badge bg-light text-dark border">{{ $assignments->total() }} {{ __('Total Assignments') }}</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">
                            @include('admin.partials.sortable-header', ['label' => __('Assignment'), 'column' => 'id', 'route' => 'admin.inventory-assignment.index', 'sort' => $sort, 'direction' => $direction])
                        </th>
                        <th>{{ __('Target Shop') }}</th>
                        <th>{{ __('Source Warehouse') }}</th>
                        <th class="text-center">{{ __('Items') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>
                            @include('admin.partials.sortable-header', ['label' => __('Date'), 'column' => 'created_at', 'route' => 'admin.inventory-assignment.index', 'sort' => $sort, 'direction' => $direction])
                        </th>
                        <th class="text-center pe-4">{{ __('Invoice') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assignments as $a)
                        <tr>
                            <td class="ps-4 fw-bold text-primary">#{{ $a->id }}</td>
                            <td>
                                <div class="fw-bold text-dark">{{ $a->shop?->name }}</div>
                                <div class="small text-muted">{{ __('Shop ID:') }} #{{ $a->shop_id }}</div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    <i class="fas fa-warehouse me-1"></i> {{ $a->warehouse?->name }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-secondary px-3 py-1 rounded-pill">{{ $a->items->count() }}</span>
                            </td>
                            <td>
                                <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill fw-semibold">
                                    <i class="fas fa-check me-1"></i> {{ __('Assigned') }}
                                </span>
                            </td>
                            <td class="small text-muted">{{ $a->created_at?->format('Y-m-d H:i') }}</td>
                            <td class="text-center pe-4">
                                <a href="{{ route('admin.stock-request.invoice', $a->id) }}" class="btn btn-sm btn-outline-primary px-3">
                                    <i class="fas fa-file-invoice me-1"></i> {{ __('Invoice') }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fas fa-broom fs-1 mb-3 d-block text-secondary"></i>
                                {{ __('No inventory assignments yet.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white border-top py-0 px-0">
        @include('admin.partials.pagination', ['paginator' => $assignments])
    </div>
</div>
@endsection