@extends('layouts.app')

@section('title', __('Shop Inventory Assignment'))

@push('css')
<style>
    /* Prevent style.css tr:not(:first-child) rule from hiding non-first rows */
    #assignmentTable tr.product-row {
        opacity: 1 !important;
        animation: none !important;
    }
    #assignmentTable tr.product-row.is-visible {
        display: table-row !important;
        opacity: 1 !important;
    }
    #assignmentTable tr.product-row.is-hidden {
        display: none !important;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark">{{ __('New Shop Inventory Assignment') }}</h4>
            <p class="text-muted mb-0 small">{{ __('Transfer inventory stock from a Source Warehouse directly to a Target Shop.') }}</p>
        </div>
        <a href="{{ route('admin.inventory-assignment.index') }}" class="btn btn-outline-secondary btn-sm rounded-2">
            <i class="fas fa-arrow-left me-1"></i> {{ __('Back to Assignments') }}
        </a>
    </div>

    <!-- Error Messages -->
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm mb-4">
            <div class="fw-bold mb-1"><i class="fas fa-exclamation-triangle me-2"></i>{{ __('Please resolve the following errors:') }}</div>
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('admin.inventory-assignment.store') }}" method="POST" id="assignmentForm">
        @csrf

        <!-- Selection Card -->
        <div class="card shadow-sm border-0 rounded-3 mb-4">
            <div class="card-body p-4">
                <div class="row g-4">
                    <!-- Source Warehouse -->
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark mb-1">
                            <i class="fas fa-warehouse text-primary me-2"></i>{{ __('1. Select Source Warehouse') }} <span class="text-danger">*</span>
                        </label>
                        <div class="form-text text-muted mb-2 small">{{ __('Choose the warehouse holding the inventory to transfer.') }}</div>
                        <select name="from_warehouse_id" id="from_warehouse_id" class="form-select form-select-lg border-2 @error('from_warehouse_id') is-invalid @enderror" required>
                            <option value="">{{ __('-- Choose Source Warehouse --') }}</option>
                            @foreach($warehouses as $wh)
                                <option value="{{ $wh->id }}" {{ old('from_warehouse_id') == $wh->id ? 'selected' : '' }}>
                                    🏢 {{ $wh->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('from_warehouse_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <!-- Target Shop -->
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark mb-1">
                            <i class="fas fa-store text-success me-2"></i>{{ __('2. Select Target Shop') }} <span class="text-danger">*</span>
                        </label>
                        <div class="form-text text-muted mb-2 small">{{ __('Choose the destination shop receiving the stock.') }}</div>
                        <select name="shop_id" id="shop_id" class="form-select form-select-lg border-2 @error('shop_id') is-invalid @enderror" required>
                            <option value="">{{ __('-- Choose Target Shop --') }}</option>
                            @foreach($shops as $shop)
                                <option value="{{ $shop->id }}" {{ old('shop_id') == $shop->id ? 'selected' : '' }}>
                                    🛍️ {{ $shop->name }} ({{ $shop->user?->first_name ?? 'Root' }})
                                </option>
                            @endforeach
                        </select>
                        @error('shop_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Initial Placeholder (When No Warehouse Selected) -->
        <div id="noWarehouseSelectedNotice" class="card shadow-sm border-0 rounded-3 text-center py-5 mb-4">
            <div class="card-body">
                <div class="avatar-lg bg-primary-subtle text-primary rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
                    <i class="fas fa-boxes-stacked fa-2x"></i>
                </div>
                <h5 class="fw-bold text-dark">{{ __('Select a Source Warehouse to View Products') }}</h5>
                <p class="text-muted small mb-0">{{ __('Please select a warehouse from dropdown above to list all available products & stock.') }}</p>
            </div>
        </div>

        <!-- Catalog Container (Visible when warehouse is selected) -->
        <div id="catalogContainer" class="d-none">
            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-header bg-white py-3 border-0">
                    <div class="row align-items-center g-3">
                        <div class="col-md-5">
                            <h6 class="fw-bold mb-0 text-dark">
                                <i class="fas fa-cubes me-2 text-primary"></i>{{ __('3. Select Products & Assignment Quantities') }}
                            </h6>
                        </div>
                        <div class="col-md-7">
                            <div class="d-flex align-items-center justify-content-md-end gap-2">
                                <!-- Filter Toggles -->
                                <div class="btn-group btn-group-sm" role="group">
                                    <button type="button" class="btn btn-outline-primary active" id="filterAvailableBtn">
                                        <i class="fas fa-check-circle me-1"></i>{{ __('In Stock Only') }}
                                    </button>
                                    <button type="button" class="btn btn-outline-primary" id="filterAllBtn">
                                        <i class="fas fa-list me-1"></i>{{ __('All Catalog') }}
                                    </button>
                                </div>
                                <span class="badge bg-light text-dark border px-3 py-2 fs-6 rounded-pill" id="productCounterBadge">
                                    <span id="visibleProductCount">0</span> / <span id="totalProductCount">0</span> {{ __('Items') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body p-0">
                    <!-- Search Input -->
                    <div class="p-3 border-top border-bottom bg-light">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                            <input type="text" id="catalogSearchInput" class="form-control border-start-0" autocomplete="off"
                                placeholder="{{ __('Search products by name or SKU code...') }}">
                            <button type="button" class="btn btn-outline-secondary d-none" id="clearSearchBtn">
                                <i class="fas fa-times me-1"></i>{{ __('Clear Search') }}
                            </button>
                        </div>
                    </div>

                    <!-- Products Table -->
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 no-row-animation" id="assignmentTable">
                            <thead class="table-light text-uppercase small text-muted">
                                <tr>
                                    <th style="width: 50px;" class="text-center py-3">
                                        <input type="checkbox" class="form-check-input" id="selectAllCheckbox" title="{{ __('Select All Available') }}">
                                    </th>
                                    <th class="py-3">{{ __('Product Name & SKU') }}</th>
                                    <th class="text-center py-3" style="width: 240px;">{{ __('Source Warehouse Availability') }}</th>
                                    <th class="text-center py-3" style="width: 220px;">{{ __('Quantity To Assign') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($products as $index => $product)
                                    <tr class="product-row is-visible"
                                        data-id="{{ $product->id }}"
                                        data-name="{{ strtolower($product->name) }}"
                                        data-code="{{ strtolower($product->code ?? '') }}"
                                        data-stock='@json($product->stock_map)'>
                                        <td class="text-center">
                                            <input type="checkbox" class="form-check-input product-checkbox" id="check_{{ $product->id }}">
                                        </td>
                                        <td>
                                            <label for="check_{{ $product->id }}" class="fw-bold text-dark mb-0 d-block cursor-pointer">{{ $product->name }}</label>
                                            <div class="small text-muted font-monospace"><i class="fas fa-barcode me-1 text-secondary"></i>SKU: {{ $product->code ?? 'N/A' }}</div>
                                        </td>
                                        <td class="text-center" id="avail_{{ $product->id }}">
                                            <span class="badge bg-secondary-subtle text-secondary px-3 py-2 rounded-pill">{{ __('Select warehouse') }}</span>
                                        </td>
                                        <td class="text-center">
                                            <input type="hidden" name="items[{{ $index }}][product_id]" value="{{ $product->id }}" class="product-id-field" disabled data-id="{{ $product->id }}">
                                            <div class="input-group input-group-sm justify-content-center">
                                                <button type="button" class="btn btn-outline-secondary qty-minus-btn" disabled>-</button>
                                                <input type="number" name="items[{{ $index }}][quantity]" id="qty_input_{{ $product->id }}"
                                                    class="form-control text-center qty-input fw-bold" style="max-width: 90px;" min="1" value="1" disabled>
                                                <button type="button" class="btn btn-outline-secondary qty-plus-btn" disabled>+</button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Notes & Summary -->
            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-body p-4">
                    <label class="form-label fw-bold text-dark"><i class="fas fa-sticky-note text-warning me-2"></i>{{ __('Assignment Notes / Reference') }}</label>
                    <textarea name="notes" class="form-control" rows="2" placeholder="{{ __('Optional notes or reference number for this inventory assignment...') }}">{{ old('notes') }}</textarea>
                </div>
                <div class="card-footer bg-white p-4 border-top d-flex flex-column flex-md-row align-items-center justify-content-between gap-3">
                    <div class="small text-muted">
                        <span class="fw-bold text-dark" id="summaryText">{{ __('No items selected') }}</span>
                    </div>
                    <div class="d-flex gap-2 w-100 w-md-auto justify-content-end">
                        <a href="{{ route('admin.inventory-assignment.index') }}" class="btn btn-light px-4">{{ __('Cancel') }}</a>
                        <button type="submit" class="btn btn-primary btn-lg px-5 shadow-sm" id="submitBtn" disabled>
                            <i class="fas fa-check-circle me-2"></i>{{ __('Confirm & Assign Stock') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
(function() {
    function initInventoryAssignment() {
        const whSelect = document.getElementById('from_warehouse_id');
        const shopSelect = document.getElementById('shop_id');
        const formEl = document.getElementById('assignmentForm');
        const noWhNotice = document.getElementById('noWarehouseSelectedNotice');
        const catalogContainer = document.getElementById('catalogContainer');
        const catalogSearch = document.getElementById('catalogSearchInput');
        const clearSearchBtn = document.getElementById('clearSearchBtn');
        const filterAvailableBtn = document.getElementById('filterAvailableBtn');
        const filterAllBtn = document.getElementById('filterAllBtn');
        const visibleCountEl = document.getElementById('visibleProductCount');
        const totalCountEl = document.getElementById('totalProductCount');
        const selectAllCheck = document.getElementById('selectAllCheckbox');
        const submitBtn = document.getElementById('submitBtn');
        const summaryTextEl = document.getElementById('summaryText');

        if (!whSelect) return;

        let activeFilter = 'available'; // 'available' or 'all'

        if (catalogSearch) catalogSearch.value = '';

        function setRowVisibility(row, visible) {
            if (visible) {
                row.classList.remove('is-hidden');
                row.classList.add('is-visible');
                row.style.setProperty('display', 'table-row', 'important');
                row.style.setProperty('opacity', '1', 'important');
            } else {
                row.classList.remove('is-visible');
                row.classList.add('is-hidden');
                row.style.setProperty('display', 'none', 'important');
            }
        }

        function renderCatalog() {
            const selectedWh = (whSelect.value || '').trim();

            if (!selectedWh) {
                if (noWhNotice) noWhNotice.classList.remove('d-none');
                if (catalogContainer) catalogContainer.classList.add('d-none');
                return;
            }

            if (noWhNotice) noWhNotice.classList.add('d-none');
            if (catalogContainer) catalogContainer.classList.remove('d-none');

            const searchTerm = catalogSearch ? catalogSearch.value.toLowerCase().trim() : '';
            const rows = document.querySelectorAll('#assignmentTable tbody tr.product-row');

            if (clearSearchBtn) {
                if (searchTerm) {
                    clearSearchBtn.classList.remove('d-none');
                } else {
                    clearSearchBtn.classList.add('d-none');
                }
            }

            let visibleCount = 0;

            rows.forEach(function(row) {
                try {
                    const rawStock = row.getAttribute('data-stock') || '{}';
                    let stockMap = {};
                    if (typeof rawStock === 'string') {
                        stockMap = JSON.parse(rawStock.replace(/&quot;/g, '"'));
                    } else {
                        stockMap = rawStock;
                    }

                    const name = (row.getAttribute('data-name') || '').toLowerCase();
                    const code = (row.getAttribute('data-code') || '').toLowerCase();
                    const productId = row.getAttribute('data-id');
                    const availEl = document.getElementById('avail_' + productId);
                    const qtyInput = document.getElementById('qty_input_' + productId);
                    const qtyMinus = row.querySelector('.qty-minus-btn');
                    const qtyPlus = row.querySelector('.qty-plus-btn');
                    const checkbox = row.querySelector('.product-checkbox');
                    const hiddenInput = row.querySelector('.product-id-field');

                    const qty = parseInt(stockMap[selectedWh] ?? stockMap[parseInt(selectedWh, 10)] ?? 0, 10);
                    const matchesSearch = !searchTerm || name.indexOf(searchTerm) !== -1 || code.indexOf(searchTerm) !== -1;
                    const matchesFilter = activeFilter === 'all' || qty > 0;

                    if (!matchesSearch || !matchesFilter) {
                        setRowVisibility(row, false);
                        return;
                    }

                    setRowVisibility(row, true);
                    visibleCount++;

                    if (qty > 0) {
                        if (availEl) {
                            availEl.innerHTML = '<span class="badge bg-success-subtle text-success fs-6 px-3 py-2 rounded-pill"><i class="fas fa-check-circle me-1"></i>' + qty + ' units available</span>';
                        }
                        if (checkbox) checkbox.disabled = false;
                    } else {
                        if (availEl) {
                            availEl.innerHTML = '<span class="badge bg-secondary-subtle text-secondary px-3 py-2 rounded-pill"><i class="fas fa-times-circle me-1"></i>Out of Stock (0)</span>';
                        }
                        if (checkbox) {
                            checkbox.checked = false;
                            checkbox.disabled = true;
                        }
                        if (qtyInput) qtyInput.disabled = true;
                        if (qtyMinus) qtyMinus.disabled = true;
                        if (qtyPlus) qtyPlus.disabled = true;
                        if (hiddenInput) hiddenInput.disabled = true;
                    }
                } catch (err) {
                    console.error('Error rendering row:', err, row);
                    setRowVisibility(row, true);
                }
            });

            if (visibleCountEl) visibleCountEl.textContent = visibleCount;
            if (totalCountEl) totalCountEl.textContent = rows.length;

            if (selectAllCheck) selectAllCheck.checked = false;
            updateSelectionState();
        }

        function updateSelectionState() {
            let selectedCount = 0;
            let totalAssignedQty = 0;

            const rows = document.querySelectorAll('#assignmentTable tbody tr.product-row');
            rows.forEach(function(row) {
                const checkbox = row.querySelector('.product-checkbox');
                const hiddenInput = row.querySelector('.product-id-field');
                const qtyInput = row.querySelector('.qty-input');
                const qtyMinus = row.querySelector('.qty-minus-btn');
                const qtyPlus = row.querySelector('.qty-plus-btn');

                const isVisible = !row.classList.contains('is-hidden') && row.style.display !== 'none';
                const isChecked = isVisible && checkbox && checkbox.checked && !checkbox.disabled;

                if (hiddenInput) hiddenInput.disabled = !isChecked;
                if (qtyInput) qtyInput.disabled = !isChecked;
                if (qtyMinus) qtyMinus.disabled = !isChecked;
                if (qtyPlus) qtyPlus.disabled = !isChecked;

                if (isChecked) {
                    selectedCount++;
                    const currentQty = parseInt(qtyInput ? qtyInput.value : '1', 10) || 1;
                    totalAssignedQty += currentQty;
                    row.classList.add('table-primary');
                } else {
                    row.classList.remove('table-primary');
                }
            });

            const hasTargetShop = !!(shopSelect && shopSelect.value);
            const isValid = selectedCount > 0 && hasTargetShop;

            if (submitBtn) submitBtn.disabled = !isValid;

            if (summaryTextEl) {
                if (selectedCount > 0) {
                    summaryTextEl.innerHTML = '<i class="fas fa-boxes me-1 text-primary"></i><strong>' + selectedCount + ' Product(s)</strong> selected for assignment (Total: <strong>' + totalAssignedQty + ' units</strong>).';
                } else {
                    summaryTextEl.innerHTML = '<span class="text-muted"><i class="fas fa-info-circle me-1"></i>Select at least one product above to enable assignment.</span>';
                }
            }
        }

        // Before form submission, ensure ONLY checked, visible rows submit form inputs
        if (formEl) {
            formEl.addEventListener('submit', function() {
                const rows = document.querySelectorAll('#assignmentTable tbody tr.product-row');
                rows.forEach(function(row) {
                    const checkbox = row.querySelector('.product-checkbox');
                    const isVisible = !row.classList.contains('is-hidden') && row.style.display !== 'none';
                    const isChecked = isVisible && checkbox && checkbox.checked && !checkbox.disabled;
                    if (!isChecked) {
                        row.querySelectorAll('.product-id-field, .qty-input').forEach(function(input) {
                            input.disabled = true;
                        });
                    }
                });
            });
        }

        const onWhChange = function() {
            if (catalogSearch) catalogSearch.value = '';
            renderCatalog();
        };

        whSelect.addEventListener('change', onWhChange);
        whSelect.addEventListener('input', onWhChange);

        if (shopSelect) {
            shopSelect.addEventListener('change', updateSelectionState);
        }

        if (window.jQuery) {
            window.jQuery(whSelect).on('change change.select2 select2:select select2:clear', onWhChange);
            if (shopSelect) {
                window.jQuery(shopSelect).on('change change.select2 select2:select select2:clear', updateSelectionState);
            }
        }

        if (catalogSearch) catalogSearch.addEventListener('input', renderCatalog);

        if (clearSearchBtn) {
            clearSearchBtn.addEventListener('click', function() {
                if (catalogSearch) catalogSearch.value = '';
                renderCatalog();
            });
        }

        if (filterAvailableBtn && filterAllBtn) {
            filterAvailableBtn.addEventListener('click', function() {
                activeFilter = 'available';
                filterAvailableBtn.classList.add('active');
                filterAllBtn.classList.remove('active');
                renderCatalog();
            });
            filterAllBtn.addEventListener('click', function() {
                activeFilter = 'all';
                filterAllBtn.classList.add('active');
                filterAvailableBtn.classList.remove('active');
                renderCatalog();
            });
        }

        if (selectAllCheck) {
            selectAllCheck.addEventListener('change', function() {
                const isChecked = this.checked;
                const rows = document.querySelectorAll('#assignmentTable tbody tr.product-row');
                rows.forEach(function(row) {
                    if (!row.classList.contains('is-hidden')) {
                        const checkbox = row.querySelector('.product-checkbox');
                        if (checkbox && !checkbox.disabled) checkbox.checked = isChecked;
                    }
                });
                updateSelectionState();
            });
        }

        // Delegate checkbox and quantity plus/minus buttons
        document.querySelectorAll('#assignmentTable tbody tr.product-row').forEach(function(row) {
            const checkbox = row.querySelector('.product-checkbox');
            const qtyInput = row.querySelector('.qty-input');
            const qtyMinus = row.querySelector('.qty-minus-btn');
            const qtyPlus = row.querySelector('.qty-plus-btn');

            if (checkbox) {
                checkbox.addEventListener('change', updateSelectionState);
            }
            if (qtyInput) {
                qtyInput.addEventListener('input', updateSelectionState);
            }
            if (qtyMinus && qtyInput) {
                qtyMinus.addEventListener('click', function() {
                    let val = parseInt(qtyInput.value || '1', 10);
                    if (val > 1) {
                        qtyInput.value = val - 1;
                        updateSelectionState();
                    }
                });
            }
            if (qtyPlus && qtyInput) {
                qtyPlus.addEventListener('click', function() {
                    let val = parseInt(qtyInput.value || '1', 10);
                    let max = parseInt(qtyInput.max || '99999', 10);
                    if (val < max) {
                        qtyInput.value = val + 1;
                        updateSelectionState();
                    }
                });
            }
        });

        renderCatalog();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initInventoryAssignment);
    } else {
        initInventoryAssignment();
    }
})();
</script>
@endpush
@endsection