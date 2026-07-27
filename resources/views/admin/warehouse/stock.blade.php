@extends('layouts.app')

@section('header-title', __('Refill Warehouse Inventory'))
@section('header-subtitle', __('Add supplier shipments or manual stock additions to warehouse inventory.'))

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.warehouse.show', $warehouse->id) }}" class="btn btn-outline-secondary btn-sm">
        <i class="fas fa-arrow-left me-1"></i> {{ __('Back to Warehouse Inventory') }}
    </a>
</div>

<div class="card border-0 shadow-sm rounded-12 col-lg-8 mx-auto bg-white">
    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0 fw-bold"><i class="fas fa-cubes text-primary me-2"></i>{{ __('Refill Physical Stock') }} — {{ $warehouse->name }}</h5>
        <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill">{{ __('Warehouse Direct Deposit') }}</span>
    </div>
    <div class="card-body p-4">
        <form action="{{ route('admin.warehouse.stock.add', $warehouse->id) }}" method="POST">
            @csrf
            <p class="text-muted small mb-4">
                {{ __('Select a physical master product to deposit additional physical inventory into') }} <strong>{{ $warehouse->name }}</strong>. {{ __('This transaction will be logged in the immutable stock ledger.') }}
            </p>

            <div class="mb-4">
                <label class="form-label fw-bold small required">{{ __('Select Physical Master Product') }}</label>
                <select name="product_id" class="form-select form-select-lg @error('product_id') is-invalid @enderror" required>
                    <option value="">{{ __('-- Select Product --') }}</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}">
                            {{ $product->name }} — {{ __('Current In Stock:') }} {{ $product->current_wh_qty }} {{ __('units') }}
                        </option>
                    @endforeach
                </select>
                @error('product_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="row">
                <div class="col-md-6 mb-4">
                    <label class="form-label fw-bold small">{{ __('Color Variant (Optional)') }}</label>
                    <select name="color_id" class="form-select">
                        <option value="">{{ __('-- Standard / No Color Variant --') }}</option>
                        @foreach($colors as $color)
                            <option value="{{ $color->id }}">{{ $color->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6 mb-4">
                    <label class="form-label fw-bold small">{{ __('Size Variant (Optional)') }}</label>
                    <select name="size_id" class="form-select">
                        <option value="">{{ __('-- Standard / No Size Variant --') }}</option>
                        @foreach($sizes as $size)
                            <option value="{{ $size->id }}">{{ $size->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold small required">{{ __('Quantity to Deposit / Refill') }}</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="fas fa-plus-circle text-success"></i></span>
                    <input type="number" name="quantity" class="form-control form-control-lg @error('quantity') is-invalid @enderror" min="1" value="50" required>
                </div>
                @error('quantity')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold small">{{ __('Stock Source / Reference Notes') }}</label>
                <textarea name="notes" class="form-control" rows="3" placeholder="{{ __('e.g. Supplier Batch Shipment #402, Factory Stock Replenishment') }}"></textarea>
            </div>

            <div class="d-flex justify-content-end gap-2 border-top pt-3">
                <a href="{{ route('admin.warehouse.show', $warehouse->id) }}" class="btn btn-light px-4">{{ __('Cancel') }}</a>
                <button type="submit" class="btn btn-success px-4 fw-bold">
                    <i class="fas fa-check me-1"></i> {{ __('Deposit & Refill Stock') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
