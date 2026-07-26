@extends('layouts.app')

@section('title', __('Add Stock to Warehouse'))

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.warehouse.show', $warehouse->id) }}" class="btn btn-outline-secondary btn-sm">
        <i class="fas fa-arrow-left me-1"></i> {{ __('Back to Warehouse Stock') }}
    </a>
</div>

<div class="card shadow-sm col-md-8 mx-auto">
    <div class="card-header bg-white">
        <h5 class="card-title mb-0">{{ __('Add Stock') }} — {{ $warehouse->name }}</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.warehouse.stock.add', $warehouse->id) }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label required">{{ __('Product') }}</label>
                <select name="product_id" class="form-select @error('product_id') is-invalid @enderror" required>
                    <option value="">{{ __('-- Select Product --') }}</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }} (ID: #{{ $product->id }})</option>
                    @endforeach
                </select>
                @error('product_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">{{ __('Color (Optional)') }}</label>
                    <select name="color_id" class="form-select">
                        <option value="">{{ __('-- None --') }}</option>
                        @foreach($colors as $color)
                            <option value="{{ $color->id }}">{{ $color->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">{{ __('Size (Optional)') }}</label>
                    <select name="size_id" class="form-select">
                        <option value="">{{ __('-- None --') }}</option>
                        @foreach($sizes as $size)
                            <option value="{{ $size->id }}">{{ $size->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label required">{{ __('Quantity to Add') }}</label>
                <input type="number" name="quantity" class="form-control @error('quantity') is-invalid @enderror" min="1" value="1" required>
                @error('quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label">{{ __('Notes / Source') }}</label>
                <textarea name="notes" class="form-control" rows="2" placeholder="{{ __('e.g. Supplier delivery, manual adjustment') }}"></textarea>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('admin.warehouse.show', $warehouse->id) }}" class="btn btn-light">{{ __('Cancel') }}</a>
                <button type="submit" class="btn btn-success">{{ __('Add Stock') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection
