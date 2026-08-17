@extends('layouts.app')

@section('header-title', __('POS Sales'))

@section('content')
<!-- Integrated 2-Line Compact Header Banner Card -->
<div class="card border-0 shadow-sm rounded-12 mb-4 bg-gradient bg-primary text-white">
    <div class="card-body p-3">
        <div class="d-flex justify-content-between align-items-center mb-1">
            <h1 class="h4 mb-0 text-white fw-bold"><i class="fas fa-receipt me-2 text-warning"></i>{{ __('POS Sales History') }}</h1>
            <a href="{{ route('shop.pos.index') }}" class="btn btn-sm btn-warning text-dark fw-bold">
                <i class="fas fa-cash-register me-1"></i> {{ __('Open POS Counter') }}
            </a>
        </div>
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 text-white-50 small">
            <span>{{ __('Audit offline POS counter sales and completed in-store transaction records.') }}</span>
        </div>
    </div>
</div>

    <div class="card">
        <div class="card-body">

            <div class="cardTitleBox">
                <h5 class="card-title chartTitle">
                    {{ __('Orders Summary') }}
                </h5>
            </div>

            <div class="table-responsive">
                <table class="table table-responsive-lg">
                    <thead>
                        <tr>
                            <th style="min-width: 120px">
                                @include('admin.partials.sortable-header', ['label' => __('Order ID'), 'column' => 'id', 'route' => 'shop.pos.sales', 'sort' => $sort ?? 'id', 'direction' => $direction ?? 'desc'])
                            </th>
                            <th style="min-width: 150px">
                                @include('admin.partials.sortable-header', ['label' => __('Order Date'), 'column' => 'created_at', 'route' => 'shop.pos.sales', 'sort' => $sort ?? 'id', 'direction' => $direction ?? 'desc'])
                            </th>
                            <th style="min-width: 140px">
                                @include('admin.partials.sortable-header', ['label' => __('Customer'), 'column' => 'customer_name', 'route' => 'shop.pos.sales', 'sort' => $sort ?? 'id', 'direction' => $direction ?? 'desc'])
                            </th>
                            <th style="min-width: 140px">
                                @include('admin.partials.sortable-header', ['label' => __('Total Amount'), 'column' => 'payable_amount', 'route' => 'shop.pos.sales', 'sort' => $sort ?? 'id', 'direction' => $direction ?? 'desc'])
                            </th>
                            <th style="min-width: 140px">
                                @include('admin.partials.sortable-header', ['label' => __('Payment Method'), 'column' => 'payment_method', 'route' => 'shop.pos.sales', 'sort' => $sort ?? 'id', 'direction' => $direction ?? 'desc'])
                            </th>
                            <th style="min-width: 120px">
                                @include('admin.partials.sortable-header', ['label' => __('Status'), 'column' => 'order_status', 'route' => 'shop.pos.sales', 'sort' => $sort ?? 'id', 'direction' => $direction ?? 'desc'])
                            </th>
                            <th>{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $order)
                            <tr>
                                <td>
                                    {{ $order->prefix . $order->order_code }}
                                </td>
                                <td>{{ $order->created_at->format('d M Y, h:i A') }}</td>
                                <td>{{ $order->customer?->user?->name }}</td>
                                <td>
                                    {{ showCurrency($order->payable_amount) }}
                                    <br>
                                    <span class="badge rounded-pill text-bg-primary">
                                        {{ __($order->payment_status->value) }}
                                    </span>
                                </td>
                                <td>{{ __($order->payment_method->value) }}</td>
                                <td>{{ __($order->order_status->value) }}</td>
                                <td>
                                    <a href="{{ route('shop.order.show', $order->id) }}" data-bs-toggle="tooltip"
                                        data-bs-placement="top" data-bs-title="{{__('view order details')}}"
                                        class="circleIcon btn-outline-primary">
                                        <img src="{{ asset('assets/icons-admin/eye.svg') }}" alt="view" loading="lazy" />
                                    </a>
                                    <a href="{{ route('shop.pos.invoice', $order->id) }}" target="_blank" data-bs-toggle="tooltip" data-bs-placement="left"
                                        data-bs-title="{{__('Download Invoice')}}" class="circleIcon btn-outline-success">
                                        <img src="{{ asset('assets/icons-admin/download-alt.svg') }}" alt="download" loading="lazy" />
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>

        </div>
    </div>

    <div class="my-3">
        {{ $orders->links() }}
    </div>
@endsection
