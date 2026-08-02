@extends('layouts.app')

@section('header-title', __('Orders'))

@section('content')
<!-- Integrated 2-Line Compact Header Banner Card -->
<div class="card border-0 shadow-sm rounded-12 mb-4 bg-gradient bg-primary text-white">
    <div class="card-body p-3">
        <div class="d-flex justify-content-between align-items-center mb-1">
            <h1 class="h4 mb-0 text-white fw-bold"><i class="fas fa-shopping-bag me-2 text-warning"></i>{{ __('All Orders') }}</h1>
            <span class="badge bg-light text-primary fw-bold">{{ __('Shop Order Management') }}</span>
        </div>
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 text-white-50 small">
            <span>{{ __('View and process customer orders placed across your shop storefront.') }}</span>
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
                            <th>{{ __('Order ID') }}</th>
                            <th>{{ __('Order Date') }}</th>
                            <th>{{ __('Customer') }}</th>
                            <th>{{ __('Total Amount') }}</th>
                            <th>{{ __('Payment Method') }}</th>
                            <th>{{ __('Status') }}</th>
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
                                <td>{{ $order->customer?->user?->name ?? ($order->pos_order ? __('Walk-in Customer / POS') : __('N/A')) }}</td>
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
                                    @hasPermission('shop.order.show')
                                    <button type="button"
                                        data-url="{{ route('shop.order.show', $order->id) }}?modal=1"
                                        title="{{__('view order details')}}"
                                        class="circleIcon btn-outline-primary svg-bg border-0 btn-view-order-modal">
                                        <img src="{{ asset('assets/icons-admin/eye.svg') }}" alt="icon" loading="lazy" />
                                    </button>
                                    @endhasPermission
                                    <a href="{{ route('shop.download-invoice', $order->id) }}" data-bs-toggle="tooltip" data-bs-placement="left"
                                        data-bs-title="{{__('Download Invoice')}}" class="circleIcon btn-outline-secondary">
                                        <img src="{{ asset('assets/icons-admin/download-alt.svg') }}" alt="icon" loading="lazy" />
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

    <!-- Order Details AJAX Modal -->
    <div class="modal fade" id="orderDetailsModal" tabindex="-1" aria-labelledby="orderDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content" id="orderDetailsModalContent">
                <div class="modal-body text-center p-5">
                    <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3 text-muted fw-semibold">{{ __('Loading Order Details...') }}</p>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    $(document).on('click', '.btn-view-order-modal', function(e) {
        e.preventDefault();
        let url = $(this).data('url');
        let modalContent = $('#orderDetailsModalContent');

        // Reset modal body to loading spinner immediately to prevent showing old data
        modalContent.html(`
            <div class="modal-body text-center p-5">
                <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-3 text-muted fw-semibold">{{ __('Loading Order Details...') }}</p>
            </div>
        `);

        // Show Bootstrap Modal
        $('#orderDetailsModal').modal('show');

        // Fetch fresh order details for this specific order
        $.ajax({
            url: url,
            type: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                modalContent.html(response);
            },
            error: function() {
                modalContent.html(`
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">{{ __('Error') }}</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center p-4">
                        <p class="text-danger fw-bold mb-0">{{ __('Unable to load order details. Please try again.') }}</p>
                    </div>
                `);
            }
        });
    });
</script>
@endpush
