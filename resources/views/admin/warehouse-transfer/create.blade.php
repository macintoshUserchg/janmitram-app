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
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label required">{{ __('Product') }}</label>
                    <select name="items[0][product_id]" class="form-select" required>
                        <option value="">{{ __('-- Select Product --') }}</option>
                        @foreach($products as $product)
                            @php $whList = $product->warehouseStocks->where('quantity', '>', 0)->pluck('warehouse_id')->implode(','); @endphp
                            <option value="{{ $product->id }}" data-warehouses="{{ $whList }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">{{ __('Color (Optional)') }}</label>
                    <select name="items[0][color_id]" class="form-select">
                        <option value="">{{ __('Any / Default') }}</option>
                        @foreach($colors as $color)
                            <option value="{{ $color->id }}">{{ $color->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">{{ __('Size (Optional)') }}</label>
                    <select name="items[0][size_id]" class="form-select">
                        <option value="">{{ __('Any / Default') }}</option>
                        @foreach($sizes as $size)
                            <option value="{{ $size->id }}">{{ $size->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
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

@push('scripts')
<script>
    $(function() {
        const $whSelect = $('select[name="from_warehouse_id"]');
        const $productSelect = $('select[name="items[0][product_id]"]');

        function filterProducts() {
            const selectedWh = $whSelect.val();
            $productSelect.find('option').each(function() {
                if (!this.value) return;
                const warehouses = ($(this).data('warehouses') || '').toString().split(',');
                const hasStock = selectedWh ? warehouses.includes(selectedWh) : true;
                this.hidden = !hasStock;
            });
            if ($productSelect.find('option:selected').is(':hidden')) {
                $productSelect.val('');
            }
        }

        $whSelect.on('change', filterProducts);
        filterProducts();
    });
</script>
@endpush
@endsection
