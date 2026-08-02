<div class="modal-header bg-light py-3">
    <div class="d-flex align-items-center justify-content-between w-100 me-3">
        <div>
            <h5 class="modal-title fw-bold text-dark mb-0">
                <i class="fas fa-receipt text-primary me-2"></i>{{ __('Order Details') }} #{{ $order->prefix . $order->order_code }}
            </h5>
            <small class="text-muted"><i class="far fa-clock me-1"></i>{{ $order->created_at->format('M d, Y h:i A') }}</small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('shop.download-invoice', $order->id) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-file-pdf me-1"></i>{{ __('Invoice PDF') }}
            </a>
            <a href="{{ route('shop.order.show', $order->id) }}" class="btn btn-sm btn-primary">
                <i class="fas fa-external-link-alt me-1"></i>{{ __('Full View') }}
            </a>
        </div>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body p-4">
    <!-- Status Badges Bar -->
    <div class="row g-3 mb-4 bg-light p-3 rounded-3 border">
        <div class="col-md-4">
            <div class="small text-muted mb-1">{{ __('Order Status') }}</div>
            <span class="badge bg-primary px-3 py-2 fs-6 fw-semibold">{{ __($order->order_status->value) }}</span>
        </div>
        <div class="col-md-4">
            <div class="small text-muted mb-1">{{ __('Payment Status') }}</div>
            <span class="badge {{ $order->payment_status->value == 'Paid' ? 'bg-success' : 'bg-warning text-dark' }} px-3 py-2 fs-6 fw-semibold">
                {{ __($order->payment_status->value) }}
            </span>
        </div>
        <div class="col-md-4">
            <div class="small text-muted mb-1">{{ __('Payment Method') }}</div>
            <span class="badge bg-secondary px-3 py-2 fs-6 fw-semibold">{{ __($order->payment_method->value) }}</span>
        </div>
    </div>

    <!-- Products Table -->
    <div class="table-responsive mb-4">
        <table class="table table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width: 50px;">{{ __('SL') }}</th>
                    <th>{{ __('Product') }}</th>
                    <th class="text-center" style="width: 90px;">{{ __('Qty') }}</th>
                    <th class="text-end" style="width: 120px;">{{ __('Unit Price') }}</th>
                    <th class="text-end" style="width: 120px;">{{ __('Total') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->products as $key => $product)
                    @php
                        $price = $product->pivot->price > 0 ? $product->pivot->price : ($product->discount_price > 0 ? $product->discount_price : $product->price);
                    @endphp
                    <tr>
                        <td class="text-center">{{ $key + 1 }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <img src="{{ $product->thumbnail }}" alt="" class="rounded border" width="40" height="40" style="object-fit: cover;">
                                <div>
                                    <div class="fw-semibold text-dark">{{ $product->name }}</div>
                                    @if (!empty($product->pivot->sku))
                                        <small class="text-muted">#{{ __('SKU') }}: {{ $product->pivot->sku }}</small>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="text-center fw-bold">{{ $product->pivot->quantity }}</td>
                        <td class="text-end">{{ showCurrency($price) }}</td>
                        <td class="text-end fw-bold">{{ showCurrency($product->pivot->quantity * $price) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Summary & Financials Row -->
    <div class="row g-4 mb-3">
        <div class="col-md-6">
            <div class="card h-100 border-light shadow-sm">
                <div class="card-header bg-white fw-bold py-2 border-bottom">
                    <i class="fas fa-user-circle me-1 text-primary"></i>{{ __('Customer Information') }}
                </div>
                <div class="card-body p-3">
                    <div class="mb-2">
                        <span class="text-muted">{{ __('Name') }}:</span>
                        <span class="fw-semibold ms-1">
                            {{ $order->customer?->user?->name ?? ($order->pos_order ? __('Walk-in Customer / POS') : __('N/A')) }}
                        </span>
                    </div>
                    <div>
                        <span class="text-muted">{{ __('Phone') }}:</span>
                        <span class="fw-semibold ms-1">{{ $order->customer?->user?->phone ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card h-100 border-light shadow-sm">
                <div class="card-header bg-white fw-bold py-2 border-bottom">
                    <i class="fas fa-calculator me-1 text-success"></i>{{ __('Order Summary') }}
                </div>
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">{{ __('Sub Total') }}</span>
                        <span>{{ showCurrency($order->total_amount) }}</span>
                    </div>
                    @if($order->coupon_discount > 0)
                        <div class="d-flex justify-content-between mb-1 text-danger">
                            <span>{{ __('Coupon Discount') }}</span>
                            <span>-{{ showCurrency($order->coupon_discount) }}</span>
                        </div>
                    @endif
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">{{ __('Delivery Charge') }}</span>
                        <span>{{ showCurrency($order->delivery_charge) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">{{ __('VAT & Tax') }}</span>
                        <span>{{ showCurrency($order->tax_amount) }}</span>
                    </div>
                    <div class="d-flex justify-content-between border-top pt-2 fw-bold fs-5 text-dark">
                        <span>{{ __('Grand Total') }}</span>
                        <span class="text-primary">{{ showCurrency($order->payable_amount) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Shipping Info Note -->
    <div class="card border-light shadow-sm">
        <div class="card-header bg-white fw-bold py-2 border-bottom">
            <i class="fas fa-map-marker-alt me-1 text-warning"></i>{{ __('Shipping Address') }}
        </div>
        <div class="card-body p-3">
            @if ($order->pos_order || !$order->address)
                <div class="text-muted fst-italic">
                    <i class="fas fa-store me-1"></i>{{ __('POS Counter Sale (In-Store Purchase - No Shipping Required)') }}
                </div>
            @else
                <div><strong>{{ $order->address->name }}</strong> ({{ $order->address->phone }})</div>
                <div class="text-muted">{{ $order->address->address_line }}, {{ $order->address->area }}</div>
            @endif
        </div>
    </div>
</div>

<div class="modal-footer bg-light py-2">
    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">{{ __('Close') }}</button>
</div>
