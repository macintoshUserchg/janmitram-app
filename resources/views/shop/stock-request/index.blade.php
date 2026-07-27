@extends('shop.layouts.app')

@section('title', __('Warehouse Stock Requests'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h1 class="h3 mb-0">{{ __('Stock Requests to Warehouse') }}</h1>
        <p class="text-muted small mb-0">{{ __('Manage and request physical inventory dispatches from central warehouses.') }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('shop.shop-inventory.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-boxes me-1"></i> {{ __('View Shop Inventory') }}
        </a>
        <a href="{{ route('shop.stock-request.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> {{ __('New Stock Request') }}
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}</div>
@endif

<!-- Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card shadow-sm border-0 bg-light">
            <div class="card-body">
                <div class="text-muted small fw-bold">{{ __('Total Requests') }}</div>
                <div class="h3 mb-0 text-primary">{{ $totalRequests ?? 0 }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm border-0 bg-light">
            <div class="card-body">
                <div class="text-muted small fw-bold">{{ __('Pending Approval') }}</div>
                <div class="h3 mb-0 text-warning">{{ $pendingRequests ?? 0 }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm border-0 bg-light">
            <div class="card-body">
                <div class="text-muted small fw-bold">{{ __('Fulfilled & Stocked') }}</div>
                <div class="h3 mb-0 text-success">{{ $completedRequests ?? 0 }}</div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white pb-0 border-bottom-0">
        <ul class="nav nav-tabs card-header-tabs">
            <li class="nav-item">
                <a class="nav-link {{ !request('status') ? 'active' : '' }}" href="{{ route('shop.stock-request.index') }}">
                    {{ __('All Requests') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request('status') === 'pending' ? 'active' : '' }}" href="{{ route('shop.stock-request.index', ['status' => 'pending']) }}">
                    {{ __('Pending') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request('status') === 'completed' ? 'active' : '' }}" href="{{ route('shop.stock-request.index', ['status' => 'completed']) }}">
                    {{ __('Completed') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request('status') === 'rejected' ? 'active' : '' }}" href="{{ route('shop.stock-request.index', ['status' => 'rejected']) }}">
                    {{ __('Rejected') }}
                </a>
            </li>
        </ul>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>{{ __('Request ID') }}</th>
                        <th>{{ __('Target Warehouse') }}</th>
                        <th>{{ __('Items Count') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Submitted Date') }}</th>
                        <th class="text-center">{{ __('Action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $req)
                        <tr>
                            <td class="fw-bold">#{{ $req->id }}</td>
                            <td class="fw-bold">{{ $req->warehouse?->name }}</td>
                            <td><span class="badge bg-secondary">{{ $req->items->count() }} {{ __('items') }}</span></td>
                            <td>
                                @if($req->status === 'pending')
                                    <span class="badge bg-warning text-dark">{{ __('Pending Approval') }}</span>
                                @elseif($req->status === 'completed')
                                    <span class="badge bg-success">{{ __('Approved & Stock Added') }}</span>
                                @else
                                    <span class="badge bg-danger">{{ __('Rejected') }}</span>
                                @endif
                            </td>
                            <td>{{ $req->created_at?->format('Y-m-d H:i') }}</td>
                            <td class="text-center">
                                <a href="{{ route('shop.stock-request.show', $req->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i> {{ __('View Details') }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">{{ __('No stock requests found.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($requests->hasPages())
        <div class="card-footer">
            {{ $requests->links() }}
        </div>
    @endif
</div>
@endsection
