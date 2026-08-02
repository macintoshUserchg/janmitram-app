@extends('layouts.app')

@section('title', __('Shop Inventory Assignment'))

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.inventory-assignment.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="fas fa-arrow-left me-1"></i> {{ __('Back to Assignments') }}
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white">
        <h5 class="card-title mb-0">{{ __('New Shop Inventory Assignment') }}</h5>
    </div>
    <div class="card-body">
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                <ul class="mb-0">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.inventory-assignment.store') }}" method="POST" id="assignmentForm">
            @csrf
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label required">{{ __('From Source Warehouse') }}</label>
                    <select name="from_warehouse_id" class="form-select @error('from_warehouse_id') is-invalid @enderror" required>
                        <option value="">{{ __('-- Select Source Warehouse --') }}</option>
                        @foreach($warehouses as $wh)
                            <option value="{{ $wh->id }}" {{ old('from_warehouse_id') == $wh->id ? 'selected' : '' }}>{{ $wh->name }}</option>
                        @endforeach
                    </select>
                    @error('from_warehouse_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label required">{{ __('Target Shop') }}</label>
                    <select name="shop_id" class="form-select @error('shop_id') is-invalid @enderror" required>
                        <option value="">{{ __('-- Select Shop --') }}</option>
                        @foreach($shops as $shop)
                            <option value="{{ $shop->id }}" {{ old('shop_id') == $shop->id ? 'selected' : '' }}>
                                {{ $shop->name }} ({{ $shop->user?->first_name ?? '—' }})
                            </option>
                        @endforeach
                    </select>
                    @error('shop_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <hr class="my-4">

            <h6>{{ __('Assign Products & Quantities') }}</h6>
            <div class="mb-3">
                <input type="text" id="catalogSearchInput" class="form-control"
                    placeholder="{{ __('Search catalog by name or SKU...') }}">
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="assignmentTable">
                    <thead class="table-light text-uppercase small text-muted">
                        <tr>
                            <th style="width: 50px;" class="text-center">
                                <input type="checkbox" class="form-check-input" id="selectAllCheckbox" title="{{ __('Select All Available') }}">
                            </th>
                            <th>{{ __('Product & SKU') }}</th>
                            <th class="text-center">{{ __('Available In Source') }}</th>
                            <th class="text-center" style="width: 180px;">{{ __('Quantity To Assign') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $index => $product)
                            @php
                                $stock = $product->warehouseStocks->where('quantity', '>', 0);
                                $whList = $stock->pluck('warehouse_id')->implode(',');
                            @endphp
                            <tr class="product-row"
                                data-name="{{ strtolower($product->name) }}"
                                data-code="{{ strtolower($product->code ?? '') }}"
                                data-warehouses="{{ $whList }}"
                                data-stock="{{ $stock->mapWithKeys(fn ($s) => [$s->warehouse_id => $s->quantity])->toJson() }}">
                                <td class="text-center">
                                    <input type="checkbox" class="form-check-input product-checkbox" id="check_{{ $product->id }}" data-target="qty_input_{{ $product->id }}">
                                </td>
                                <td>
                                    <label for="check_{{ $product->id }}" class="fw-bold text-dark mb-0">{{ $product->name }}</label>
                                    <div class="small text-muted font-monospace">SKU: {{ $product->code ?? 'N/A' }}</div>
                                </td>
                                <td class="text-center" id="avail_{{ $product->id }}">
                                    <span class="badge bg-secondary-subtle text-secondary">{{ __('Select source') }}</span>
                                </td>
                                <td class="text-center">
                                    <input type="hidden" name="items[{{ $index }}][product_id]" value="{{ $product->id }}" class="product-id-field" disabled data-id="{{ $product->id }}">
                                    <input type="number" name="items[{{ $index }}][quantity]" id="qty_input_{{ $product->id }}"
                                        class="form-control form-control-sm text-center qty-input" min="1" value="1" disabled>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mb-3 mt-4">
                <label class="form-label">{{ __('Notes / Reference') }}</label>
                <textarea name="notes" class="form-control" rows="2" placeholder="{{ __('Optional assignment notes') }}">{{ old('notes') }}</textarea>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('admin.inventory-assignment.index') }}" class="btn btn-light">{{ __('Cancel') }}</a>
                <button type="submit" class="btn btn-primary" id="submitBtn" disabled>{{ __('Assign Stock') }}</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
$(function() {
    const $whSelect = $('select[name="from_warehouse_id"]');
    const $checkboxes = $('.product-checkbox');
    const $selectAll = $('#selectAllCheckbox');
    const $search = $('#catalogSearchInput');
    const $submitBtn = $('#submitBtn');

    // Filter product rows to those with stock in the selected source warehouse + propagate availability.
    function applyWarehouseFilter() {
        const wh = $whSelect.val() || '';
        let visible = 0;

        $('.product-row').each(function() {
            const warehouses = ($(this).attr('data-warehouses') || '').split(',').filter(Boolean);
            const hasStock = wh ? warehouses.includes(wh) : false;
            this.style.display = hasStock || !wh ? '' : 'none';

            // Show available qty for this warehouse (from data-stock map)
            const id = $(this).find('.product-id-field').data('id');
            const availEl = $('#avail_' + id);
            const stockMap = JSON.parse($(this).attr('data-stock') || '{}');
            const qty = (wh && stockMap[wh]) ? parseInt(stockMap[wh], 10) : 0;

            if (wh && qty > 0) {
                availEl.html('<span class="badge bg-success-subtle text-success">' + qty + ' units</span>');
            } else if (wh) {
                availEl.html('<span class="badge bg-secondary-subtle text-secondary">Unavailable</span>');
            } else {
                availEl.html('<span class="badge bg-secondary-subtle text-secondary">Select source</span>');
            }

            if (hasStock || !wh) visible++;

            // Uncheck + cap qty for rows hidden / out of stock in the selected warehouse
            const $qtyInput = $(this).find('.qty-input');
            if (wh && !hasStock) {
                $(this).find('.product-checkbox').prop('checked', false);
            } else if (wh && qty > 0) {
                $qtyInput.attr('max', qty);
                if (parseInt($qtyInput.val(), 10) > qty) $qtyInput.val(qty);
            }
        });

        $('#selectAllCheckbox').prop('checked', false);
        updateTotals();
    }

    function updateTotals() {
        let count = 0;
        $checkboxes.each(function() {
            const $row = $(this).closest('tr');
            const active = $(this).prop('checked');
            $row.find('.product-id-field').prop('disabled', !active);
            $row.find('.qty-input').prop('disabled', !active);
            if (active) count++;
        });
        $submitBtn.prop('disabled', count === 0);
    }

    $whSelect.on('change', applyWarehouseFilter);
    $checkboxes.on('change', updateTotals);

    $selectAll.on('change', function() {
        const check = $(this).prop('checked');
        $('.product-row').each(function() {
            if (this.style.display !== 'none') {
                $(this).find('.product-checkbox').prop('checked', check);
            }
        });
        updateTotals();
    });

    $search.on('input', function() {
        const term = this.value.toLowerCase().trim();
        $('.product-row').each(function() {
            const name = $(this).attr('data-name') || '';
            const code = $(this).attr('data-code') || '';
            $(this).css('display', name.includes(term) || code.includes(term) ? '' : 'none');
        });
    });

    applyWarehouseFilter();
});
</script>
@endpush
@endsection