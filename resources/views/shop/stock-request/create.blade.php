@extends('shop.layouts.app')

@section('title', __('New Stock Request'))

@section('content')
<div class="mb-4">
    <a href="{{ route('shop.stock-request.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="fas fa-arrow-left me-1"></i> {{ __('Back to Requests') }}
    </a>
</div>

<div class="card shadow-sm col-lg-10 mx-auto">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">{{ __('Request Stock from Linked Warehouse') }} ({{ $warehouse->name }})</h5>
        <span class="badge bg-success">{{ __('Central Hub Linked') }}</span>
    </div>
    <div class="card-body">
        <form action="{{ route('shop.stock-request.store') }}" method="POST">
            @csrf

            <p class="text-muted small mb-4">
                {{ __('Select products from the central warehouse catalog and enter the requested quantity to add to your shop sellable inventory upon admin approval.') }}
            </p>

            <div id="itemsContainer">
                <div class="card bg-light border-0 mb-3 item-row p-3">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-8">
                            <label class="form-label fw-bold small required">{{ __('Select Product') }}</label>
                            <select name="items[0][product_id]" class="form-select" required>
                                <option value="">{{ __('-- Select Master Product --') }}</option>
                                @foreach($masterProducts as $mp)
                                    <option value="{{ $mp->id }}" {{ request('product_id') == $mp->id ? 'selected' : '' }}>
                                        {{ $mp->name }} (ID: #{{ $mp->id }})
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
                <button type="button" class="btn btn-outline-primary btn-sm" id="addItemBtn">
                    <i class="fas fa-plus me-1"></i> {{ __('Add Another Product') }}
                </button>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold small">{{ __('Notes / Special Instructions') }}</label>
                <textarea name="notes" class="form-control" rows="3" placeholder="{{ __('e.g. Urgent stock needed for weekend promotion') }}"></textarea>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('shop.stock-request.index') }}" class="btn btn-light">{{ __('Cancel') }}</a>
                <button type="submit" class="btn btn-primary px-4">{{ __('Submit Stock Request') }}</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    let itemIndex = 1;
    const masterOptions = `@foreach($masterProducts as $mp)<option value="{{ $mp->id }}">{{ addslashes($mp->name) }} (ID: #{{ $mp->id }})</option>@endforeach`;

    document.getElementById('addItemBtn').addEventListener('click', function() {
        const container = document.getElementById('itemsContainer');
        const row = document.createElement('div');
        row.className = 'card bg-light border-0 mb-3 item-row p-3';
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
