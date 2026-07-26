@extends('shop.layouts.app')

@section('title', __('New Stock Request'))

@section('content')
<div class="mb-4">
    <a href="{{ route('shop.stock-request.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="fas fa-arrow-left me-1"></i> {{ __('Back to Requests') }}
    </a>
</div>

<div class="card shadow-sm col-md-9 mx-auto">
    <div class="card-header bg-white">
        <h5 class="card-title mb-0">{{ __('Request Stock from Linked Warehouse') }} ({{ $warehouse->name }})</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('shop.stock-request.store') }}" method="POST">
            @csrf

            <p class="text-muted small mb-4">
                {{ __('Select available products from the master central warehouse and enter the quantity you wish to request for your shop inventory.') }}
            </p>

            <div class="mb-3">
                <label class="form-label required">{{ __('Product from Warehouse') }}</label>
                <select name="items[0][product_id]" class="form-select" required>
                    <option value="">{{ __('-- Select Master / Warehouse Product --') }}</option>
                    @if($masterProducts->count() > 0)
                        @foreach($masterProducts as $mp)
                            <option value="{{ $mp->id }}">
                                {{ $mp->name }} (ID: #{{ $mp->id }})
                            </option>
                        @endforeach
                    @else
                        @foreach($warehouseStocks as $st)
                            <option value="{{ $st->product_id }}">
                                {{ $st->product?->name }} 
                                @if($st->color || $st->size) ({{ $st->color?->name }} {{ $st->size?->name }}) @endif
                                — Available: {{ $st->quantity }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label required">{{ __('Requested Quantity') }}</label>
                <input type="number" name="items[0][quantity]" class="form-control" min="1" value="1" required>
            </div>

            <div class="mb-3">
                <label class="form-label">{{ __('Notes / Special Instructions') }}</label>
                <textarea name="notes" class="form-control" rows="3" placeholder="{{ __('e.g. Urgent stock needed for sale promotion') }}"></textarea>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('shop.stock-request.index') }}" class="btn btn-light">{{ __('Cancel') }}</a>
                <button type="submit" class="btn btn-primary">{{ __('Submit Stock Request') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection
