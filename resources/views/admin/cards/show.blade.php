@extends('layouts.app')
@section('header-title', __('Card Details'))

@section('content')
    <div class="d-flex align-items-center flex-wrap gap-3 justify-content-between px-3">
        <h4>{{ __('Card Details') }}</h4>
        <a href="{{ route('admin.cards.index') }}" class="btn btn-outline-secondary">
            {{ __('Back to Cards') }}
        </a>
    </div>

    <div class="mt-4">
        <div class="row">
            <div class="col-lg-4">
                <div class="card rounded-12">
                    <div class="card-body">
                        <h5 class="fw-bold">{{ $card->card_number }}</h5>
                        <ul class="list-unstyled mb-0 mt-3">
                            <li class="mb-2">
                                <strong>{{ __('Customer') }}:</strong>
                                @if ($card->customer)
                                    {{ $card->customer->user?->name ?? '#' . $card->customer->id }}
                                    <small class="text-muted">({{ $card->customer->user?->phone ?? '' }})</small>
                                @else
                                    <span class="text-muted">{{ __('Unassigned') }}</span>
                                @endif
                            </li>
                            <li class="mb-2">
                                <strong>{{ __('Status') }}:</strong>
                                <span class="badge bg-{{ $card->is_active ? 'success' : 'danger' }}">
                                    {{ $card->is_active ? __('Active') : __('Inactive') }}
                                </span>
                            </li>
                            <li class="mb-2">
                                <strong>{{ __('Created') }}:</strong>
                                {{ $card->created_at?->format('d M Y h:i a') }}
                            </li>
                            <li class="mb-2">
                                <strong>{{ __('Discount') }}:</strong>
                                {{ $terms['percentage'] }}% {{ __('off orders of') }}
                                {{ showCurrency($terms['min_order_amount']) }}
                                {{ __('or more') }}
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card rounded-12">
                    <div class="card-body">
                        <h5>{{ __('Card Usage') }}</h5>
                        <div class="table-responsive">
                            <table class="table border-left-right">
                                <thead>
                                    <tr>
                                        <th>{{ __('Order') }}</th>
                                        <th>{{ __('Order Total') }}</th>
                                        <th>{{ __('Card Discount') }}</th>
                                        <th>{{ __('Date') }}</th>
                                        <th>{{ __('Status') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($orders as $order)
                                        <tr>
                                            <td>{{ $order->order_code }}</td>
                                            <td>{{ showCurrency($order->total_amount) }}</td>
                                            <td class="text-success">-{{ showCurrency($order->card_discount) }}</td>
                                            <td>{{ $order->created_at?->format('d M Y h:i a') }}</td>
                                            <td>
                                                <span class="badge bg-secondary">{{ $order->order_status?->value }}</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">{{ __('No orders used this card yet') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{ $orders->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
