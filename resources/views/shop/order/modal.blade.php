<div class="modal-header bg-light py-3">
    <div class="d-flex align-items-center justify-content-between w-100 me-3 flex-wrap gap-2">
        <div>
            <h5 class="modal-title fw-bold text-dark mb-0">
                <i class="fas fa-receipt text-primary me-2"></i>{{ __('Order Details') }} #{{ $order->prefix . $order->order_code }}
            </h5>
            <small class="text-muted"><i class="far fa-clock me-1"></i>{{ $order->created_at->format('M d, Y h:i A') }}</small>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('shop.payment-slip', $order->id) }}" target="_blank" class="btn btn-sm btn-success">
                <i class="fas fa-file-invoice me-1"></i>{{ __('Payment Slip') }}
            </a>
            <a href="{{ route('shop.download-invoice', $order->id) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-file-pdf me-1"></i>{{ __('Download Invoice') }}
            </a>
            <a href="{{ route('shop.order.show', $order->id) }}" class="btn btn-sm btn-primary">
                <i class="fas fa-external-link-alt me-1"></i>{{ __('Full Page View') }}
            </a>
        </div>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body p-4">
    <!-- Row 1: Order Info & Status Change Controls -->
    <div class="row g-3 mb-4">
        <div class="col-md-8">
            <div class="card h-100 border-light shadow-sm">
                <div class="card-body p-3">
                    <div class="row g-3">
                        <div class="col-6 col-sm-4">
                            <label class="text-muted small d-block">{{ __('Order ID') }}</label>
                            <span class="fw-bold text-dark">#{{ $order->prefix . $order->order_code }}</span>
                        </div>
                        <div class="col-6 col-sm-4">
                            <label class="text-muted small d-block">{{ __('Order Date') }}</label>
                            <span class="fw-semibold">{{ $order->created_at->format('M d, Y') }}</span>
                        </div>
                        <div class="col-6 col-sm-4">
                            <label class="text-muted small d-block">{{ __('Delivery Date') }}</label>
                            <span class="fw-semibold">{{ $order->delivery_date ? Carbon\Carbon::parse($order->delivery_date)->format('M d, Y') : '-' }}</span>
                        </div>
                        <div class="col-6 col-sm-4">
                            <label class="text-muted small d-block">{{ __('Payment Method') }}</label>
                            <span class="badge bg-secondary px-2.5 py-1.5 fs-6">{{ is_object($order->payment_method) ? $order->payment_method->value : $order->payment_method }}</span>
                        </div>
                        <div class="col-6 col-sm-4">
                            <label class="text-muted small d-block">{{ __('Payment Status') }}</label>
                            <span class="badge {{ (is_object($order->payment_status) ? $order->payment_status->value : $order->payment_status) == 'Paid' ? 'bg-success' : 'bg-warning text-dark' }} px-2.5 py-1.5 fs-6">
                                {{ is_object($order->payment_status) ? $order->payment_status->value : $order->payment_status }}
                            </span>
                        </div>
                        <div class="col-6 col-sm-4">
                            <label class="text-muted small d-block">{{ __('Current Order Status') }}</label>
                            <span class="badge bg-primary px-2.5 py-1.5 fs-6">{{ is_object($order->order_status) ? $order->order_status->value : $order->order_status }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card h-100 border-light shadow-sm bg-light">
                <div class="card-body p-3 d-flex flex-column justify-content-center">
                    <label class="text-muted small fw-bold mb-2">{{ __('Change Order Status') }}</label>
                    <div class="dropdown w-100">
                        <button class="btn btn-outline-primary dropdown-toggle w-100 text-start d-flex justify-content-between align-items-center" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <span>{{ is_object($order->order_status) ? $order->order_status->value : $order->order_status }}</span>
                        </button>
                        <ul class="dropdown-menu w-100 shadow-sm">
                            @foreach ($orderStatus as $status)
                                <li>
                                    <a class="dropdown-item py-2 {{ (is_object($order->order_status) ? $order->order_status->value : $order->order_status) == $status->value ? 'active fw-bold' : '' }}"
                                       href="{{ route('shop.order.status.change', $order->id) }}?status={{ $status->value }}">
                                        {{ __($status->value) }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Products Table -->
    <div class="card border-light shadow-sm mb-4">
        <div class="card-header bg-white fw-bold py-2 border-bottom">
            <i class="fas fa-box me-1 text-primary"></i>{{ __('Purchased Items') }} ({{ count($order->products) }})
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;" class="text-center">{{ __('SL') }}</th>
                        <th>{{ __('Product') }}</th>
                        <th class="text-center" style="width: 90px;">{{ __('Quantity') }}</th>
                        <th class="text-center" style="width: 80px;">{{ __('Size') }}</th>
                        <th class="text-center" style="width: 80px;">{{ __('Color') }}</th>
                        <th class="text-end" style="width: 110px;">{{ __('Unit Price') }}</th>
                        <th class="text-end" style="width: 120px;">{{ __('Total') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($order->products as $key => $product)
                        @php
                            $price = $product->pivot->price > 0 ? $product->pivot->price : ($product->discount_price > 0 ? $product->discount_price : $product->price);
                        @endphp
                        <tr>
                            <td class="text-center">{{ $key + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ $product->thumbnail }}" alt="" class="rounded border" width="42" height="42" style="object-fit: cover;">
                                    <div>
                                        <div class="fw-semibold text-dark">{{ $product->name }}</div>
                                        @if (module_exists('purchase') && !empty($product->pivot->sku))
                                            <small class="text-muted">#{{ __('SKU') }}: <span class="text-primary">{{ $product->pivot->sku }}</span></small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="text-center fw-bold">{{ $product->pivot->quantity }}</td>
                            <td class="text-center text-muted">{{ $product->pivot->size ?? '-' }}</td>
                            <td class="text-center text-muted">{{ $product->pivot->color ?? '-' }}</td>
                            <td class="text-end">{{ showCurrency($price) }}</td>
                            <td class="text-end fw-bold text-dark">{{ showCurrency($product->pivot->quantity * $price) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted fs-6">{{ __('No products found in this order.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Row 2: Customer Info, Shipping Address & Financial Totals -->
    <div class="row g-4">
        <!-- Customer Info -->
        <div class="col-md-4">
            <div class="card h-100 border-light shadow-sm">
                <div class="card-header bg-white fw-bold py-2 border-bottom">
                    <i class="fas fa-user me-1 text-primary"></i>{{ __('Customer Info') }}
                </div>
                <div class="card-body p-3">
                    <div class="mb-2">
                        <span class="text-muted d-block small">{{ __('Name') }}</span>
                        <span class="fw-semibold text-dark">
                            {{ $order->customer?->user?->name ?? ($order->pos_order ? __('Walk-in Customer / POS') : __('N/A')) }}
                        </span>
                    </div>
                    <div>
                        <span class="text-muted d-block small">{{ __('Phone') }}</span>
                        <span class="fw-semibold text-dark">{{ $order->customer?->user?->phone ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Shipping Address -->
        <div class="col-md-4">
            <div class="card h-100 border-light shadow-sm">
                <div class="card-header bg-white fw-bold py-2 border-bottom">
                    <i class="fas fa-map-marker-alt me-1 text-warning"></i>{{ __('Shipping Address') }}
                </div>
                <div class="card-body p-3">
                    @if ($order->pos_order || !$order->address)
                        <div class="text-muted fst-italic">
                            <i class="fas fa-store me-1"></i>{{ __('POS Counter Sale (In-Store Purchase - No Shipping Required)') }}
                        </div>
                    @else
                        <div class="fw-semibold text-dark mb-1">{{ $order->address->name }} ({{ $order->address->phone }})</div>
                        <div class="text-muted small">{{ $order->address->address_line }}, {{ $order->address->area }}</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Financial Order Summary -->
        <div class="col-md-4">
            <div class="card h-100 border-light shadow-sm">
                <div class="card-header bg-white fw-bold py-2 border-bottom">
                    <i class="fas fa-calculator me-1 text-success"></i>{{ __('Order Summary') }}
                </div>
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between mb-1.5">
                        <span class="text-muted">{{ __('Sub Total') }}</span>
                        <span class="fw-medium">{{ showCurrency($order->total_amount) }}</span>
                    </div>
                    @if($order->coupon_discount > 0)
                        <div class="d-flex justify-content-between mb-1.5 text-danger">
                            <span>{{ __('Coupon Discount') }}</span>
                            <span>-{{ showCurrency($order->coupon_discount) }}</span>
                        </div>
                    @endif
                    <div class="d-flex justify-content-between mb-1.5">
                        <span class="text-muted">{{ __('Delivery Charge') }}</span>
                        <span class="fw-medium">{{ showCurrency($order->delivery_charge) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">{{ __('VAT & Tax') }}</span>
                        <span class="fw-medium">{{ showCurrency($order->tax_amount) }}</span>
                    </div>
                    <div class="d-flex justify-content-between border-top pt-2 fw-bold fs-5 text-dark">
                        <span>{{ __('Grand Total') }}</span>
                        <span class="text-primary">{{ showCurrency($order->payable_amount) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal-footer bg-light py-2">
    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">{{ __('Close') }}</button>
</div>
