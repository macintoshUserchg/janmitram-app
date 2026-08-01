@extends('layouts.app')

@section('header-title', __('My Payouts & Earnings'))
@section('header-subtitle', __('Track your monthly MLM commissions, downline performance, and payout history.'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1 text-dark">{{ __('My Payouts & Earnings') }}</h4>
        <p class="text-muted small mb-0">{{ __('Detailed ledger of your monthly commissions, Phase 1 direct sales, and Phase 2 downline network bonuses.') }}</p>
    </div>
    <div class="d-flex gap-2">
        @hasPermission('shop.payout.network')
            <a href="{{ route('shop.payout.network') }}" class="btn btn-primary shadow-sm">
                <i class="fas fa-sitemap me-1"></i> {{ __('My Downline Network') }}
            </a>
        @endhasPermission
    </div>
</div>

<!-- Overview Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-12 bg-white h-100">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="p-3 bg-success-subtle text-success rounded-12">
                    <i class="fas fa-wallet fs-3"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold">{{ __('Total Earnings Credited') }}</div>
                    <h3 class="fw-bold mb-0 text-success">₹{{ number_format($lifetimeTotal, 2) }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-12 bg-white h-100">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="p-3 bg-primary-subtle text-primary rounded-12">
                    <i class="fas fa-calendar-check fs-3"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold">{{ __('Period') }} ({{ sprintf('%04d-%02d', $year, $month) }})</div>
                    <h3 class="fw-bold mb-0 text-primary">₹{{ number_format($currentTree['total_payout'] ?? 0, 2) }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-12 bg-white h-100">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="p-3 bg-info-subtle text-info rounded-12">
                    <i class="fas fa-layer-group fs-3"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold">{{ __('Current Achievement Tier') }}</div>
                    <div class="mt-1">
                        @if(isset($currentTree['level']) && $currentTree['level'] !== null)
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-2 fs-6">
                                Level {{ $currentTree['level'] }}
                            </span>
                        @else
                            <span class="badge bg-light text-secondary border rounded-pill px-3 py-2 fs-6">—</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-12 bg-white h-100">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="p-3 bg-warning-subtle text-warning rounded-12">
                    <i class="fas fa-users fs-3"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold">{{ __('Group Sales & Size') }}</div>
                    <h5 class="fw-bold mb-0 text-dark">₹{{ number_format($currentTree['group_sales'] ?? 0, 2) }}</h5>
                    <small class="text-muted">({{ $currentTree['group_size'] ?? 1 }} {{ __('members') }})</small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Current Month Projected / Credited Breakdown Card -->
<div class="card border-0 shadow-sm rounded-12 mb-4">
    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h5 class="card-title mb-0 fw-bold">{{ __('Current Period Earnings Breakdown') }} ({{ sprintf('%04d-%02d', $year, $month) }})</h5>
            <small class="text-muted">{{ __('Personal Delivered sales and group downline commission for this billing period.') }}</small>
        </div>
        <span class="badge {{ $isPaid ? 'bg-success text-white' : 'bg-warning text-dark' }} px-3 py-2 rounded-pill">
            {{ $isPaid ? __('Credited Snapshot') : __('Live Monthly Projection') }}
        </span>
    </div>
    <div class="card-body p-4">
        <div class="row g-4">
            <div class="col-md-3">
                <div class="p-3 bg-light rounded-12 border">
                    <div class="text-muted small fw-semibold mb-1">{{ __('Personal Sales (Delivered)') }}</div>
                    <h4 class="fw-bold text-dark mb-0">₹{{ number_format($currentTree['personal_sales'] ?? 0, 2) }}</h4>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-3 bg-light rounded-12 border">
                    <div class="text-muted small fw-semibold mb-1">{{ __('Phase 1 Commission (10%)') }}</div>
                    <h4 class="fw-bold text-primary mb-0">₹{{ number_format($currentTree['phase1_amount'] ?? 0, 2) }}</h4>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-3 bg-light rounded-12 border">
                    <div class="text-muted small fw-semibold mb-1">{{ __('Phase 2 Group Bonus') }}</div>
                    <h4 class="fw-bold text-info mb-0">₹{{ number_format($currentTree['phase2_amount'] ?? 0, 2) }}</h4>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-3 bg-success-subtle rounded-12 border border-success-subtle">
                    <div class="text-success small fw-bold mb-1">{{ __('Total Period Payout') }}</div>
                    <h4 class="fw-bold text-success mb-0">₹{{ number_format($currentTree['total_payout'] ?? 0, 2) }}</h4>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Historical Payout Ledger -->
<div class="card border-0 shadow-sm rounded-12 mb-4">
    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0 fw-bold">{{ __('Historical Payout Ledger') }}</h5>
        <span class="badge bg-light text-dark border">{{ $payouts->total() }} {{ __('Records') }}</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">{{ __('Period') }}</th>
                        <th class="text-end">{{ __('Personal Sales') }}</th>
                        <th class="text-end">{{ __('Group Sales') }}</th>
                        <th class="text-center">{{ __('Group Size') }}</th>
                        <th class="text-center">{{ __('Tier Level') }}</th>
                        <th class="text-end">{{ __('Phase 1') }}</th>
                        <th class="text-end">{{ __('Phase 2') }}</th>
                        <th class="text-end pe-4">{{ __('Total Credited') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payouts as $payout)
                        <tr>
                            <td class="ps-4">
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill fw-semibold">
                                    {{ sprintf('%04d-%02d', $payout->year, $payout->month) }}
                                </span>
                            </td>
                            <td class="text-end">₹{{ number_format((float) $payout->personal_sales, 2) }}</td>
                            <td class="text-end">₹{{ number_format((float) $payout->group_sales, 2) }}</td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark border px-3 py-2 rounded-pill">
                                    {{ $payout->group_size }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($payout->level !== null)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill fw-semibold">
                                        Level {{ $payout->level }}
                                    </span>
                                @else
                                    <span class="badge bg-light text-secondary border rounded-pill">—</span>
                                @endif
                            </td>
                            <td class="text-end text-primary fw-semibold">₹{{ number_format((float) $payout->phase1_amount, 2) }}</td>
                            <td class="text-end text-info fw-semibold">₹{{ number_format((float) $payout->phase2_amount, 2) }}</td>
                            <td class="text-end pe-4 fw-bold text-success fs-6">₹{{ number_format((float) $payout->total_payout, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="fas fa-coins fs-1 mb-3 d-block text-secondary"></i>
                                {{ __('No historical payout records credited yet.') }}
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
@endsection
