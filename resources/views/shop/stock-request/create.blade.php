@extends('layouts.app')

@section('title', __('New Stock Request'))

@section('content')
<!-- Integrated 2-Line Compact Header Banner Card -->
<div class="card border-0 shadow-sm rounded-12 mb-4 bg-gradient bg-primary text-white">
    <div class="card-body p-3">
        <!-- Line 1: Title + Back Button -->
        <div class="d-flex justify-content-between align-items-center mb-1">
            <h1 class="h4 mb-0 text-white fw-bold"><i class="fas fa-cart-flatbed me-2 text-warning"></i>{{ __('Request Warehouse Stock') }}</h1>
            <a href="{{ route('shop.stock-request.index') }}" class="btn btn-sm btn-light text-primary fw-bold">
                <i class="fas fa-arrow-left me-1"></i> {{ __('Back to Stock Requests') }}
            </a>
        </div>
        <!-- Line 2: Subtitle & Linked Hub Badge inline -->
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 text-white-50 small">
            <span>{{ __('Select master products from your assigned Central Warehouse catalog and specify requested quantities.') }}</span>
            @if($warehouse)
                <span class="badge bg-light text-primary fw-bold">
                    <i class="fas fa-warehouse me-1 text-warning"></i>{{ __('Linked Hub:') }} {{ $warehouse->name }} @if($warehouse->address)({{ $warehouse->address }})@endif
                </span>
            @endif
        </div>
    </div>
</div>

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-4">{{ session('error') }}</div>
@endif

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

@if($isFirstTransfer)
    <div class="alert alert-warning border-0 shadow-sm rounded-12 mb-4 d-flex align-items-center gap-3">
        <div class="p-2 bg-warning-subtle rounded-circle text-warning fs-4">
            <i class="fas fa-star"></i>
        </div>
        <div>
            <div class="fw-bold text-dark">{{ __('⭐ Welcome to Janmitram! Initial Stocking Request') }}</div>
            <div class="small text-muted">
                {{ __('As per franchise policy, your first stock request must be valued at') }}
                <strong class="text-dark">₹3,000.00</strong> {{ __('or above. Subsequent stock requests can be of any quantity.') }}
            </div>
        </div>
    </div>
@endif

