@extends('layouts.app')

@section('header-title', __('Shop Stock Requests'))
@section('header-subtitle', __('Review and fulfill inventory dispatch requests submitted by vendor shops.'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h1 class="h3 mb-0 text-gray-800 fw-bold">{{ __('Shop Stock Requests') }}</h1>
        <p class="text-muted small mb-0">{{ __('Approve and dispatch central warehouse physical inventory to vendor shops.') }}</p>
    </div>
    <a href="{{ route('admin.warehouse.index') }}" class="btn btn-outline-secondary shadow-sm">
        <i class="fas fa-warehouse me-1"></i> {{ __('Manage Warehouses') }}
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4">{{ session('error') }}</div>
@endif

<!-- Metric Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-12 bg-white">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="p-3 bg-warning-subtle text-warning rounded-12">
                    <i class="fas fa-clock fs-3"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold">{{ __('Pending Review') }}</div>
                    <h3 class="fw-bold mb-0 text-warning">{{ $requests->where('status', 'pending')->count() }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-12 bg-white">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="p-3 bg-success-subtle text-success rounded-12">
                    <i class="fas fa-check-circle fs-3"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold">{{ __('Approved & Fulfilled') }}</div>
                    <h3 class="fw-bold mb-0 text-success">{{ $requests->where('status', 'completed')->count() }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-12 bg-white">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="p-3 bg-danger-subtle text-danger rounded-12">
                    <i class="fas fa-times-circle fs-3"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold">{{ __('Rejected Requests') }}</div>
                    <h3 class="fw-bold mb-0 text-danger">{{ $requests->where('status', 'rejected')->count() }}</h3>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-12">
    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0 fw-bold">{{ __('Dispatched Stock Requests Queue') }}</h5>
        <span class="badge bg-light text-dark border">{{ $requests->total() }} {{ __('Total Requests') }}</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">{{ __('Request ID') }}</th>
                        <th>{{ __('Requesting Shop') }}</th>
                        <th>{{ __('Source Warehouse') }}</th>
                        <th class="text-center">{{ __('Items Count') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Date Submitted') }}</th>
                        <th class="text-center pe-4">{{ __('Action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $req)
                        <tr>
                            <td class="ps-4 fw-bold text-primary">#{{ $req->id }}</td>
                            <td>
                                <div class="fw-bold text-dark fs-6">{{ $req->shop?->name }}</div>
                                <div class="small text-muted">{{ __('Shop ID:') }} #{{ $req->shop_id }}</div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    <i class="fas fa-warehouse text-secondary me-1"></i> {{ $req->warehouse?->name }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-secondary fs-6 px-3 py-1 rounded-pill">
                                    {{ $req->items->count() }} {{ __('items') }}
                                </span>
                            </td>
                            <td>
                                @if($req->status === 'pending')
                                    <span class="badge bg-warning-subtle text-dark px-3 py-2 rounded-pill fw-semibold">
                                        <i class="fas fa-hourglass-half me-1"></i> {{ __('Pending Review') }}
                                    </span>
                                @elseif($req->status === 'completed')
                                    <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill fw-semibold">
                                        <i class="fas fa-check me-1"></i> {{ __('Fulfilled / Dispatched') }}
                                    </span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger px-3 py-2 rounded-pill fw-semibold">
                                        <i class="fas fa-times me-1"></i> {{ __('Rejected') }}
                                    </span>
                                @endif
                            </td>
                            <td class="small text-muted">{{ $req->created_at?->format('Y-m-d H:i') }}</td>
                            <td class="text-center pe-4">
                                <a href="{{ route('admin.stock-request.show', $req->id) }}" class="btn btn-sm btn-outline-primary px-3">
                                    <i class="fas fa-search me-1"></i> {{ __('Review Request') }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fas fa-inbox fs-1 mb-3 d-block text-secondary"></i>
                                {{ __('No stock requests currently in the queue.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($requests->hasPages())
        <div class="card-footer bg-white border-top py-3">
            {{ $requests->links() }}
        </div>
    @endif
</div>
@endsection
