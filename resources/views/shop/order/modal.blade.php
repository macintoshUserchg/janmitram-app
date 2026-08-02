<div class="modal-header bg-gradient bg-primary text-white py-3 px-4 rounded-top">
    <div class="d-flex align-items-center justify-content-between w-100 me-2">
        <div class="d-flex align-items-center gap-2">
            <i class="fas fa-shopping-bag fs-4 text-warning"></i>
            <div>
                <h5 class="modal-title fw-bold text-white mb-0">
                    {{ __('Order Details') }} #{{ $order->prefix . $order->order_code }}
                </h5>
                <small class="text-white-50"><i class="far fa-clock me-1"></i>{{ $order->created_at->format('d M Y, h:i A') }}</small>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('shop.download-invoice', $order->id) }}" target="_blank" class="btn btn-sm btn-light text-primary fw-bold shadow-sm">
                <i class="fas fa-file-pdf me-1 text-danger"></i>{{ __('Invoice PDF') }}
            </a>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
    </div>
</div>

<div class="modal-body p-4 bg-light-subtle">
    <!-- Status Overview Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-12 p-3 text-center bg-white h-100">
                <span class="text-muted small fw-semibold text-uppercase d-block mb-1">{{ __('Order Status') }}</span>
                <div>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 fs-6 rounded-pill fw-bold">
                        <i class="fas fa-info-circle me-1"></i>{{ is_object($order->order_status) ? $order->order_status->value : $order->order_status }}
                    </span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-12 p-3 text-center bg-white h-100">
                <span class="text-muted small fw-semibold text-uppercase d-block mb-1">{{ __('Payment Status') }}</span>
                <div>
                    @php
                        $payStatus = is_object($order->payment_status) ? $order->payment_status->value : $order->payment_status;
                    @endphp
                    <span class="badge {{ $payStatus == 'Paid' ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-warning-subtle text-warning border border-warning-subtle' }} px-3 py-2 fs-6 rounded-pill fw-bold">
                        <i class="fas {{ $payStatus == 'Paid' ? 'fa-check-circle' : 'fa-exclamation-circle' }} me-1"></i>{{ $payStatus }}
                    </span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-12 p-3 text-center bg-white h-100">
                <span class="text-muted small fw-semibold text-uppercase d-block mb-1">{{ __('Payment Method') }}</span>
                <div>
                    <span class="badge bg-secondary-subtle text-dark border border-secondary-subtle px-3 py-2 fs-6 rounded-pill fw-bold">
                        <i class="fas fa-credit-card me-1"></i>{{ is_object($order->payment_method) ? $order->payment_method->value : $order->payment_method }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer & Shipping Grid -->
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-12 h-100 bg-white">
                <div class="card-header bg-white border-bottom py-2.5 px-3 fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="fas fa-user-circle text-primary fs-5"></i>{{ __('Customer Details') }}
                </div>
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">{{ __('Customer Name') }}:</span>
                        <span class="fw-bold text-dark">
                            {{ $order->customer?->user?->name ?? ($order->pos_order ? __('Walk-in Customer / POS') : __('N/A')) }}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted small">{{ __('Phone Number') }}:</span>
                        <span class="fw-semibold text-dark">{{ $order->customer?->user?->phone ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-12 h-100 bg-white">
                <div class="card-header bg-white border-bottom py-2.5 px-3 fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="fas fa-map-marker-alt text-warning fs-5"></i>{{ __('Shipping Address') }}
                </div>
                <div class="card-body p-3">
                    @if ($order->pos_order || !$order->address)
                        <div class="text-muted fst-italic small">
                            <i class="fas fa-store me-1 text-secondary"></i>{{ __('POS Counter Sale (In-Store Purchase - No Shipping Required)') }}
                        </div>
                    @else
                        <div class="fw-bold text-dark mb-1">{{ $order->address->name }} ({{ $order->address->phone }})</div>
                        <div class="text-muted small">{{ $order->address->address_line }}, {{ $order->address->area }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Products Table -->
    <div class="card border-0 shadow-sm rounded-12 mb-4 bg-white overflow-hidden">
        <div class="card-header bg-white border-bottom py-3 px-3 fw-bold text-dark d-flex align-items-center justify-content-between">
            <span class="d-flex align-items-center gap-2">
                <i class="fas fa-boxes text-success fs-5"></i>{{ __('Order Items') }}
            </span>
            <span class="badge bg-light text-dark border">{{ count($order->products) }} {{ __('Items') }}</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-uppercase small text-muted">
                    <tr>
                        <th class="text-center" style="width: 50px;">#</th>
                        <th>{{ __('Product') }}</th>
                        <th class="text-center" style="width: 90px;">{{ __('Qty') }}</th>
                        <th class="text-end" style="width: 120px;">{{ __('Unit Price') }}</th>
                        <th class="text-end" style="width: 130px;">{{ __('Total Amount') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($order->products as $key => $product)
                        @php
                            $price = $product->pivot->price > 0 ? $product->pivot->price : ($product->discount_price > 0 ? $product->discount_price : $product->price);
                        @endphp
                        <tr>
                            <td class="text-center fw-bold text-muted">{{ $key + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <img src="{{ $product->thumbnail }}" alt="" class="rounded border p-1 bg-light" width="48" height="48" style="object-fit: cover;">
                                    <div>
                                        <div class="fw-bold text-dark">{{ $product->name }}</div>
                                        @if (module_exists('purchase') && !empty($product->pivot->sku))
                                            <span class="badge bg-light text-muted border">#SKU: {{ $product->pivot->sku }}</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark border fs-6 px-3 py-1 fw-bold">{{ $product->pivot->quantity }}</span>
                            </td>
                            <td class="text-end fw-semibold text-muted">{{ showCurrency($price) }}</td>
                            <td class="text-end fw-bold text-dark">{{ showCurrency($product->pivot->quantity * $price) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">{{ __('No products found for this order.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Financial Calculation Summary Card -->
    <div class="row justify-content-end">
        <div class="col-md-6 col-lg-5">
            <div class="card border-0 shadow-sm rounded-12 bg-white">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">{{ __('Sub Total') }}</span>
                        <span class="fw-semibold">{{ showCurrency($order->total_amount) }}</span>
                    </div>
                    @if($order->coupon_discount > 0)
                        <div class="d-flex justify-content-between mb-2 text-danger">
                            <span class="small">{{ __('Coupon Discount') }}</span>
                            <span class="fw-semibold">-{{ showCurrency($order->coupon_discount) }}</span>
                        </div>
                    @endif
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">{{ __('Delivery Charge') }}</span>
                        <span class="fw-semibold">{{ showCurrency($order->delivery_charge) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted small">{{ __('VAT & Tax') }}</span>
                        <span class="fw-semibold">{{ showCurrency($order->tax_amount) }}</span>
                    </div>
                    <div class="d-flex justify-content-between border-top pt-2 mt-1">
                        <span class="fw-bold fs-5 text-dark">{{ __('Grand Total') }}</span>
                        <span class="fw-bold fs-5 text-primary">{{ showCurrency($order->payable_amount) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal-footer bg-white border-top py-3 px-4 d-flex justify-content-between align-items-center">
    <div class="small text-muted">
        <i class="fas fa-info-circle me-1"></i>{{ __('Showing details for Order') }} <strong>#{{ $order->prefix . $order->order_code }}</strong>
    </div>
    <button type="button" class="btn btn-secondary px-4 fw-semibold" data-bs-dismiss="modal">{{ __('Close') }}</button>
</div>
