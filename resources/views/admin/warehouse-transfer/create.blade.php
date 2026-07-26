@extends('layouts.app')

@section('title', __('Create Warehouse Transfer'))

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.warehouse-transfer.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="fas fa-arrow-left me-1"></i> {{ __('Back to List') }}
    </a>
</div>

<div class="card shadow-sm col-md-9 mx-auto">
    <div class="card-header bg-white">
        <h5 class="card-title mb-0">{{ __('New Warehouse Stock Transfer') }}</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.warehouse-transfer.store') }}" method="POST">
            @csrf
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label required">{{ __('From Source Warehouse') }}</label>
                    <select name="from_warehouse_id" class="form-select @error('from_warehouse_id') is-invalid @enderror" required>
                        <option value="">{{ __('-- Select Source Warehouse --') }}</option>
                        @foreach($warehouses as $wh)
                            <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                        @endforeach
                    </select>
                    @error('from_warehouse_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label required">{{ __('To Target Warehouse') }}</label>
                    <select name="to_warehouse_id" class="form-select @error('to_warehouse_id') is-invalid @enderror" required>
                        <option value="">{{ __('-- Select Target Warehouse --') }}</option>
                        @foreach($warehouses as $wh)
                            <option value="{{ $wh->id }}" {{ request('to_warehouse_id') == $wh->id ? 'selected' : '' }}>{{ $wh->name }}</option>
                        @endforeach
                    </select>
                    @error('to_warehouse_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <hr class="my-4">

            <h6>{{ __('Transfer Items') }}</h6>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label required">{{ __('Product') }}</label>
                    <select name="items[0][product_id]" class="form-select" required>
                        <option value="">{{ __('-- Select Product --') }}</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label required">{{ __('Quantity') }}</label>
                    <input type="number" name="items[0][quantity]" class="form-control" min="1" value="{{ request('quantity', 1) }}" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">{{ __('Notes / Reference') }}</label>
                <textarea name="notes" class="form-control" rows="2" placeholder="{{ __('Optional transfer notes') }}"></textarea>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('admin.warehouse-transfer.index') }}" class="btn btn-light">{{ __('Cancel') }}</a>
                <button type="submit" class="btn btn-primary">{{ __('Create Transfer Record') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection
