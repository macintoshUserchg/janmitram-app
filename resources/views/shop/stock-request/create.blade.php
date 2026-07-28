@extends('layouts.app')

@section('title', __('New Stock Request'))

@section('content')
<!-- Integrated Header & Linked Logistics Hub Banner Card -->
<div class="card border-0 shadow-sm rounded-12 mb-4 bg-gradient bg-primary text-white">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-3">
            <div>
                <a href="{{ route('shop.stock-request.index') }}" class="btn btn-sm btn-light text-primary fw-bold mb-2">
                    <i class="fas fa-arrow-left me-1"></i> {{ __('Back to Stock Requests') }}
                </a>
                <h1 class="h3 mb-1 text-white fw-bold">{{ __('Request Warehouse Stock') }}</h1>
                <p class="text-white-50 small mb-0">{{ __('Select master catalog products to request physical stock dispatches from your assigned Central Warehouse.') }}</p>
            </div>
        </div>

        @if($warehouse)
            <div class="pt-3 border-top border-white-20 d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-light text-primary fw-bold">{{ __('Linked Logistics Fulfillment Hub') }}</span>
                    <span class="fw-bold text-white fs-5"><i class="fas fa-warehouse me-1 text-warning"></i> {{ $warehouse->name }}</span>
                    @if($warehouse->address)
                        <span class="text-white-50 small">({{ $warehouse->address }})</span>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>

<div class="card border-0 shadow-sm rounded-12 bg-white col-lg-10 mx-auto">
    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0 fw-bold"><i class="fas fa-cart-flatbed me-2 text-primary"></i>{{ __('Request Inventory Items') }}</h5>
        <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill"><i class="fas fa-link me-1"></i>{{ __('Linked Hub Locked') }}</span>
    </div>
    <div class="card-body p-4">
        <form action="{{ route('shop.stock-request.store') }}" method="POST">
            @csrf

            <p class="text-muted small mb-4">
                {{ __('Select products from the central warehouse catalog and enter the requested quantity to add to your shop sellable inventory upon admin approval.') }}
            </p>

            <div id="itemsContainer">
                <div class="card bg-light border-0 mb-3 item-row p-3 rounded-12">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-8">
                            <label class="form-label fw-bold small required">{{ __('Select Product') }}</label>
                            <select name="items[0][product_id]" class="form-select" required>
                                <option value="">{{ __('-- Select Master Product --') }}</option>
                                @foreach($masterProducts as $mp)
                                    <option value="{{ $mp->id }}" {{ request('product_id') == $mp->id ? 'selected' : '' }}>
                                        {{ $mp->name }} — {{ __('Available in') }} {{ $warehouse->name }}: {{ $mp->warehouse_qty }} {{ __('units') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold small required">{{ __('Quantity') }}</label>
                            <input type="number" name="items[0][quantity]" class="form-control" min="1" value="1" required>
                        </div>
                        <div class="col-md-1 text-center">
                            <button type="button" class="btn btn-outline-danger btn-sm remove-item-btn d-none">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-bold" id="addItemBtn">
                    <i class="fas fa-plus me-1"></i> {{ __('Add Another Product') }}
                </button>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold small">{{ __('Notes / Special Instructions') }}</label>
                <textarea name="notes" class="form-control" rows="3" placeholder="{{ __('e.g. Urgent stock needed for weekend promotion') }}"></textarea>
            </div>

            <div class="d-flex justify-content-end gap-2 border-top pt-3">
                <a href="{{ route('shop.stock-request.index') }}" class="btn btn-light rounded-pill px-4">{{ __('Cancel') }}</a>
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">{{ __('Submit Stock Request') }}</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    let itemIndex = 1;
    const masterOptions = `@foreach($masterProducts as $mp)<option value="{{ $mp->id }}">{{ addslashes($mp->name) }} — {{ __('Available in') }} {{ addslashes($warehouse->name) }}: {{ $mp->warehouse_qty }} {{ __('units') }}</option>@endforeach`;

    document.getElementById('addItemBtn').addEventListener('click', function() {
        const container = document.getElementById('itemsContainer');
        const row = document.createElement('div');
        row.className = 'card bg-light border-0 mb-3 item-row p-3 rounded-12';
        row.innerHTML = `
            <div class="row g-3 align-items-end">
                <div class="col-md-8">
                    <label class="form-label fw-bold small required">{{ __('Select Product') }}</label>
                    <select name="items[${itemIndex}][product_id]" class="form-select" required>
                        <option value="">{{ __('-- Select Master Product --') }}</option>
                        ${masterOptions}
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold small required">{{ __('Quantity') }}</label>
                    <input type="number" name="items[${itemIndex}][quantity]" class="form-control" min="1" value="1" required>
                </div>
                <div class="col-md-1 text-center">
                    <button type="button" class="btn btn-outline-danger btn-sm remove-item-btn">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `;
        container.appendChild(row);
        itemIndex++;
        updateRemoveButtons();
    });

    document.getElementById('itemsContainer').addEventListener('click', function(e) {
        if (e.target.closest('.remove-item-btn')) {
            const row = e.target.closest('.item-row');
            if (document.querySelectorAll('.item-row').length > 1) {
                row.remove();
                updateRemoveButtons();
            }
        }
    });

    function updateRemoveButtons() {
        const rows = document.querySelectorAll('.item-row');
        rows.forEach((row) => {
            const btn = row.querySelector('.remove-item-btn');
            if (rows.length > 1) {
                btn.classList.remove('d-none');
            } else {
                btn.classList.add('d-none');
            }
        });
    }
</script>
@endpush
@endsection