<form action="{{ route('shop.stock-request.store') }}" method="POST" id="stockRequestForm" data-is-first-transfer="{{ $isFirstTransfer ? '1' : '0' }}">
    @csrf

    <!-- Product Search Bar -->
    <div class="card border-0 shadow-sm rounded-12 mb-4 bg-white">
        <div class="card-body p-3">
            <div class="row g-2 align-items-center">
                <div class="col-md-8">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" id="catalogSearchInput" class="form-control border-start-0" placeholder="{{ __('Search catalog by product name, brand, or SKU code...') }}">
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    <span class="badge bg-primary-subtle text-primary fs-6 px-3 py-2 rounded-pill" id="selectedItemsBadge">
                        <i class="fas fa-shopping-basket me-1"></i> <span id="selectedCount">0</span> {{ __('Items Selected') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Master Catalog Items Table -->
    <div class="card border-0 shadow-sm rounded-12 bg-white mb-4">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0 fw-bold"><i class="fas fa-boxes-stacked me-2 text-primary"></i>{{ __('Central Warehouse Product Catalog') }}</h5>
            <span class="badge bg-secondary-subtle text-secondary px-3 py-2 rounded-pill">{{ $masterProducts->count() }} {{ __('Products Available') }}</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="catalogTable">
                    <thead class="table-light text-uppercase small text-muted">
                        <tr>
                            <th style="width: 50px;" class="text-center">
                                <input type="checkbox" class="form-check-input" id="selectAllCheckbox" title="{{ __('Select All Products') }}">
                            </th>
                            <th>{{ __('Product & SKU') }}</th>
                            <th>{{ __('Brand / Category') }}</th>
                            <th class="text-center">{{ __('Unit Price') }}</th>
                            <th class="text-center">{{ __('Central Stock') }}</th>
                            <th class="text-center" style="width: 180px;">{{ __('Requested Quantity') }}</th>
                            <th class="text-end pe-3" style="width: 140px;">{{ __('Subtotal') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($masterProducts as $index => $mp)
                            @php
                                $unitPrice = (float) ($mp->discount_price > 0 ? $mp->discount_price : $mp->price);
                            @endphp
                            <tr class="product-row"
                                data-name="{{ strtolower($mp->name) }}"
                                data-code="{{ strtolower($mp->code ?? '') }}"
                                data-brand="{{ strtolower($mp->brand?->name ?? '') }}"
                                data-price="{{ $unitPrice }}">
                                <td class="text-center">
                                    <input type="checkbox" class="form-check-input product-checkbox" id="check_{{ $mp->id }}" data-target="qty_input_{{ $mp->id }}">
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="{{ $mp->thumbnail }}" width="50" height="50" class="rounded border object-fit-cover">
                                        <div>
                                            <label for="check_{{ $mp->id }}" class="fw-bold text-dark mb-0 cursor-pointer text-decoration-underline-hover">{{ $mp->name }}</label>
                                            <div class="small text-muted font-monospace">
                                                <span>{{ __('SKU:') }} {{ $mp->code ?? 'N/A' }}</span>
                                                <span class="ms-2 badge bg-light text-dark border">{{ __('Master #') }}{{ $mp->id }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <span class="fw-semibold text-dark">{{ $mp->brand?->name ?? '—' }}</span>
                                        @if($mp->categories->isNotEmpty())
                                            <div class="small text-muted">{{ $mp->categories->pluck('name')->implode(', ') }}</div>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-center fw-bold">{{ showCurrency($unitPrice) }}</td>
                                <td class="text-center">
                                    @if($mp->warehouse_qty > 10)
                                        <span class="badge bg-success-subtle text-success fs-6 px-3 py-2">
                                            <i class="fas fa-check-circle me-1"></i> {{ $mp->warehouse_qty }} {{ __('units') }}
                                        </span>
                                    @elseif($mp->warehouse_qty > 0)
                                        <span class="badge bg-warning-subtle text-dark fs-6 px-3 py-2">
                                            <i class="fas fa-exclamation-triangle me-1"></i> {{ $mp->warehouse_qty }} {{ __('units left') }}
                                        </span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger fs-6 px-3 py-2">
                                            <i class="fas fa-times-circle me-1"></i> {{ __('Out of Stock') }}
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <input type="hidden" name="items[{{ $index }}][product_id]" value="{{ $mp->id }}" class="product-id-field" disabled>
                                    <div class="input-group input-group-sm quantity-group">
                                        <button type="button" class="btn btn-outline-secondary btn-minus" disabled>-</button>
                                        <input type="number" name="items[{{ $index }}][quantity]" id="qty_input_{{ $mp->id }}" class="form-control text-center qty-input fw-bold" min="1" max="{{ max(1, $mp->warehouse_qty) }}" value="1" disabled>
                                        <button type="button" class="btn btn-outline-secondary btn-plus" disabled>+</button>
                                    </div>
                                </td>
                                <td class="text-end pe-3">
                                    <span class="fw-bold text-dark item-subtotal">{{ showCurrency(0) }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <div class="mb-2"><i class="fas fa-boxes fs-1 text-muted"></i></div>
                                    <h5>{{ __('No Master Products Available in Central Warehouse') }}</h5>
                                    <p class="small mb-0">{{ __('Stock must be added to the central warehouse before shops can request dispatches.') }}</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Notes & Submission Footer Card -->
    <div class="card border-0 shadow-sm rounded-12 bg-white mb-4">
        <div class="card-body p-4">
            <div class="mb-3">
                <label class="form-label fw-bold small text-dark"><i class="fas fa-comment-dots me-1 text-primary"></i> {{ __('Request Notes / Special Instructions') }}</label>
                <textarea name="notes" class="form-control" rows="3" placeholder="{{ __('e.g. Urgent stock needed for weekend sale. Please dispatch as early as possible.') }}"></textarea>
            </div>

            <div class="d-flex justify-content-between align-items-center pt-3 border-top flex-wrap gap-2">
                <div id="requestSummary">
                    <span class="text-muted small"><i class="fas fa-info-circle me-1"></i>Select products above to build your stock request.</span>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('shop.stock-request.index') }}" class="btn btn-light rounded-pill px-4">{{ __('Cancel') }}</a>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 py-2 fw-bold fs-6 shadow-sm" id="submitBtn" disabled>
                        <i class="fas fa-paper-plane me-1"></i> {{ __('Submit Stock Request') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const formEl = document.getElementById('stockRequestForm');
        const checkboxes = document.querySelectorAll('.product-checkbox');
        const selectAll = document.getElementById('selectAllCheckbox');
        const searchInput = document.getElementById('catalogSearchInput');
        const submitBtn = document.getElementById('submitBtn');
        const selectedCount = document.getElementById('selectedCount');
        const requestSummary = document.getElementById('requestSummary');
        const isFirstTransfer = formEl && formEl.getAttribute('data-is-first-transfer') === '1';

        function updateTotals() {
            let count = 0;
            let totalQty = 0;
            let totalValue = 0.0;

            checkboxes.forEach(cb => {
                const row = cb.closest('tr');
                const productIdField = row.querySelector('.product-id-field');
                const qtyInput = row.querySelector('.qty-input');
                const btnMinus = row.querySelector('.btn-minus');
                const btnPlus = row.querySelector('.btn-plus');
                const subtotalEl = row.querySelector('.item-subtotal');
                const price = parseFloat(row.getAttribute('data-price') || '0');

                if (cb.checked) {
                    count++;
                    productIdField.disabled = false;
                    qtyInput.disabled = false;
                    btnMinus.disabled = false;
                    btnPlus.disabled = false;
                    row.classList.add('table-primary-subtle');

                    const qty = parseInt(qtyInput.value) || 1;
                    const itemTotal = qty * price;
                    totalQty += qty;
                    totalValue += itemTotal;
                    if (subtotalEl) subtotalEl.textContent = '₹' + itemTotal.toFixed(2);
                } else {
                    productIdField.disabled = true;
                    qtyInput.disabled = true;
                    btnMinus.disabled = true;
                    btnPlus.disabled = true;
                    row.classList.remove('table-primary-subtle');
                    if (subtotalEl) subtotalEl.textContent = '₹0.00';
                }
            });

            selectedCount.textContent = count;

            const meetsLimit = !isFirstTransfer || (totalValue >= 3000.0);
            submitBtn.disabled = (count === 0) || !meetsLimit;

            if (requestSummary) {
                if (count > 0) {
                    let html = '<div class="d-flex flex-column gap-1">' +
                        '<div><strong>' + count + ' Product(s)</strong> selected (Total: <strong>' + totalQty + ' units</strong> | Value: <strong class="text-success fs-6">₹' + totalValue.toFixed(2) + '</strong>)</div>';
                    if (isFirstTransfer) {
                        if (totalValue < 3000.0) {
                            const shortfall = (3000.0 - totalValue).toFixed(2);
                            html += '<div class="badge bg-warning text-dark px-3 py-1.5 rounded-pill text-start"><i class="fas fa-exclamation-triangle me-1"></i>First request minimum ₹3,000 required. Need ₹' + shortfall + ' more.</div>';
                        } else {
                            html += '<div class="badge bg-success text-white px-3 py-1.5 rounded-pill text-start"><i class="fas fa-check-circle me-1"></i>First request minimum threshold (₹3,000) fulfilled.</div>';
                        }
                    }
                    html += '</div>';
                    requestSummary.innerHTML = html;
                } else {
                    requestSummary.innerHTML = '<span class="text-muted small"><i class="fas fa-info-circle me-1"></i>Select products above to build your stock request.</span>';
                }
            }
        }

        checkboxes.forEach(cb => {
            cb.addEventListener('change', updateTotals);
        });

        if (selectAll) {
            selectAll.addEventListener('change', function () {
                const visibleCheckboxes = document.querySelectorAll('#catalogTable tbody tr:not([style*="display: none"]) .product-checkbox');
                visibleCheckboxes.forEach(cb => {
                    cb.checked = selectAll.checked;
                });
                updateTotals();
            });
        }

        // Stepper Quantity buttons (+ / -)
        document.querySelectorAll('.btn-minus').forEach(btn => {
            btn.addEventListener('click', function () {
                const input = this.closest('.quantity-group').querySelector('.qty-input');
                let val = parseInt(input.value) || 1;
                if (val > 1) {
                    input.value = val - 1;
                    updateTotals();
                }
            });
        });

        document.querySelectorAll('.btn-plus').forEach(btn => {
            btn.addEventListener('click', function () {
                const input = this.closest('.quantity-group').querySelector('.qty-input');
                let val = parseInt(input.value) || 1;
                let max = parseInt(input.getAttribute('max')) || 999999;
                if (val < max) {
                    input.value = val + 1;
                    updateTotals();
                }
            });
        });

        document.querySelectorAll('.qty-input').forEach(input => {
            input.addEventListener('input', function () {
                updateTotals();
            });
        });

        // Dynamic Client-side Search Filter
        if (searchInput) {
            searchInput.addEventListener('input', function () {
                const term = this.value.toLowerCase().trim();
                document.querySelectorAll('#catalogTable tbody tr.product-row').forEach(row => {
                    const name = row.getAttribute('data-name') || '';
                    const code = row.getAttribute('data-code') || '';
                    const brand = row.getAttribute('data-brand') || '';
                    if (name.includes(term) || code.includes(term) || brand.includes(term)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        }

        updateTotals();
    });
</script>
@endpush
@endsection
