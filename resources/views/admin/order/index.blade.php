@extends('layouts.app')

@section('header-title', __('Orders'))

@section('content')
    <div class="admin-order-index">
        <div class="card">
            <div class="card-body">
                <ul class="nav nav-tabs order-tabs">
                @php
                    use App\Enums\OrderStatus;
                    $orderStatuses = OrderStatus::cases();
                @endphp


                        <li class="nav-item">
                        <a href="{{ route('admin.order.index') }}"
                            class="nav-link {{ request()->url() === route('admin.order.index') ? 'active' : '' }}">
                            {{ __('All') }}
                            {{-- <span class="count statusAll">{{ $allOrders > 99 ? '99+' : $allOrders }}</span> --}}
                        </a>
                        </li>

                        @foreach ($orderStatuses as $status)

                        <li class="nav-item">
                            <a href="{{ route('admin.order.index', str_replace(' ', '_', $status->value)) }}"
                                class="nav-link {{ request()->url() === route('admin.order.index', str_replace(' ', '_', $status->value)) ? 'active' : '' }}">
                                <span>{{ __($status->value) }}</span>
                            </a>
                        </li>
                        @endforeach

                </ul>
                <div class="table-responsive">

                    <table class="table border-left-right table-responsive-lg order-index-table">
                        <thead>
                            <tr>
                                <th style="min-width: 120px">
                                    @include('admin.partials.sortable-header', ['label' => __('Order ID'), 'column' => 'id', 'route' => 'admin.order.index', 'routeParam' => $status, 'sort' => $sort ?? 'id', 'direction' => $direction ?? 'desc'])
                                </th>
                                <th style="min-width: 150px">
                                    @include('admin.partials.sortable-header', ['label' => __('Order Date'), 'column' => 'created_at', 'route' => 'admin.order.index', 'routeParam' => $status, 'sort' => $sort ?? 'id', 'direction' => $direction ?? 'desc'])
                                </th>
                                <th style="min-width: 140px">
                                    @include('admin.partials.sortable-header', ['label' => __('Customer'), 'column' => 'customer_name', 'route' => 'admin.order.index', 'routeParam' => $status, 'sort' => $sort ?? 'id', 'direction' => $direction ?? 'desc'])
                                </th>
                                @if ($businessModel == 'multi')
                                    <th style="min-width: 140px">
                                        @include('admin.partials.sortable-header', ['label' => __('Shop'), 'column' => 'shop_name', 'route' => 'admin.order.index', 'routeParam' => $status, 'sort' => $sort ?? 'id', 'direction' => $direction ?? 'desc'])
                                    </th>
                                @endif
                                <th style="min-width: 140px">
                                    @include('admin.partials.sortable-header', ['label' => __('Discounts'), 'column' => 'discount', 'route' => 'admin.order.index', 'routeParam' => $status, 'sort' => $sort ?? 'id', 'direction' => $direction ?? 'desc'])
                                </th>
                                <th style="min-width: 130px">
                                    @include('admin.partials.sortable-header', ['label' => __('GST / Tax'), 'column' => 'tax_amount', 'route' => 'admin.order.index', 'routeParam' => $status, 'sort' => $sort ?? 'id', 'direction' => $direction ?? 'desc'])
                                </th>
                                <th style="min-width: 140px">
                                    @include('admin.partials.sortable-header', ['label' => __('Total Amount'), 'column' => 'payable_amount', 'route' => 'admin.order.index', 'routeParam' => $status, 'sort' => $sort ?? 'id', 'direction' => $direction ?? 'desc'])
                                </th>
                                <th style="min-width: 140px">
                                    @include('admin.partials.sortable-header', ['label' => __('Payment Method'), 'column' => 'payment_method', 'route' => 'admin.order.index', 'routeParam' => $status, 'sort' => $sort ?? 'id', 'direction' => $direction ?? 'desc'])
                                </th>
                                <th>{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($orders as $order)
                                <tr>
                                    <td class="w-auto order-code-cell fw-semibold text-dark">{{ $order->prefix . $order->order_code }}</td>
                                    <td class="w-min order-date-cell">{{ $order->created_at->format('d M Y, h:i A') }}</td>
                                    <td class="w-min order-customer-cell">{{ $order->customer?->user?->name }}</td>

                                    @if ($businessModel == 'multi')
                                        <td class="w-min order-shop-cell">
                                            {{ $order->shop?->name }}
                                        </td>
                                    @endif
                                    @php
                                        $couponDisc = (float)($order->coupon_discount ?? 0);
                                        $cardDisc = (float)($order->card_discount ?? 0);
                                        $otherDisc = max(0, (float)($order->discount ?? 0) - $couponDisc - $cardDisc);
                                        $totalDisc = $couponDisc + $cardDisc + $otherDisc;
                                    @endphp
                                    <td class="w-min order-discount-cell">
                                        @if ($totalDisc > 0)
                                            <span class="fw-bold text-danger">-{{ showCurrency($totalDisc) }}</span>
                                            <div class="mt-1 d-flex flex-wrap gap-1">
                                                @if ($couponDisc > 0)
                                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle font-monospace" style="font-size: 10px;">
                                                        Coupon: -{{ showCurrency($couponDisc) }}{{ $order->coupon ? ' (' . $order->coupon->code . ')' : '' }}
                                                    </span>
                                                @endif
                                                @if ($cardDisc > 0)
                                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-monospace" style="font-size: 10px;">
                                                        Card: -{{ showCurrency($cardDisc) }}{{ $order->card ? ' (' . $order->card->card_number . ')' : '' }}
                                                    </span>
                                                @endif
                                                @if ($otherDisc > 0)
                                                    <span class="badge bg-warning-subtle text-warning-emphasis border font-monospace" style="font-size: 10px;">
                                                        Special: -{{ showCurrency($otherDisc) }}
                                                    </span>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="w-min order-tax-cell">
                                        <span class="fw-bold text-dark">{{ showCurrency($order->tax_amount ?? 0) }}</span>
                                        @if ($order->vatTaxes && $order->vatTaxes->count() > 0)
                                            <div class="mt-1 d-flex flex-wrap gap-1">
                                                @foreach($order->vatTaxes as $vt)
                                                    <span class="badge bg-light text-secondary border font-monospace" style="font-size: 10px;">{{ $vt->name }}: {{ showCurrency($vt->amount) }}</span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </td>
                                    <td class="w-min order-amount-cell">
                                        <span class="fw-bold text-dark">{{ showCurrency($order->payable_amount) }}</span>
                                        <br>
                                        <span class="badge rounded-pill text-bg-primary order-payment-badge">{{ $order->payment_status }}</span>
                                    </td>
                                    <td class="w-min order-method-cell">{{ $order->payment_method }}</td>
                                    <td class="w-min order-action-cell">
                                        @hasPermission('admin.order.show')
                                            <a href="{{ route('admin.order.show', $order->id) }}" data-bs-toggle="tooltip"
                                                data-bs-placement="top" data-bs-title="{{ __('view details') }}"
                                                class="circleIcon svg-bg">
                                                <img src="{{ asset('assets/icons-admin/eye.svg') }}" alt="icon"
                                                    loading="lazy" />
                                            </a>
                                        @endhasPermission
                                        <a href="{{ route('shop.download-invoice', $order->id) }}" data-bs-toggle="tooltip"
                                            data-bs-placement="left" data-bs-title="{{ __('Download Invoice') }}"
                                            class="circleIcon btn-outline-secondary">
                                            <img src="{{ asset('assets/icons-admin/download-alt.svg') }}" alt="icon"
                                                loading="lazy" />
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="100%" class="text-center text-muted py-5">
                                        {{ __('No order found') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                </div>

            </div>
            <div class="card-footer bg-white border-top py-0 px-0">
                @include('admin.partials.pagination', ['paginator' => $orders])
            </div>
        </div>
    </div>

@endsection

@push('css')
    <style>
        .admin-order-index .order-tabs {
            gap: 10px;
            margin-bottom: 1rem;
            border-bottom: 0;
            flex-wrap: wrap;
        }

        .admin-order-index .order-tabs .nav-item {
            flex: 0 0 auto;
        }

        .admin-order-index .order-tabs .nav-link {
            border: 1px solid #d7dae0;
            border-radius: 999px;
            padding: 0.55rem 0.9rem;
            white-space: nowrap;
        }

        .admin-order-index .order-index-table {
            min-width: 760px;
        }

        .admin-order-index .order-index-table td,
        .admin-order-index .order-index-table th {
            vertical-align: middle;
        }

        .admin-order-index .order-code-cell,
        .admin-order-index .order-date-cell,
        .admin-order-index .order-customer-cell,
        .admin-order-index .order-shop-cell,
        .admin-order-index .order-method-cell {
            white-space: nowrap;
        }

        .admin-order-index .order-amount-cell {
            min-width: 140px;
        }

        .admin-order-index .order-payment-badge {
            margin-top: 6px;
            display: inline-flex;
        }

        .admin-order-index .order-action-cell {
            white-space: nowrap;
        }

        .admin-order-index .order-action-cell .circleIcon,
        .admin-order-index .order-action-cell .btn-outline-secondary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .admin-order-index .order-pagination .pagination {
            flex-wrap: wrap;
            gap: 8px;
        }

        @media (max-width: 991.98px) {
            .admin-order-index .card-body {
                padding: 1rem;
            }

            .admin-order-index .order-tabs {
                gap: 8px;
            }

            .admin-order-index .order-index-table {
                min-width: 700px;
            }
        }

        @media (max-width: 767.98px) {
            .admin-order-index .order-tabs {
                flex-wrap: nowrap;
                overflow-x: auto;
                padding-bottom: 4px;
                scrollbar-width: thin;
            }

            .admin-order-index .order-tabs .nav-link {
                padding: 0.5rem 0.85rem;
                font-size: 14px;
            }

            .admin-order-index .order-index-table {
                min-width: 640px;
            }

            .admin-order-index .order-action-cell {
                display: flex;
                flex-wrap: wrap;
                gap: 8px;
            }

            .admin-order-index .order-payment-badge {
                white-space: normal;
            }
        }

        @media (max-width: 575.98px) {
            .admin-order-index .card-body {
                padding: 0.875rem;
            }

            .admin-order-index .order-tabs {
                margin-bottom: 0.875rem;
            }

            .admin-order-index .order-tabs .nav-link {
                font-size: 13px;
            }

            .admin-order-index .order-index-table {
                min-width: 560px;
            }

            .admin-order-index .order-amount-cell {
                min-width: 120px;
            }
        }
    </style>
@endpush
