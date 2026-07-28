@extends('layouts.app')

@section('header-title', __('Orders'))

@section('content')
<!-- Integrated 2-Line Compact Header Banner Card -->
<div class="card border-0 shadow-sm rounded-12 mb-4 bg-gradient bg-primary text-white">
    <div class="card-body p-3">
        <div class="d-flex justify-content-between align-items-center mb-1">
            <h1 class="h4 mb-0 text-white fw-bold"><i class="fas fa-undo-alt me-2 text-warning"></i>{{ __('Refund & Return Management') }}</h1>
            <span class="badge bg-light text-primary fw-bold">{{ __('Returns Audit') }}</span>
        </div>
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 text-white-50 small">
            <span>{{ __('Process customer product return and refund requests.') }}</span>
        </div>
    </div>
</div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">

                <table class="table border-left-right table-responsive-lg">
                    <thead>
                        <tr>
                            <th style="min-width: 85px">{{ __('Order ID') }}</th>
                            <th>{{ __('Return Date') }}</th>
                            <th>{{ __('Customer') }}</th>
                            @if ($businessModel == 'multi')
                                <th>{{ __('Shop') }}</th>
                            @endif
                            <th>{{ __('Amount') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Payment Status') }}</th>
                            <th>{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($returnOrder as $order)
                            <tr>
                                <td class="w-auto">{{ $order->order->prefix . $order->order->order_code }}</td>
                                <td class="w-min">{{ $order->created_at->format('d M Y, h:i A') }}</td>
                                <td class="w-min">{{ $order->customer?->user?->name }}</td>

                                @if ($businessModel == 'multi')
                                    <td class="w-min">
                                        {{ $order->shop?->name }}
                                    </td>
                                @endif
                                <td class="w-min">
                                    {{ showCurrency($order->amount) }}
                                </td>
                                <td class="w-min">
                                    {{ $order->status }}
                                </td>
                                <td><button class="badge rounded-pill text-bg-{{ $order->payment_status ? 'success' : 'danger' }}">{{ $order->payment_status ? 'Paid' : 'Unpaid' }}</button></td>
                                <td class="w-min">
                                    @hasPermission('admin.returnOrder.show')
                                        <a href="{{ route('shop.returnOrder.show', $order->id) }}" data-bs-toggle="tooltip"
                                            data-bs-placement="top" data-bs-title="{{ __('view details') }}"
                                            class="circleIcon svg-bg">
                                            <img src="{{ asset('assets/icons-admin/eye.svg') }}" alt="icon"
                                                loading="lazy" />
                                        </a>
                                    @endhasPermission
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="100%" class="text-center">
                                    {{ __('No order found') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

            </div>

        </div>
    </div>

    <div class="my-3">
        {{ $returnOrder->links() }}
    </div>

@endsection
