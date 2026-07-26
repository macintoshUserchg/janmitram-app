@extends('layouts.app')

@section('title', __('Warehouse Transfers'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">{{ __('Warehouse Transfers') }}</h1>
    <a href="{{ route('admin.warehouse-transfer.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i> {{ __('New Stock Transfer') }}
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
                        <th>{{ __('ID') }}</th>
                        <th>{{ __('From Source') }}</th>
                        <th>{{ __('To Destination') }}</th>
                        <th>{{ __('Items Count') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Date') }}</th>
                        <th>{{ __('Action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transfers as $transfer)
                        <tr>
                            <td>#{{ $transfer->id }}</td>
                            <td class="fw-bold text-danger">{{ $transfer->fromWarehouse?->name }}</td>
                            <td class="fw-bold text-success">{{ $transfer->toWarehouse?->name }}</td>
                            <td>{{ $transfer->items->count() }}</td>
                            <td>
                                @if($transfer->status === 'pending')
                                    <span class="badge bg-warning text-dark">{{ __('Pending Execution') }}</span>
                                @elseif($transfer->status === 'completed')
                                    <span class="badge bg-success">{{ __('Completed') }}</span>
                                @else
                                    <span class="badge bg-secondary">{{ __('Cancelled') }}</span>
                                @endif
                            </td>
                            <td>{{ $transfer->created_at?->format('Y-m-d H:i') }}</td>
                            <td>
                                <a href="{{ route('admin.warehouse-transfer.show', $transfer->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i> {{ __('View Detail') }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">{{ __('No warehouse transfers recorded.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($transfers->hasPages())
        <div class="card-footer">
            {{ $transfers->links() }}
        </div>
    @endif
</div>
@endsection
