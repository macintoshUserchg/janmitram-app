@extends('layouts.app')

@section('title', __('Warehouses'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">{{ __('Warehouse Management') }}</h1>
    <a href="{{ route('admin.warehouse.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i> {{ __('Add Warehouse') }}
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
                        <th>{{ __('Name') }}</th>
                        <th>{{ __('Owner Shop') }}</th>
                        <th>{{ __('Type') }}</th>
                        <th>{{ __('Stock Items') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($warehouses as $warehouse)
                        <tr>
                            <td>#{{ $warehouse->id }}</td>
                            <td class="fw-bold">{{ $warehouse->name }}</td>
                            <td>{{ $warehouse->shop?->name ?? __('Root System') }}</td>
                            <td>
                                @if($warehouse->is_default)
                                    <span class="badge bg-success">{{ __('Central Hub') }}</span>
                                @else
                                    <span class="badge bg-secondary">{{ __('Sub Warehouse') }}</span>
                                @endif
                            </td>
                            <td>{{ $warehouse->stocks_count ?? $warehouse->stocks->count() }}</td>
                            <td>
                                <a href="{{ route('admin.warehouse.show', $warehouse->id) }}" class="btn btn-sm btn-outline-info me-1">
                                    <i class="fas fa-eye"></i> {{ __('View Stock') }}
                                </a>
                                <a href="{{ route('admin.warehouse.edit', $warehouse->id) }}" class="btn btn-sm btn-outline-primary me-1">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if(!$warehouse->is_default)
                                    <form action="{{ route('admin.warehouse.destroy', $warehouse->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Are you sure?') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">{{ __('No warehouses found.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($warehouses->hasPages())
        <div class="card-footer">
            {{ $warehouses->links() }}
        </div>
    @endif
</div>
@endsection
