@extends('layouts.app')

@section('title', __('Edit Warehouse'))

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.warehouse.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="fas fa-arrow-left me-1"></i> {{ __('Back to List') }}
    </a>
</div>

<div class="card shadow-sm col-md-8 mx-auto">
    <div class="card-header bg-white">
        <h5 class="card-title mb-0">{{ __('Edit Warehouse') }} - {{ $warehouse->name }}</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.warehouse.update', $warehouse->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label required">{{ __('Warehouse Name') }}</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $warehouse->name) }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label">{{ __('Linked Shop (Owner)') }}</label>
                <select name="shop_id" class="form-select @error('shop_id') is-invalid @enderror">
                    <option value="">{{ __('-- Central / Admin Owned --') }}</option>
                    @foreach($shops as $shop)
                        <option value="{{ $shop->id }}" {{ old('shop_id', $warehouse->shop_id) == $shop->id ? 'selected' : '' }}>{{ $shop->name }}</option>
                    @endforeach
                </select>
                @error('shop_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label">{{ __('Address') }}</label>
                <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="3">{{ old('address', $warehouse->address) }}</textarea>
                @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('admin.warehouse.index') }}" class="btn btn-light">{{ __('Cancel') }}</a>
                <button type="submit" class="btn btn-primary">{{ __('Update Warehouse') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection
