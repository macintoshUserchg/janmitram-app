@extends('shop.layouts.app')

@section('title', __('Warehouse Stock Requests'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">{{ __('Stock Requests to Warehouse') }}</h1>
    <a href="{{ route('shop.stock-request.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i> {{ __('Request Stock from Warehouse') }}
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="card shadow-sm">
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
                        <th>{{ __('Action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $req)
                        <tr>
                            <td>#{{ $req->id }}</td>
                            <td class="fw-bold">{{ $req->warehouse?->name }}</td>
                            <td>{{ $req->items->count() }}</td>
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
                            <td>
                                <a href="{{ route('shop.stock-request.show', $req->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i> {{ __('View Detail') }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">{{ __('No stock requests created yet.') }}</td>
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
