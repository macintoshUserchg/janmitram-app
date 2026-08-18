@extends('layouts.app')
@section('header-title', __('Membership & Health Cards'))

@section('content')
    <div class="d-flex align-items-center flex-wrap gap-3 justify-content-between mb-4">
        <div>
            <h4 class="mb-1 fw-bold text-dark">{{ __('Janmitram Privilege & Health Cards') }}</h4>
            <p class="text-muted mb-0 small">
                {{ __('Manage membership cards, assign customer privileges (:pct% POS/Online discount), and print physical verification cards.', ['pct' => $terms['percentage'] ?? 10]) }}
            </p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#createCardModal">
                <i class="bi bi-plus-circle-fill"></i>
                <span>{{ __('Issue New Card') }}</span>
            </button>
        </div>
    </div>

    {{-- KPI Metric Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-12 h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-12 bg-primary-subtle text-primary p-3 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                        <i class="bi bi-credit-card-2-front-fill fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-medium">{{ __('Total Cards Issued') }}</div>
                        <h4 class="fw-bold mb-0 text-dark">{{ number_format($totalCards) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-12 h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-12 bg-success-subtle text-success p-3 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                        <i class="bi bi-check-circle-fill fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-medium">{{ __('Active Valid Cards') }}</div>
                        <h4 class="fw-bold mb-0 text-success">{{ number_format($activeCards) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-12 h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-12 bg-info-subtle text-info p-3 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                        <i class="bi bi-person-badge-fill fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-medium">{{ __('Assigned to Members') }}</div>
                        <h4 class="fw-bold mb-0 text-dark">{{ number_format($assignedCards) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-12 h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-12 bg-warning-subtle text-warning p-3 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                        <i class="bi bi-inboxes-fill fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-medium">{{ __('Unassigned Stock') }}</div>
                        <h4 class="fw-bold mb-0 text-dark">{{ number_format($unassignedCards) }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Filter & Data Table Card --}}
    <div class="card border-0 shadow-sm rounded-12">
        <div class="card-body p-4">

            {{-- Filter & Search Bar --}}
            <form method="GET" action="{{ route('admin.cards.index') }}" class="row g-3 align-items-end mb-4">
                <input type="hidden" name="sort" value="{{ $sort }}">
                <input type="hidden" name="direction" value="{{ $direction }}">

                <div class="col-md-4">
                    <label class="form-label small fw-bold text-muted">{{ __('Search Cards / Holders') }}</label>
                    <div class="position-relative">
                        <input type="text" name="search" class="form-control ps-4"
                               placeholder="{{ __('Search card #, name, phone, email...') }}"
                               value="{{ request('search') }}">
                        <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-2 text-muted small"></i>
                    </div>
                </div>

                <div class="col-md-2 col-sm-6">
                    <label class="form-label small fw-bold text-muted">{{ __('Status') }}</label>
                    <select name="status" class="form-select">
                        <option value="">{{ __('All Statuses') }}</option>
                        <option value="active" @selected(request('status') === 'active')>{{ __('Active') }}</option>
                        <option value="inactive" @selected(request('status') === 'inactive')>{{ __('Inactive') }}</option>
                    </select>
                </div>

                <div class="col-md-3 col-sm-6">
                    <label class="form-label small fw-bold text-muted">{{ __('Assignment') }}</label>
                    <select name="assignment" class="form-select">
                        <option value="">{{ __('All Cards') }}</option>
                        <option value="assigned" @selected(request('assignment') === 'assigned')>{{ __('Assigned to Customer') }}</option>
                        <option value="unassigned" @selected(request('assignment') === 'unassigned')>{{ __('Unassigned (Stock)') }}</option>
                    </select>
                </div>

                <div class="col-md-1 col-sm-4">
                    <label class="form-label small fw-bold text-muted">{{ __('Show') }}</label>
                    <select name="per_page" class="form-select">
                        @foreach ([10, 15, 20, 50, 100] as $size)
                            <option value="{{ $size }}" @selected((int) request('per_page', 15) === $size)>{{ $size }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2 col-sm-8 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">
                        <i class="bi bi-funnel-fill me-1"></i> {{ __('Filter') }}
                    </button>
                    @if (request()->hasAny(['search', 'status', 'assignment', 'per_page']))
                        <a href="{{ route('admin.cards.index') }}" class="btn btn-outline-secondary" title="{{ __('Reset Filters') }}">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    @endif
                </div>
            </form>

            {{-- Table --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle border-left-right mb-0">
                    <thead class="table-light text-nowrap">
                        <tr>
                            <th class="ps-3" style="width: 80px;">
                                @include('admin.partials.sortable-header', [
                                    'label' => __('ID'),
                                    'column' => 'id',
                                    'route' => 'admin.cards.index',
                                    'sort' => $sort,
                                    'direction' => $direction,
                                ])
                            </th>
                            <th>
                                @include('admin.partials.sortable-header', [
                                    'label' => __('Card Number'),
                                    'column' => 'card_number',
                                    'route' => 'admin.cards.index',
                                    'sort' => $sort,
                                    'direction' => $direction,
                                ])
                            </th>
                            <th>
                                @include('admin.partials.sortable-header', [
                                    'label' => __('Card Holder / Member'),
                                    'column' => 'customer_name',
                                    'route' => 'admin.cards.index',
                                    'sort' => $sort,
                                    'direction' => $direction,
                                ])
                            </th>
                            <th>{{ __('Privilege Benefits') }}</th>
                            <th>
                                @include('admin.partials.sortable-header', [
                                    'label' => __('Issued Date'),
                                    'column' => 'created_at',
                                    'route' => 'admin.cards.index',
                                    'sort' => $sort,
                                    'direction' => $direction,
                                ])
                            </th>
                            <th class="text-center">
                                @include('admin.partials.sortable-header', [
                                    'label' => __('Status'),
                                    'column' => 'is_active',
                                    'route' => 'admin.cards.index',
                                    'sort' => $sort,
                                    'direction' => $direction,
                                ])
                            </th>
                            <th class="text-center" style="min-width: 150px;">
                                <i class="bi bi-file-earmark-pdf-fill text-danger me-1"></i> {{ __('Download Card') }}
                            </th>
                            <th class="text-end pe-3">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($cards as $card)
                            <tr>
                                <td class="ps-3 fw-bold text-muted">
                                    #{{ $card->id }}
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-8 bg-dark text-warning px-2 py-1 font-monospace fw-bold small shadow-sm">
                                            <i class="bi bi-credit-card me-1"></i> {{ chunk_split($card->card_number, 4, ' ') }}
                                        </div>
                                        <button type="button" class="btn btn-sm btn-link text-muted p-0" title="{{ __('Copy Card Number') }}"
                                                onclick="navigator.clipboard.writeText('{{ $card->card_number }}'); toastr?.success('Copied card number!');">
                                            <i class="bi bi-clipboard"></i>
                                        </button>
                                    </div>
                                </td>
                                <td>
                                    @if ($card->customer)
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="avatar-circle bg-primary-subtle text-primary fw-bold d-flex align-items-center justify-content-center rounded-circle" style="width: 34px; height: 34px; font-size: 13px;">
                                                {{ strtoupper(substr($card->customer->user?->name ?? 'C', 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark">{{ $card->customer->user?->name ?? '#' . $card->customer->id }}</div>
                                                <div class="text-muted small">
                                                    <i class="bi bi-telephone me-1"></i>{{ $card->customer->user?->phone ?? 'No phone' }}
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2.5 py-1.5 rounded-pill">
                                            <i class="bi bi-inbox me-1"></i> {{ __('Unassigned (Available Stock)') }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1.5 rounded-8">
                                        <i class="bi bi-tag-fill me-1"></i> {{ $terms['percentage'] ?? 10 }}% {{ __('Off') }}
                                        <small class="text-muted">({{ showCurrency($terms['min_order_amount'] ?? 500) }}+)</small>
                                    </span>
                                </td>
                                <td class="text-muted small text-nowrap">
                                    {{ $card->created_at?->format('d M Y') }}
                                    <div class="text-muted" style="font-size: 11px;">{{ $card->created_at?->diffForHumans() }}</div>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.cards.toggle', $card) }}" class="text-decoration-none" title="{{ __('Click to toggle active state') }}">
                                        @if ($card->is_active)
                                            <span class="badge bg-success px-2.5 py-1.5 rounded-pill">
                                                <i class="bi bi-check-circle-fill me-1"></i> {{ __('Active') }}
                                            </span>
                                        @else
                                            <span class="badge bg-danger px-2.5 py-1.5 rounded-pill">
                                                <i class="bi bi-x-circle-fill me-1"></i> {{ __('Inactive') }}
                                            </span>
                                        @endif
                                    </a>
                                </td>
                                <td class="text-center text-nowrap">
                                    <div class="btn-group shadow-sm" role="group">
                                        {{-- Direct Attachment Download --}}
                                        <a href="{{ route('admin.cards.download', $card) }}"
                                           class="btn btn-sm btn-outline-danger d-inline-flex align-items-center gap-1 font-semibold"
                                           title="{{ __('Download Printable PDF Card (Front & Back with QR)') }}">
                                            <i class="bi bi-file-earmark-pdf-fill"></i>
                                            <span>{{ __('Download') }}</span>
                                        </a>
                                        {{-- Inline Preview / Print Tab --}}
                                        <a href="{{ route('admin.cards.download', [$card, 'preview' => 1]) }}"
                                           target="_blank"
                                           class="btn btn-sm btn-outline-secondary"
                                           title="{{ __('Open Printable Preview in New Tab') }}">
                                            <i class="bi bi-printer"></i>
                                        </a>
                                    </div>
                                </td>
                                <td class="text-end pe-3 text-nowrap">
                                    <a href="{{ route('admin.cards.show', $card) }}"
                                       class="btn btn-sm btn-outline-primary me-1"
                                       title="{{ __('View Usage History & Orders') }}">
                                        <i class="bi bi-eye-fill"></i>
                                    </a>
                                    <a href="{{ route('admin.cards.toggle', $card) }}"
                                       class="btn btn-sm btn-outline-{{ $card->is_active ? 'warning' : 'success' }}"
                                       title="{{ $card->is_active ? __('Deactivate Card') : __('Activate Card') }}">
                                        <i class="bi bi-power"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <div class="mb-3">
                                        <i class="bi bi-credit-card-2-front text-muted" style="font-size: 42px;"></i>
                                    </div>
                                    <h6 class="fw-bold text-dark">{{ __('No membership cards found') }}</h6>
                                    <p class="small text-muted mb-3">{{ __('Try adjusting your search criteria or issue a new card.') }}</p>
                                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#createCardModal">
                                        <i class="bi bi-plus-circle me-1"></i> {{ __('Issue Card Now') }}
                                    </button>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination & Count Info --}}
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mt-4 pt-3 border-top">
                <div class="text-muted small">
                    {{ __('Showing') }} <span class="fw-bold text-dark">{{ $cards->firstItem() ?? 0 }}</span>
                    {{ __('to') }} <span class="fw-bold text-dark">{{ $cards->lastItem() ?? 0 }}</span>
                    {{ __('of') }} <span class="fw-bold text-dark">{{ $cards->total() }}</span> {{ __('cards') }}
                </div>
                <div>
                    {{ $cards->links() }}
                </div>
            </div>

        </div>
    </div>

    {{-- Create / Issue Card Modal --}}
    <div class="modal fade" id="createCardModal" tabindex="-1" aria-labelledby="createCardModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-12">
                <div class="modal-header border-bottom pb-3">
                    <h5 class="modal-title fw-bold text-dark" id="createCardModalLabel">
                        <i class="bi bi-credit-card-2-front-fill text-primary me-2"></i>{{ __('Issue Privilege Card') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.cards.store') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-dark">{{ __('Assign to Customer') }} <span class="text-muted">({{ __('Optional') }})</span></label>
                            <select name="customer_id" class="form-select select2" style="width: 100%;">
                                <option value="">— {{ __('Unassigned (Add to Ready Stock)') }} —</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}">
                                        {{ $customer->user?->name ?? '#' . $customer->id }}
                                        ({{ $customer->user?->phone ?? 'No Phone' }})
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text small text-muted">
                                {{ __('Assigning to an existing customer automatically links their profile and deactivates their older cards.') }}
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-dark">{{ __('Quantity to Generate') }}</label>
                            <input type="number" name="quantity" class="form-control" value="1" min="1" max="50">
                            <div class="form-text small text-muted">
                                {{ __('Unique 8-digit card numbers with QR codes will be generated automatically.') }}
                            </div>
                        </div>

                        <div class="p-3 bg-light rounded-8 border">
                            <div class="d-flex align-items-center gap-2 text-dark fw-semibold small mb-1">
                                <i class="bi bi-shield-check text-success"></i> {{ __('Privilege Discount Settings') }}
                            </div>
                            <div class="text-muted small">
                                • {{ $terms['percentage'] ?? 10 }}% {{ __('flat discount applies on orders of') }} {{ showCurrency($terms['min_order_amount'] ?? 500) }}+.<br>
                                • {{ __('Instant printable PDF card with QR Code is generated upon issuance.') }}
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top pt-3">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-primary d-flex align-items-center gap-2">
                            <i class="bi bi-check-lg"></i> {{ __('Create & Issue Card') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
