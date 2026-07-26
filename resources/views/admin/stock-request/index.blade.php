@extends('layouts.app')

@section('title', __('Shop Stock Requests'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">{{ __('Shop Stock Requests') }}</h1>
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
                        <th>{{ __('Requesting Shop') }}</th>
                        <th>{{ __('Source Warehouse') }}</th>
                        <th>{{ __('Items Count') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Date') }}</th>
                        <th>{{ __('Action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $req)
                        <tr>
                            <td>#{{ $req->id }}</td>
                            <td class="fw-bold">{{ $req->shop?->name }}</td>
                            <td>{{ $req->warehouse?->name }}</td>
                            <td>{{ $req->items->count() }}</td>
                            <td>
                                @if($req->status === 'pending')
                                    <span class="badge bg-warning text-dark">{{ __('Pending Review') }}</span>
                                @elseif($req->status === 'completed')
                                    <span class="badge bg-success">{{ __('Fulfilled / Approved') }}</span>
                                @else
                                    <span class="badge bg-danger">{{ __('Rejected') }}</span>
                                @endif
                            </td>
                            <td>{{ $req->created_at?->format('Y-m-d H:i') }}</td>
                            <td>
                                <a href="{{ route('admin.stock-request.show', $req->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i> {{ __('Review Request') }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">{{ __('No stock requests found.') }}</td>
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
