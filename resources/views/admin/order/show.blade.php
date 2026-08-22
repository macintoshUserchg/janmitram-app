@extends('layouts.app')
@section('header-title', __('Order Details'))

@section('content')
    <div class="admin-order-show">
    <div class="row my-3 g-3">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between flex-column flex-md-row  gap-2 py-3">
                    <h4 class="card-title mb-0">{{ __('Order Details') }}</h4>
                    <div class="d-flex gap-2 flex-wrap order-detail-actions">
                        @hasPermission(['shop.order.attach.barcode'])
                            @if (module_exists('purchase'))
                                <button type="button" class="btn btn-info py-2.5" data-bs-toggle="modal"
                                    data-bs-target="#stockOutModal">
                                    {{ __('Attach Product Barcode') }}
                                </button>
                            @endif
                        @endhasPermission
                        <a href="{{ route('shop.payment-slip', $order->id) }}" target="_blank"
                            class="btn btn-success py-2.5">
                            <img src="{{ asset('assets/icons-admin/download-alt.svg') }}" alt="icon" loading="lazy"
                                width="20" />
                            {{ __('Payment Slip') }}
                        </a>
                        <a href="{{ route('shop.download-invoice', $order->id) }}" target="_blank"
                            class="btn btn-primary py-2.5">
                            <img src="{{ asset('assets/icons-admin/download-alt.svg') }}" alt="icon" loading="lazy"
                                width="20" />
                            {{ __('Download Invoice') }}
                        </a>
                        <button type="button" class="btn btn-warning " id="orderLocation" data-id="{{ $order->id }}"
                            data-bs-toggle="modal" data-bs-target="#orderLocationModal">
                            <i class="fa-solid fa-location-dot"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex gap-3 flex-wrap align-items-center order-summary-grid">
                        <div class="flex-grow-1 order-summary-column">
                            <div class="order-item">
                                <label class="label">{{ __('Order Id') }}:</label>
                                <span class="value">#{{ $order->prefix . $order->order_code }}</span>
                            </div>
                            <div class="order-item">
                                <label class="label">{{ __('Payment Status') }}:</label>
                                <span class="value">{{ $order->payment_status }}</span>
                            </div>
                            <div class="order-item">
                                <label class="label">{{ __('Payment Method') }}:</label>
                                <span class="value">{{ $order->payment_method }}</span>
                            </div>
                        </div>

                        <div class="item-divider"></div>

                        <div class="flex-grow-1 order-summary-column">
                            <div class="order-item">
                                <label class="label">{{ __('Order Status') }}:</label>
                                <span class="value">{{ $order->order_status }}</span>
                            </div>
                            <div class="order-item">
                                <label class="label">{{ __('Order Date') }}:</label>
                                <span class="value">{{ $order->created_at->format('M d, Y') }}</span>
                            </div>
                            <div class="order-item">
                                <label class="label">{{ __('Delivery Date') }}:</label>
                                <span
                                    class="value">{{ $order->delivery_date ? Carbon\Carbon::parse($order->delivery_date)->format('M d, Y') : '-' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive mt-4 mb-0">
                        <table class="table border-left-right order-products-table">
                            <thead>
                                <tr>
                                    <th>{{ __('SL') }}</th>
                                    <th>{{ __('Product') }}</th>
                                    @if ($businessModel == 'multi')
                                        <th>{{ __('Shop') }}</th>
                                    @endif
                                    <th>{{ __('Quantity') }}</th>
                                    <th>{{ __('Size') }}</th>
                                    <th>{{ __('Color') }}</th>
                                    <th>{{ __('Price') }}</th>
                                    <th class="text-end">{{ __('Total') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order->products as $key => $product)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>
                                            <div class="d-flex gap-1 align-items-center order-product-cell">
                                                <img src="{{ $product->thumbnail }}" alt="" width="40"
                                                    height="40" loading="lazy">
                                                <span class="order-product-name">{{ $product->name }}
                                                    @if (module_exists('purchase') && !empty($product->pivot->sku))
                                                        <span class="fw-bold">
                                                            #{{ __('SKU') }}:
                                                            <span class="text-primary">({{ $product->pivot->sku }})</span>
                                                        </span>
                                                    @endif
                                                </span>
                                            </div>
                                        </td>
                                        @if ($businessModel == 'multi')
                                            <td>{{ $product->shop?->name }}</td>
                                        @endif
                                        <td>{{ $product->pivot->quantity }}</td>
                                        <td>{{ $product->pivot->size ?? '-' }}</td>
                                        <td>{{ $product->pivot->color ?? '-' }}</td>
                                        <td>
                                            @php
                                                $price =
                                                    $product->pivot->price > 0
                                                        ? $product->pivot->price
                                                        : ($product->discount_price > 0
                                                            ? $product->discount_price
                                                            : $product->price);
                                            @endphp
                                            {{ showCurrency($price) }}
                                        </td>
                                        <td class="text-end">
                                            {{ showCurrency($product->pivot->quantity * $price) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @php
                        $couponDisc = (float)($order->coupon_discount ?? 0);
                        $cardDisc = (float)($order->card_discount ?? 0);
                        $otherDisc = max(0, (float)($order->discount ?? 0) - $couponDisc - $cardDisc);
                        $totalDisc = $couponDisc + $cardDisc + $otherDisc;

                        $taxAmount = (float)($order->tax_amount ?? 0);
                        $totalAmount = (float)($order->total_amount ?? 0);
                        $discountedItems = max(0, $totalAmount - $totalDisc);
                        $discountFactor = $totalAmount > 0 ? ($discountedItems / $totalAmount) : 1.0;

                        $grossTax = $discountFactor > 0 ? ($taxAmount / $discountFactor) : $taxAmount;
                        $preTaxable = number_format(max(0, $totalAmount - $grossTax), 2, '.', '');
                        $netTaxable = number_format(max(0, $discountedItems - $taxAmount), 2, '.', '');
                        $baseDiscount = number_format(max(0, (float)$preTaxable - (float)$netTaxable), 2, '.', '');
                        $taxSavings = number_format(max(0, $grossTax - $taxAmount), 2, '.', '');
                    @endphp

                    <div class="max-300 ms-auto d-flex flex-column gap-1 order-total-summary">
                        <div class="d-flex align-items-center justify-content-between gap-2">
                            <div>{{ __('Item Total (MRP)') }}</div>
                            <div class="fw-semibold">{{ showCurrency($order->total_amount) }}</div>
                        </div>

                        @if ($preTaxable > 0)
                            <div class="d-flex align-items-center justify-content-between gap-2 text-muted" style="font-size: 12px;">
                                <div>{{ __('Price Without GST (Taxable Base)') }}</div>
                                <div>{{ showCurrency($preTaxable) }}</div>
                            </div>
                        @endif

                        @if ($cardDisc > 0)
                            <div class="d-flex align-items-center justify-content-between gap-2 text-primary">
                                <div>
                                    <div>{{ __('Card Discount') }} {{ $order->card ? '(' . $order->card->card_number . ')' : '' }}</div>
                                    @if ($baseDiscount > 0)
                                        <div class="text-muted" style="font-size: 11px;">({{ showCurrency($baseDiscount) }} base + {{ showCurrency($taxSavings) }} GST saved)</div>
                                    @endif
                                </div>
                                <div class="fw-semibold">-{{ showCurrency($cardDisc) }}</div>
                            </div>
                        @endif

                        @if ($couponDisc > 0)
                            <div class="d-flex align-items-center justify-content-between gap-2 text-danger">
                                <div>{{ __('Coupon Discount') }} {{ $order->coupon ? '(' . $order->coupon->code . ')' : '' }}</div>
                                <div class="fw-semibold">-{{ showCurrency($couponDisc) }}</div>
                            </div>
                        @endif

                        @if ($otherDisc > 0)
                            <div class="d-flex align-items-center justify-content-between gap-2 text-warning">
                                <div>{{ __('Special Discount') }}</div>
                                <div class="fw-semibold">-{{ showCurrency($otherDisc) }}</div>
                            </div>
                        @endif

                        @if ($netTaxable > 0 && $totalDisc > 0)
                            <div class="d-flex align-items-center justify-content-between gap-2 text-muted border-top border-dashed pt-1" style="font-size: 12px;">
                                <div>{{ __('Net Taxable Value (After Discount)') }}</div>
                                <div class="fw-medium text-dark">{{ showCurrency($netTaxable) }}</div>
                            </div>
                        @endif

                        @if ($totalDisc > 0)
                            <div class="d-flex align-items-center justify-content-between gap-2">
                                <div>{{ __('Subtotal After Discount') }}</div>
                                <div class="fw-bold">{{ showCurrency($discountedItems) }}</div>
                            </div>
                        @endif

                        <div class="d-flex align-items-center justify-content-between gap-2">
                            <div>{{ __('Delivery Charge') }}</div>
                            <div>
                                @if ($order->delivery_charge == 0)
                                    <span class="text-success fw-medium">FREE</span>
                                @else
                                    {{ showCurrency($order->delivery_charge) }}
                                @endif
                            </div>
                        </div>

                        @if ($order->vatTaxes && $order->vatTaxes->count() > 0)
                            <div class="p-2 my-1 rounded bg-light border">
                                <div class="d-flex align-items-center justify-content-between gap-2 text-success fw-medium" style="font-size: 12px;">
                                    <span>🛡️ {{ __('GST & Taxes (Included in Prices)') }}</span>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">{{ showCurrency($taxAmount) }}</span>
                                </div>
                                @foreach ($order->vatTaxes as $vatTax)
                                    <div class="d-flex align-items-center justify-content-between gap-2 text-muted mt-1 pt-1 border-top" style="font-size: 11px;">
                                        <div>{{ $vatTax->name }} ({{ $vatTax->percentage }}%):</div>
                                        <div>{{ showCurrency($vatTax->amount) }}</div>
                                    </div>
                                @endforeach
                            </div>
                        @elseif ($order->tax_amount > 0)
                            <div class="p-2 my-1 rounded bg-light border">
                                <div class="d-flex align-items-center justify-content-between gap-2 text-success fw-medium" style="font-size: 12px;">
                                    <span>🛡️ {{ __('GST & Taxes (Included in Prices)') }}</span>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">{{ showCurrency($order->tax_amount) }}</span>
                                </div>
                            </div>
                        @endif

                        <div class="d-flex align-items-center justify-content-between gap-2 border-top pt-2 mt-1">
                            <div class="fw-bold" style="font-size: 15px;">{{ __('Grand Total') }}</div>
                            <div class="fw-bold text-primary" style="font-size: 15px;">{{ showCurrency($order->payable_amount) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!--##### Customer Info #####-->
            <div class="mt-3 card">
                <h5 class="fz-16 border-bottom px-3 py-12 m-0">{{ __('Customer Info') }}</h5>

                <div class="border-bottom px-3 py-2 d-flex align-items-center gap-3 customer-info-row">
                    <span class="text-color">{{ __('Name') }}: </span>
                    <span class="fw-medium">{{ $order->customer?->user?->name }}</span>
                </div>
                <div class="px-3 py-2 d-flex align-items-center gap-3 customer-info-row">
                    <span class="text-color">{{ __('Phone') }}: </span>
                    <span class="fw-medium">{{ $order->customer?->user?->phone }}</span>
                </div>
            </div>

        </div>

        <div class="col-lg-4">
            <!--##### Order & Shipping Info #####-->
            <div class="card">
                <h5 class="fz-18 border-bottom p-3 m-0">{{ __('Order & Shipping Info') }}</h5>

                <div class="px-3 py-2 d-flex justify-content-between align-items-center flex-wrap gap-2 border-bottom info-row">
                    <div class="text-color">{{ __('Change Order Status') }}</div>
                    <div class="dropdown info-row-control">
                        <a class="btn border text-start dropdown-toggle" href="#" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            {{ $order->order_status->value }}
                        </a>
                        @if ($order->order_status->value != 'Delivered' && $order->order_status->value != 'Cancelled')
                            @hasPermission(['admin.order.status.change'])
                                <ul class="dropdown-menu order-status">
                                    @foreach ($orderStatus as $status)
                                        <li>
                                            <a class="dropdown-item @if (in_array($status->value, ['Delivered', 'Cancelled'])) OrderStatusConfirm @endif"
                                                href="{{ route('admin.order.status.change', $order->id) }}?status={{ $status->value }}">
                                                {{ __($status->value) }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            @endhasPermission
                        @endif
                    </div>
                </div>

                <div class="border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2 p-3 info-row">
                    <div class="text-color">{{ __('Payment Status') }}</div>
                    <div class="d-flex align-items-center gap-1 info-row-control">
                        <span>{{ $order->payment_status }}</span>
                        @hasPermission('admin.order.payment.status.toggle')
                            <label class="switch mb-0">
                                <a href="{{ route('admin.order.payment.status.toggle', $order->id) }}">
                                    <input type="checkbox" {{ $order->payment_status->value == 'Paid' ? 'checked' : '' }}>
                                    <span class="slider round"></span>
                                </a>
                            </label>
                        @endhasPermission
                    </div>
                </div>

                @hasPermission('admin.rider.assign.order')
                    @if ($order->order_status->value != 'Pending')
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 p-3 info-row">
                            <div class="fw-medium text-color">{{ __('Assign Rider') }}</div>
                            <div class="d-flex align-items-center gap-1 info-row-control">

                                @if ($order->driverOrder)
                                    <span>{{ $order->driverOrder->driver?->user?->fullName }}</span>
                                @else
                                    <button class="btn btn-outline-primary" data-bs-toggle="modal"
                                        data-bs-target="#assignRider">
                                        <img src="{{ asset('assets/icons-admin/truck-fill.svg') }}" alt="icon"
                                            loading="lazy" />
                                        {{ __('Assign') }}
                                    </button>
                                @endif

                            </div>
                        </div>
                    @endif
                @endhasPermission
            </div>

            <!--##### Shipping Address #####-->
            <div class="card mt-3">
                <h5 class="fz-18 border-bottom p-3 m-0">{{ __('Shipping Address') }}</h5>

                <div class="border-bottom d-flex align-items-center justify-content-between gap-2 px-3 py-12 shipping-row">
                    <span class="text-color">{{ __('Name') }}: </span>
                    <span class="fw-medium">{{ $order->address?->name }}</span>
                </div>
                <div class="border-bottom d-flex align-items-center justify-content-between gap-2 px-3 py-12 shipping-row">
                    <span class="text-color">{{ __('Phone') }}: </span>
                    <span class="fw-medium">{{ $order->address?->phone }}</span>
                </div>
                <div class="border-bottom d-flex align-items-center justify-content-between gap-2 px-3 py-12 shipping-row">
                    <span class="text-color">{{ __('Address Type') }}: </span>
                    <span class="fw-medium">{{ $order->address?->address_type }}</span>
                </div>
                <div class="border-bottom d-flex align-items-center justify-content-between gap-2 px-3 py-12 shipping-row">
                    <span class="text-color">{{ __('Area') }}: </span>
                    <span class="fw-medium">{{ $order->order_area ?? 'N/A' }}</span>
                </div>
                <div class="border-bottom d-flex align-items-center justify-content-between gap-2 px-3 py-12 shipping-row">
                    <span class="text-color">{{ __('Address Line') }}: </span>
                    <span class="fw-medium">{{ $order->address?->address_line }}</span>
                </div>
            </div>

        </div>
    </div>
    </div>

    <!-- Assign Rider Modal -->
    <form action="{{ route('admin.rider.assign.order', $order->id) }}" method="POST">
        @csrf
        <div class="modal fade" id="assignRider">
            <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title fs-5">{{ __('Select a rider') }}</h3>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="d-flex gap-2 flex-column">
                            @foreach ($riders as $rider)
                                <div class="w-100">
                                    <input type="radio" name="rider" value="{{ $rider->id }}"
                                        id="rider{{ $rider->id }}" class="btn-check">
                                    <label for="rider{{ $rider->id }}" class="btn riderSelectBtn">
                                        <div>
                                            <img src="{{ $rider->user->thumbnail }}" alt="profile"
                                                class="profilePhoto" />
                                            <span class="riderName">
                                                {{ $rider->user->fullName }}
                                            </span>
                                        </div>
                                        <div class="d-flex gap-1 align-items-center">
                                            <span class="text-muted inCompleted">
                                                {{ __('Incomplete Orders') }}:
                                            </span>
                                            <span class="totalOrders">{{ $rider->incompleteOrders()->count() }}</span>
                                        </div>

                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">
                            {{ __('Assign Now') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    @if (module_exists('purchase'))
        <form id="scannerForm" method="POST" action="{{ route('shop.order.attach.barcode') }}">
            @csrf
            <input type="hidden" name="order_id" value="{{ $order->id }}" />
            <div class="modal fade" id="stockOutModal">
                <div class="modal-dialog modal-lg modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">{{ __('Scan Barcode for attachment') }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="barcodeInput"
                                    class="form-label">{{ __('Enter Barcode Manually / Scan Barcode') }}</label>
                                <div class="input-group">
                                    <input type="text" id="barcodeInput" class="form-control"
                                        placeholder="Type barcode and press Enter" autofocus />
                                </div>
                            </div>
                            <h6>{{ __('Scanned Products') }}:</h6>
                            <div id="scanner-container" class="mb-3 p-2"></div>
                        </div>
                        <div class="modal-footer justify-content-between">
                            <button type="button" class="btn btn-outline-secondary"
                                data-bs-dismiss="modal">{{ __('Close') }}</button>
                            <button type="submit" class="btn btn-primary py-2.5 px-4" id="scanSubmit">
                                {{ __('Confirm Submit') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    @endif
    <!--Order Location Modal -->
    <div class="modal fade" id="orderLocationModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="modal-title fw-bold" id="staticBackdropLabel">
                            <i class="fa-solid fa-location-dot text-danger me-2"></i>{{ __('Order Live Location') }} - #{{ $order->prefix . $order->order_code }}
                        </h5>
                        <small class="text-muted">{{ $order->address->address ?? 'Doorstep Location' }}</small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0 position-relative">
                    <div class="p-3 bg-light border-bottom d-flex flex-wrap align-items-center justify-content-between gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-primary px-3 py-2 fs-6">
                                <i class="fa fa-home me-1"></i> {{ __('Customer') }}: <span id="adminOrderCoords">{{ number_format($order->address->latitude ?? 27.0056949, 6) }}, {{ number_format($order->address->longitude ?? 75.7775497, 6) }}</span>
                            </span>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="copyAdminOrderCoords" title="{{ __('Copy') }}">
                                <i class="fa-regular fa-copy"></i>
                            </button>
                        </div>
                        <div id="adminRiderCoordsWrap" class="d-none d-flex align-items-center gap-2">
                            <span class="badge bg-warning text-dark px-3 py-2 fs-6">
                                <i class="fa-solid fa-motorcycle me-1"></i> {{ __('Rider') }}: <span id="adminRiderCoords"></span>
                            </span>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="copyAdminRiderCoords" title="{{ __('Copy') }}">
                                <i class="fa-regular fa-copy"></i>
                            </button>
                        </div>
                    </div>
                    <div id="map" style="height: 70vh; width: 100%;"></div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('css')
    <style>
        .admin-order-show .card,
        .admin-order-show .modal-content {
            max-width: 100%;
        }

        .admin-order-show .order-detail-actions {
            justify-content: flex-end;
        }

        .admin-order-show .order-detail-actions .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .admin-order-show .order-summary-column {
            min-width: 0;
        }

        .admin-order-show .order-products-table {
            min-width: 760px;
        }

        .admin-order-show .order-products-table td,
        .admin-order-show .order-products-table th {
            vertical-align: middle;
        }

        .admin-order-show .order-product-cell {
            min-width: 220px;
        }

        .admin-order-show .order-product-name {
            overflow-wrap: anywhere;
        }

        .admin-order-show .order-total-summary {
            width: 100%;
        }

        .dropdown-menu.order-status {
            min-width: 200px;
            padding: 8px;
            border: 1px solid #e5e5e5;
            box-shadow: 0 0 10px #e5e5e5;
        }

        .dropdown-menu.order-status .dropdown-item {
            border-bottom: 1px solid #f1f1f1;
        }

        .app-theme-dark .dropdown-menu.order-status {
            border: 1px solid #343a40;
            box-shadow: 0 0 10px #343a40;
        }

        .app-theme-dark .dropdown-menu.order-status .dropdown-item {
            border-bottom: 1px solid #343a40;
        }

        .max-300 {
            max-width: 340px;
        }

        .min-w-200 {
            min-width: 200px;
            display: inline;
        }

        .item-divider {
            height: 80px;
            width: 1px;
            background: #e5e5e5;
            margin: 0 20px;
        }

        .app-theme-dark .item-divider {
            background: #343a40;
        }

        .order-item {
            display: flex;
            gap: 10px;
            align-items: center;
            justify-content: space-between;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .order-item:last-child {
            margin-bottom: 0;
        }

        .order-item .label {
            color: #687387;
            line-height: 22px;
        }

        .app-theme-dark .order-item .label {
            color: #8f96a6;
        }

        .order-item .value {
            line-height: 22px;
            font-weight: 500;
            color: #000;
        }

        .app-theme-dark .order-item .value {
            color: #fff;
        }

        @media (max-width: 991.98px) {
            .admin-order-show .card-header {
                align-items: flex-start !important;
            }

            .admin-order-show .order-detail-actions {
                justify-content: flex-start;
                width: 100%;
            }

            .admin-order-show .order-products-table {
                min-width: 680px;
            }

            .admin-order-show .info-row,
            .admin-order-show .shipping-row {
                align-items: flex-start !important;
            }
        }

        @media (max-width: 768px) {
            .item-divider {
                display: none;
            }

            .admin-order-show .card-header {
                padding: 1rem !important;
            }

            .admin-order-show .card-body {
                padding: 1rem;
            }

            .admin-order-show .order-detail-actions .btn {
                width: 100%;
            }

            .admin-order-show .order-summary-grid {
                gap: 1rem !important;
            }

            .admin-order-show .order-item {
                flex-direction: column;
                align-items: flex-start;
            }

            .admin-order-show .order-item .value {
                width: 100%;
                overflow-wrap: anywhere;
            }

            .admin-order-show .order-products-table {
                min-width: 620px;
            }

            .admin-order-show .customer-info-row,
            .admin-order-show .info-row,
            .admin-order-show .shipping-row {
                flex-direction: column;
                align-items: flex-start !important;
                gap: 6px !important;
            }

            .admin-order-show .info-row-control {
                width: 100%;
                justify-content: space-between;
            }

            .admin-order-show .info-row-control .btn,
            .admin-order-show .info-row-control .dropdown-toggle {
                max-width: 100%;
            }

            .admin-order-show #map {
                height: 55vh !important;
            }
        }

        @media (max-width: 575.98px) {
            .admin-order-show .row.my-3 {
                margin-top: 0.75rem !important;
                margin-bottom: 0.75rem !important;
            }

            .admin-order-show .card-title {
                font-size: 1.1rem;
            }

            .admin-order-show .order-detail-actions {
                gap: 0.75rem !important;
            }

            .admin-order-show .order-products-table {
                min-width: 560px;
            }

            .admin-order-show .max-300 {
                max-width: 100%;
            }

            .admin-order-show .dropdown-menu.order-status {
                min-width: 180px;
            }

            .admin-order-show .modal-dialog {
                margin: 0.75rem;
            }

            .admin-order-show #map {
                height: 50vh !important;
            }
        }
    </style>
@endpush
@push('scripts')
    <script>
        $(document).ready(function() {
            $(".dropdown-menu").on("click", ".OrderStatusConfirm", function(e) {
                e.preventDefault();
                e.stopImmediatePropagation();

                const url = $(this).attr("href");
                const statusName = $(this).text().trim();

                Swal.fire({
                    title: "Are you sure?",
                    text: `Do you really want to mark this order as ${statusName}?`,
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes, proceed!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = url;
                    }
                });
            });
        });
    </script>

@push('scripts')
    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}&libraries=places,geometry"></script>
    <script>
        let gOrderMap = null;
        let gCustomerMarker = null;
        let gRiderMarker = null;
        let gRoutePolyline = null;
        let trackingInterval = null;
        let riderId = @json($order->driverOrder->driver_id ?? null);
        let riderChannel = null;

        const orderStatus = @json($order->order_status);

        let rawLat = parseFloat({{ $order->address->latitude ?? 0 }});
        let rawLng = parseFloat({{ $order->address->longitude ?? 0 }});
        if (isNaN(rawLat) || isNaN(rawLng) || (rawLat === 0 && rawLng === 0)) {
            rawLat = 27.005694931660006;
            rawLng = 75.77754972401056;
        }
        const orderLat = rawLat;
        const orderLng = rawLng;

        const blockedStatuses = ['Delivered', 'Cancelled'];

        function canShowRiderLocation() {
            return riderId && !blockedStatuses.includes(orderStatus);
        }

        function initMap(riderLat, riderLng) {
            const mapEl = document.getElementById('map');
            if (!mapEl || !window.google || !window.google.maps) return;

            const orderLatLng = new google.maps.LatLng(orderLat, orderLng);

            gOrderMap = new google.maps.Map(mapEl, {
                center: orderLatLng,
                zoom: 15,
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                zoomControl: true,
                mapTypeControl: false,
                streetViewControl: false,
            });

            gCustomerMarker = new google.maps.Marker({
                position: orderLatLng,
                map: gOrderMap,
                title: "Customer Doorstep",
                icon: {
                    url: "{{ asset('assets/icons/home.png') }}",
                    scaledSize: new google.maps.Size(40, 40),
                }
            });

            const customerInfoWindow = new google.maps.InfoWindow({
                content: '<div class="fw-bold fs-6">Customer Doorstep</div>'
            });
            customerInfoWindow.open(gOrderMap, gCustomerMarker);

            if (!canShowRiderLocation() || !riderLat || !riderLng) {
                return;
            }

            const riderLatLng = new google.maps.LatLng(parseFloat(riderLat), parseFloat(riderLng));

            gRiderMarker = new google.maps.Marker({
                position: riderLatLng,
                map: gOrderMap,
                title: "Delivery Rider",
                icon: {
                    url: "{{ asset('assets/icons/pin-map.png') }}",
                    scaledSize: new google.maps.Size(42, 42),
                }
            });

            updateRouteLine(riderLatLng, orderLatLng);
        }

        function updateRouteLine(riderLatLng, orderLatLng) {
            if (!gOrderMap || !window.google) return;

            const bounds = new google.maps.LatLngBounds();
            bounds.extend(riderLatLng);
            bounds.extend(orderLatLng);

            const path = [riderLatLng, orderLatLng];

            if (!gRoutePolyline) {
                gRoutePolyline = new google.maps.Polyline({
                    path: path,
                    geodesic: true,
                    strokeColor: "#f59e0b",
                    strokeOpacity: 0.85,
                    strokeWeight: 4,
                    map: gOrderMap,
                });
            } else {
                gRoutePolyline.setPath(path);
            }

            gOrderMap.fitBounds(bounds, 60);
        }

        function subscribeToRiderLocation(riderId) {
            if (!canShowRiderLocation() || !gRiderMarker || typeof pusher === 'undefined') return;
            riderChannel = pusher.subscribe('rider-location.' + riderId);

            riderChannel.bind('rider.location.updated', function(data) {
                if (!gRiderMarker || data.location.driver_id !== riderId) return;

                const latitude = parseFloat(data.location.latitude);
                const longitude = parseFloat(data.location.longitude);
                const riderLatLng = new google.maps.LatLng(latitude, longitude);
                const orderLatLng = new google.maps.LatLng(orderLat, orderLng);

                $('#adminOrderCoords').text(orderLat.toFixed(6) + ', ' + orderLng.toFixed(6));
                gRiderMarker.setPosition(riderLatLng);
                updateRouteLine(riderLatLng, orderLatLng);
            });
        }

        $(document).on('click', '#orderLocation', function() {
            $('#orderLocationModal').modal('show');

            $('#orderLocationModal').one('shown.bs.modal', function() {
                $('#adminOrderCoords').text(orderLat.toFixed(6) + ', ' + orderLng.toFixed(6));

                initMap(orderLat, orderLng);

                if (!canShowRiderLocation() || !riderId) {
                    return;
                }

                $.ajax({
                    url: "{{ route('admin.rider.location', ':id') }}".replace(':id', riderId),
                    success: function(res) {
                        if (!res?.data?.location || !gOrderMap) return;

                        let { latitude, longitude } = res.data.location;
                        const latNum = parseFloat(latitude);
                        const lngNum = parseFloat(longitude);

                        $('#adminRiderCoords').text(latNum.toFixed(6) + ', ' + lngNum.toFixed(6));
                        $('#adminRiderCoordsWrap').removeClass('d-none');

                        const riderLatLng = new google.maps.LatLng(latNum, lngNum);
                        const orderLatLng = new google.maps.LatLng(orderLat, orderLng);

                        if (!gRiderMarker) {
                            gRiderMarker = new google.maps.Marker({
                                position: riderLatLng,
                                map: gOrderMap,
                                title: "Delivery Rider",
                                icon: {
                                    url: "{{ asset('assets/icons/pin-map.png') }}",
                                    scaledSize: new google.maps.Size(42, 42),
                                }
                            });
                        } else {
                            gRiderMarker.setPosition(riderLatLng);
                        }

                        updateRouteLine(riderLatLng, orderLatLng);
                        subscribeToRiderLocation(riderId);
                    }
                });
            });
        });

        function copyCoordinates(text) {
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text).then(() => {
                    toastr.success('Coordinates copied');
                });
            } else {
                var $tmp = $('<textarea>').val(text).appendTo('body').select();
                document.execCommand('copy');
                $tmp.remove();
                toastr.success('Coordinates copied');
            }
        }

        $(document).on('click', '#copyAdminOrderCoords', function() {
            copyCoordinates($('#adminOrderCoords').text());
        });

        $(document).on('click', '#copyAdminRiderCoords', function() {
            copyCoordinates($('#adminRiderCoords').text());
        });

        $('#orderLocationModal').on('hidden.bs.modal', function() {
            if (trackingInterval) {
                clearInterval(trackingInterval);
                trackingInterval = null;
            }
            if (gRoutePolyline) {
                gRoutePolyline.setMap(null);
                gRoutePolyline = null;
            }
            if (gRiderMarker) {
                gRiderMarker.setMap(null);
                gRiderMarker = null;
            }
            if (gCustomerMarker) {
                gCustomerMarker.setMap(null);
                gCustomerMarker = null;
            }
            gOrderMap = null;

            $('#adminRiderCoordsWrap').addClass('d-none');
        });
    </script>
@endpush
@if (module_exists('purchase'))
    @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/quagga/0.12.1/quagga.min.js"></script>
        <script>
            // scanner script
            let scannedBarcodes = new Set();
            let modal = document.getElementById("stockOutModal");

            function addScannedBarcode(barcode) {
                if (!scannedBarcodes.has(barcode)) {
                    fetchProductsBySku(barcode);

                    scannedBarcodes.add(barcode);
                    $('#barcodeInput').val('').focus();
                } else {
                    $('#barcodeInput').val('').focus();
                }
            }

            function absProductAssign(product_id) {
                $('#assignProductId').val(product_id);
                $('#assignRider').modal('show');
            }

            function fetchProductsBySku(sku) {
                $.ajax({
                    url: "{{ route('shop.order.fetch.products') }}",
                    type: "post",
                    data: {
                        sku: sku,
                        _token: "{{ csrf_token() }}",
                        order_id: "{{ $order->id }}"
                    },
                    success: function(response) {
                        let product = response.data.product;
                        let scannerContainer = document.getElementById("scanner-container");

                        if ($(`#scanned-product${product.id}`).length == 0) {

                            let html = `
                        <div class="w-100 border rounded p-4 shadow-sm" id="scanned-product${product.id}">
                            <div class="d-flex gap-1 align-items-center w-100 mb-1">
                                <div class="product-image">
                                    <img src="${product.thumbnail}" alt="thumbnail" loading="lazy" />
                                </div>
                                <div class="product-info">
                                    <div class="product-name">${product.name}</div>
                                </div>
                            </div>
                            <table class="table mt-1 w-100 border-left-right">
                                <thead>
                                    <tr>
                                        <th class="py-1">Barcode</th>
                                    </tr>
                                </thead>
                                <tbody id="scannedProduct${product.id}">
                                    <tr style="display: table-row !important">
                                        <td>${product.barcode}</td>
                                        <td>
                                            <input type="hidden" name="scanned_barcodes[]" value="${product.barcode}" />
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                        </div>`;

                            scannerContainer.insertAdjacentHTML('afterbegin', html);
                        } else {
                            let table = document.getElementById(`scannedProduct${product.id}`);
                            table.insertAdjacentHTML('afterbegin',
                                `<tr style="display: table-row !important">
                                <td>
                                    ${product.barcode}
                                    <input type="hidden" name="scanned_barcodes[]" value="${product.barcode}" />
                                </td>
                            </tr>`);
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: xhr.responseJSON.message
                        });
                    }
                })
            }

            // Handle manual barcode input
            document.getElementById("barcodeInput").addEventListener("keypress", function(event) {
                if (event.key === "Enter") {
                    event.preventDefault();
                    let barcode = this.value.trim();
                    if (barcode) {
                        addScannedBarcode(barcode);
                    }
                }
            });

            // Start QuaggaJS when modal opens
            function startScannerQuaggaJS() {
                Quagga.init({
                    inputStream: {
                        name: "Live",
                        type: "LiveStream",
                        target: document.getElementById("scanner-container"),
                        constraints: {
                            width: 640,
                            height: 300,
                            facingMode: "environment" // Rear camera (scanner gun)
                        }
                    },
                    decoder: {
                        readers: ["code_128_reader", "ean_reader", "ean_8_reader"]
                    }
                }, function(err) {
                    if (err) {
                        console.error(err);
                        return;
                    }
                    Quagga.start();
                });

                Quagga.onDetected(function(result) {
                    let barcode = result.codeResult.code;
                    addScannedBarcode(barcode);
                });
            }

            // Stop QuaggaJS when modal closes
            modal.addEventListener("hidden.bs.modal", function() {
                modal.setAttribute("aria-hidden", "true");
                modal.removeAttribute("aria-modal");
                Quagga.stop();
            });

            modal.addEventListener("shown.bs.modal", function() {
                setTimeout(function() {
                    document.getElementById("barcodeInput").focus();
                });
            })

            function scannerBarcode() {

                scannedBarcodes = new Set();

                $('#scannerModal').modal('show');
                modal.removeAttribute("aria-hidden");
                modal.setAttribute("aria-modal", "true");

                setTimeout(function() {
                    document.getElementById("barcodeInput").focus();
                }, 200);

                $('#scanner-container').empty();
                startScannerQuaggaJS();

                selectedOptions.each(function() {
                    var barcode = $(this).val();
                    addScannedBarcode(barcode);
                });
            }
        </script>
    @endpush
@endif
