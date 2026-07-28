@extends('layouts.app')

@section('title', __('Warehouse Stock Requests'))

@section('content')
<!-- Integrated Header & Linked Logistics Hub Banner Card -->
<div class="card border-0 shadow-sm rounded-12 mb-4 bg-gradient bg-primary text-white">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-3">
            <div>
                <a href="{{ route('shop.shop-inventory.index') }}" class="btn btn-sm btn-light text-primary fw-bold mb-2">
                    <i class="fas fa-boxes me-1"></i> {{ __('View Shop Inventory') }}
                </a>
                <h1 class="h3 mb-1 text-white fw-bold">{{ __('Warehouse Stock Requests') }}</h1>
                <p class="text-white-50 small mb-0">{{ __('Manage and request physical inventory dispatches from central warehouses into sellable shop stock.') }}</p>
            </div>
            <div>
                <a href="{{ route('shop.stock-request.create') }}" class="btn btn-warning text-dark fw-bold px-3 py-2">
                    <i class="fas fa-plus-circle me-1"></i> {{ __('Request Warehouse Stock') }}
                </a>
            </div>
        </div>

        @if($warehouse)
            <div class="pt-3 border-top border-white-20 d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-light text-primary fw-bold">{{ __('Linked Logistics Fulfillment Hub') }}</span>
                    <span class="fw-bold text-white fs-5"><i class="fas fa-warehouse me-1 text-warning"></i> {{ $warehouse->name }}</span>
                    @if($warehouse->address)
                        <span class="text-white-50 small">({{ $warehouse->address }})</span>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-4">{{ session('error') }}</div>
@endif

<!-- Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card shadow-sm border-0 bg-white rounded-12 p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small text-uppercase fw-semibold">{{ __('Total Requests') }}</div>
                    <div class="h3 mb-0 text-primary fw-bold">{{ $totalRequests ?? 0 }}</div>
                </div>
                <div class="bg-primary-subtle text-primary p-3 rounded-circle fs-4">
                    <i class="fas fa-clipboard-list"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm border-0 bg-white rounded-12 p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small text-uppercase fw-semibold">{{ __('Pending Approval') }}</div>
                    <div class="h3 mb-0 text-warning fw-bold">{{ $pendingRequests ?? 0 }}</div>
                </div>
                <div class="bg-warning-subtle text-warning p-3 rounded-circle fs-4">
                    <i class="fas fa-hourglass-half"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm border-0 bg-white rounded-12 p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small text-uppercase fw-semibold">{{ __('Fulfilled & Stocked') }}</div>
                    <div class="h3 mb-0 text-success fw-bold">{{ $completedRequests ?? 0 }}</div>
                </div>
                <div class="bg-success-subtle text-success p-3 rounded-circle fs-4">
                    <i class="fas fa-check-double"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-12 bg-white">
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
                <thead class="table-light text-uppercase small text-muted">
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
                            <td class="fw-bold text-dark">{{ $req->warehouse?->name }}</td>
                            <td><span class="badge bg-secondary px-2 py-1">{{ $req->items->count() }} {{ __('items') }}</span></td>
                            <td>
                                @if($req->status === 'pending')
                                    <span class="badge bg-warning-subtle text-dark px-3 py-2"><i class="fas fa-clock me-1"></i>{{ __('Pending Approval') }}</span>
                                @elseif($req->status === 'completed')
                                    <span class="badge bg-success-subtle text-success px-3 py-2"><i class="fas fa-check-circle me-1"></i>{{ __('Approved & Stocked') }}</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger px-3 py-2"><i class="fas fa-times-circle me-1"></i>{{ __('Rejected') }}</span>
                                @endif
                            </td>
                            <td>{{ $req->created_at?->format('Y-m-d H:i') }}</td>
                            <td class="text-center">
                                <a href="{{ route('shop.stock-request.show', $req->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold">
                                    <i class="fas fa-eye me-1"></i> {{ __('View Details') }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <div class="mb-2"><i class="fas fa-clipboard-list fs-1 text-muted"></i></div>
                                <h5>{{ __('No Stock Requests Found') }}</h5>
                                <p class="small mb-3">{{ __('You have not submitted any stock requests yet.') }}</p>
                                <a href="{{ route('shop.stock-request.create') }}" class="btn btn-primary fw-bold">
                                    <i class="fas fa-plus-circle me-1"></i> {{ __('Submit Stock Request Now') }}
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <!-- Integrated Pagination Bar -->
    <div class="card-footer bg-white border-top py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="small text-muted">
            @if($requests->total() > 0)
                {{ __('Showing') }} <span class="fw-bold">{{ $requests->firstItem() }}</span> {{ __('to') }} <span class="fw-bold">{{ $requests->lastItem() }}</span> {{ __('of') }} <span class="fw-bold">{{ $requests->total() }}</span> {{ __('entries') }}
            @else
                {{ __('Showing 0 entries') }}
            @endif
        </div>
        <div>
            {{ $requests->links() }}
        </div>
    </div>
</div>
@endsection
