@extends('layouts.app')

@section('header-title', __('Payout Management'))
@section('header-subtitle', __('Monthly MLM payouts for shop owners.'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h1 class="h3 mb-0 text-gray-800 fw-bold">{{ __('Monthly Payouts') }}</h1>
        <p class="text-muted small mb-0">{{ __('Phase 1 (own sales) plus Phase 2 (group network) income per shop.') }}</p>
    </div>
    @hasPermission('admin.payout.run')
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#runPayoutModal">
                <i class="fas fa-play me-1"></i> {{ __('Run Payout') }}
            </button>
        </div>
    @endhasPermission
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4">{{ session('error') }}</div>
@endif

<!-- Month / Year Filter -->
<div class="card border-0 shadow-sm rounded-12 mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('admin.payout.index') }}" class="row g-2 align-items-end">
            <div class="col-auto">
                <label class="form-label small text-muted mb-1">{{ __('Month') }}</label>
                <select name="month" class="form-select form-select-sm">
                    <option value="">{{ __('All') }}</option>
                    @foreach($months as $m)
                        <option value="{{ $m }}" @selected((string) $month === (string) $m)>{{ $m }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <label class="form-label small text-muted mb-1">{{ __('Year') }}</label>
                <select name="year" class="form-select form-select-sm">
                    <option value="">{{ __('All') }}</option>
                    @for($y = now()->year; $y >= 2024; $y--)
                        <option value="{{ $y }}" @selected((string) $year === (string) $y)>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-outline-primary btn-sm shadow-sm">
                    <i class="fas fa-filter me-1"></i> {{ __('Apply') }}
                </button>
                <a href="{{ route('admin.payout.index') }}" class="btn btn-outline-secondary btn-sm shadow-sm">
                    {{ __('Reset') }}
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-12">
    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0 fw-bold">{{ __('Payout History') }}</h5>
        <span class="badge bg-light text-dark border">{{ $payouts->total() }} {{ __('Total') }}</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">{{ __('Month') }}</th>
                        <th>{{ __('Shop') }}</th>
                        <th>{{ __('Owner') }}</th>
                        <th class="text-end">{{ __('Personal Sales') }}</th>
                        <th class="text-end">{{ __('Group Sales') }}</th>
                        <th class="text-center">{{ __('Group Size') }}</th>
                        <th class="text-center">{{ __('Level') }}</th>
                        <th class="text-end">{{ __('Phase 1') }}</th>
                        <th class="text-end">{{ __('Phase 2') }}</th>
                        <th class="text-end pe-4">{{ __('Total Payout') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payouts as $payout)
                        <tr>
                            <td class="ps-4">
                                <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill">
                                    {{ sprintf('%04d-%02d', $payout->year, $payout->month) }}
                                </span>
                            </td>
                            <td>
                                <div class="fw-bold text-dark fs-6">{{ $payout->shop?->name ?? '#' . $payout->shop_id }}</div>
                            </td>
                            <td>
                                <div class="text-muted small">{{ $payout->shop?->user?->name ?? '—' }}</div>
                            </td>
                            <td class="text-end">{{ number_format((float) $payout->personal_sales, 2) }}</td>
                            <td class="text-end">{{ number_format((float) $payout->group_sales, 2) }}</td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark border px-3 py-2 rounded-pill">
                                    {{ $payout->group_size }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($payout->level !== null)
                                    <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill fw-semibold">
                                        L{{ $payout->level }}
                                    </span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-end">{{ number_format((float) $payout->phase1_amount, 2) }}</td>
                            <td class="text-end">{{ number_format((float) $payout->phase2_amount, 2) }}</td>
                            <td class="text-end pe-4 fw-bold text-success">{{ number_format((float) $payout->total_payout, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-5 text-muted">
                                <i class="fas fa-hand-holding-usd fs-1 mb-3 d-block text-secondary"></i>
                                {{ __('No payouts found yet. Click "Run Payout" to generate one.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($payouts->hasPages())
        <div class="card-footer bg-white border-top py-3">
            {{ $payouts->links() }}
        </div>
    @endif
</div>

@hasPermission('admin.payout.run')
<!-- Run Payout Modal -->
<div class="modal fade" id="runPayoutModal" tabindex="-1" aria-labelledby="runPayoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-12">
            <form method="POST" action="{{ route('admin.payout.run') }}">
                @csrf
                <div class="modal-header border-bottom bg-white py-3">
                    <h5 class="modal-title fw-bold" id="runPayoutModalLabel">{{ __('Run Monthly Payout') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small">{{ __('Credits each active shop for the selected month. Already-paid shops are skipped.') }}</p>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Month') }} <span class="text-danger">*</span></label>
                            <select name="month" class="form-select" required>
                                @foreach($months as $m)
                                    <option value="{{ $m }}" @selected((string) $month === (string) $m)>{{ $m }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Year') }} <span class="text-danger">*</span></label>
                            <select name="year" class="form-select" required>
                                @for($y = now()->year; $y >= 2024; $y--)
                                    <option value="{{ $y }}" @selected((string) $year === (string) $y)>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top bg-white py-3">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary shadow-sm">
                        <i class="fas fa-play me-1"></i> {{ __('Run Payout') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endhasPermission
@endsection
