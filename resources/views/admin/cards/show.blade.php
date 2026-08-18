@extends('layouts.app')
@section('header-title', __('Card Details') . ' - #' . $card->card_number)

@section('content')
    <div class="d-flex align-items-center flex-wrap gap-3 justify-content-between mb-4">
        <div>
            <h4 class="mb-1 fw-bold text-dark">{{ __('Card Details') }}</h4>
            <div class="text-muted small">
                {{ __('Viewing card #') }}<strong>{{ $card->card_number }}</strong> • {{ $card->is_active ? __('Active Status') : __('Inactive Status') }}
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.cards.download', $card) }}" class="btn btn-outline-danger d-flex align-items-center gap-2 shadow-sm">
                <i class="bi bi-file-earmark-pdf-fill"></i>
                <span>{{ __('Download PDF Card') }}</span>
            </a>
            <a href="{{ route('admin.cards.download', [$card, 'preview' => 1]) }}" target="_blank" class="btn btn-outline-secondary d-flex align-items-center gap-2">
                <i class="bi bi-printer"></i>
                <span>{{ __('Print / Preview') }}</span>
            </a>
            <a href="{{ route('admin.cards.index') }}" class="btn btn-primary d-flex align-items-center gap-2">
                <i class="bi bi-arrow-left"></i>
                <span>{{ __('Back to Cards') }}</span>
            </a>
        </div>
    </div>

    <div class="row g-4">
        {{-- Card Info & Miniature Preview --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-12 mb-4">
                <div class="card-body p-4">
                    {{-- Mini Physical Card Graphic --}}
                    <div class="p-3 rounded-12 text-white shadow mb-4" style="background: linear-gradient(135deg, #0F2D2A 0%, #064E3B 100%); border: 1.5px solid #10B981;">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <div class="fw-bold text-white small text-uppercase letter-spacing-1">JANMITRAM</div>
                                <div class="text-emerald-300" style="font-size: 8px; color: #34D399;">PRIVILEGE HEALTH CARD</div>
                            </div>
                            <span class="badge bg-warning text-dark small fw-bold">{{ $terms['percentage'] ?? 10 }}% OFF</span>
                        </div>

                        <div class="my-3 font-monospace fw-bold fs-5 tracking-widest text-light">
                            {{ chunk_split($card->card_number, 4, ' ') }}
                        </div>

                        <div class="d-flex justify-content-between align-items-end pt-2 border-top border-emerald-800" style="border-color: rgba(52, 211, 153, 0.2) !important;">
                            <div>
                                <div class="text-muted text-uppercase" style="font-size: 7px; color: #94A3B8 !important;">CARD HOLDER</div>
                                <div class="fw-bold text-white small">{{ Str::limit($card->customer?->user?->name ?? 'Valued Member', 18) }}</div>
                            </div>
                            <div class="text-end">
                                <div class="text-muted text-uppercase" style="font-size: 7px; color: #94A3B8 !important;">ISSUED</div>
                                <div class="fw-bold text-light small">{{ $card->created_at?->format('M Y') }}</div>
                            </div>
                        </div>
                    </div>

                    {{-- Metadata list --}}
                    <h6 class="fw-bold text-dark mb-3 border-bottom pb-2">{{ __('Card Information') }}</h6>
                    <ul class="list-unstyled mb-0">
                        <li class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted small">{{ __('Card Number') }}</span>
                            <span class="font-monospace fw-bold text-dark">{{ $card->card_number }}</span>
                        </li>
                        <li class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted small">{{ __('Card Holder') }}</span>
                            <span class="fw-bold text-dark">
                                @if ($card->customer)
                                    {{ $card->customer->user?->name ?? '#' . $card->customer->id }}
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary">{{ __('Unassigned') }}</span>
                                @endif
                            </span>
                        </li>
                        @if ($card->customer?->user?->phone)
                            <li class="d-flex justify-content-between py-2 border-bottom">
                                <span class="text-muted small">{{ __('Contact Phone') }}</span>
                                <span class="fw-semibold text-dark">{{ $card->customer->user->phone }}</span>
                            </li>
                        @endif
                        <li class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted small">{{ __('Card Status') }}</span>
                            <span>
                                @if ($card->is_active)
                                    <span class="badge bg-success px-2 py-1">{{ __('Active') }}</span>
                                @else
                                    <span class="badge bg-danger px-2 py-1">{{ __('Inactive') }}</span>
                                @endif
                            </span>
                        </li>
                        <li class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted small">{{ __('Privilege Discount') }}</span>
                            <span class="text-success fw-bold">{{ $terms['percentage'] ?? 10 }}% {{ __('Off') }}</span>
                        </li>
                        <li class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted small">{{ __('Min Order Eligibility') }}</span>
                            <span class="fw-bold text-dark">{{ showCurrency($terms['min_order_amount'] ?? 500) }}</span>
                        </li>
                        <li class="d-flex justify-content-between py-2">
                            <span class="text-muted small">{{ __('Issued At') }}</span>
                            <span class="text-dark small">{{ $card->created_at?->format('d M Y h:i a') }}</span>
                        </li>
                    </ul>

                    <div class="d-grid gap-2 mt-4">
                        <a href="{{ route('admin.cards.toggle', $card) }}" class="btn btn-outline-{{ $card->is_active ? 'warning' : 'success' }}">
                            <i class="bi bi-power me-1"></i> {{ $card->is_active ? __('Deactivate Card') : __('Activate Card') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Order History / Usage List --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-12">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h5 class="fw-bold text-dark mb-0">{{ __('Card Usage History') }}</h5>
                        <span class="badge bg-primary-subtle text-primary">{{ $orders->total() }} {{ __('Orders Redeemed') }}</span>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle border-left-right mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">{{ __('Order Code') }}</th>
                                    <th>{{ __('Order Total') }}</th>
                                    <th>{{ __('Card Discount Saved') }}</th>
                                    <th>{{ __('Redeemed Date') }}</th>
                                    <th class="text-end pe-3">{{ __('Order Status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($orders as $order)
                                    <tr>
                                        <td class="ps-3 fw-bold">
                                            <a href="{{ route('admin.order.show', $order) }}" class="text-primary text-decoration-none">
                                                #{{ $order->order_code }}
                                            </a>
                                        </td>
                                        <td>{{ showCurrency($order->total_amount) }}</td>
                                        <td class="text-success fw-bold">
                                            <i class="bi bi-tag-fill me-1"></i>-{{ showCurrency($order->card_discount) }}
                                        </td>
                                        <td class="text-muted small">
                                            {{ $order->created_at?->format('d M Y h:i a') }}
                                        </td>
                                        <td class="text-end pe-3">
                                            <span class="badge bg-secondary-subtle text-secondary px-2.5 py-1">
                                                {{ $order->order_status?->value ?? 'Processed' }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">
                                            <i class="bi bi-receipt text-muted fs-3 d-block mb-2"></i>
                                            {{ __('No orders have used this card discount yet.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $orders->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
