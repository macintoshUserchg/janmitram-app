@extends('layouts.app')

@section('title', __('Create Warehouse'))

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.warehouse.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="fas fa-arrow-left me-1"></i> {{ __('Back to List') }}
    </a>
</div>

<div class="card shadow-sm col-md-8 mx-auto">
    <div class="card-header bg-white">
        <h5 class="card-title mb-0">{{ __('New Warehouse') }}</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.warehouse.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label required">{{ __('Warehouse Name') }}</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label font-weight-bold">{{ __('Parent Logistics Hub') }}</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="fas fa-warehouse text-primary"></i></span>
                    <input type="text" class="form-control bg-light" value="{{ $centralWarehouse?->name ?? __('Central Warehouse') }} ({{ __('Central Hub') }})" readonly>
                </div>
                <input type="hidden" name="shop_id" value="{{ $centralWarehouse?->shop_id ?? $shops->first()?->id }}">
                <div class="form-text text-muted small">{{ __('All created warehouses operate as sub-warehouses under Central Warehouse.') }}</div>
            </div>

            <div class="mb-3">
                <label class="form-label">{{ __('Address') }}</label>
                <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="3">{{ old('address') }}</textarea>
                @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('admin.warehouse.index') }}" class="btn btn-light">{{ __('Cancel') }}</a>
                <button type="submit" class="btn btn-primary">{{ __('Save Warehouse') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection
