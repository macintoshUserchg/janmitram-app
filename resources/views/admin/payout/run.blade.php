@extends('layouts.app')

@section('header-title', __('Payout Management'))
@section('header-subtitle', __('Review the month, then confirm the payout run.'))

@push('styles')
@include('admin.payout.partials._tree_styles')
<style>
.step-card {
    border: 1px solid #e2e8f0;
    border-radius: 0.75rem;
    padding: 1.25rem;
    background: #ffffff;
    height: 100%;
}
.step-num {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 2rem;
    height: 2rem;
    border-radius: 9999px;
    background: #eff6ff;
    color: #1d4ed8;
    font-weight: 700;
    font-size: 0.9rem;
}
</style>
@endpush

@section('content')
{{-- Sub-Navigation Hub --}}
@include('admin.payout.partials._nav', ['year' => $year, 'month' => $month])

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4">{!! nl2br(e(session('error'))) !!}</div>
@endif

<!-- Metric KPI Summary Bar -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-12 bg-white h-100">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="p-3 bg-primary-subtle text-primary rounded-12">
                    <i class="fas fa-calendar-alt fs-3"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold">{{ __('Target Period') }}</div>
                    <h3 class="fw-bold mb-0 text-dark">{{ sprintf('%04d-%02d', $year, $month) }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-12 bg-white h-100">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="p-3 bg-info-subtle text-info rounded-12">
                    <i class="fas fa-sitemap fs-3"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold">{{ __('Active Root Networks') }}</div>
                    <h3 class="fw-bold mb-0 text-info">{{ count($nodes) }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-12 bg-white h-100">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="p-3 bg-secondary-subtle text-secondary rounded-12">
                    <i class="fas fa-chart-line fs-3"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold">{{ __('Projected Group Sales') }}</div>
                    <h3 class="fw-bold mb-0 text-dark">₹{{ number_format(collect($nodes)->sum('group_sales'), 2) }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-12 bg-white h-100">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="p-3 bg-success-subtle text-success rounded-12">
                    <i class="fas fa-hand-holding-usd fs-3"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold">{{ __('Projected Total Payout') }}</div>
                    <h3 class="fw-bold mb-0 text-success">₹{{ number_format(collect($nodes)->sum('total_payout'), 2) }}</h3>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 3-Step Execution Workflow Stepper -->
<div class="card border-0 shadow-sm rounded-12 mb-4">
    <div class="card-header bg-white py-3 border-bottom">
        <h5 class="card-title mb-0 fw-bold d-flex align-items-center gap-2">
            <i class="fas fa-tasks text-primary"></i> {{ __('Monthly Payout Execution Stepper') }}
        </h5>
    </div>
    <div class="card-body p-4">
        <div class="row g-3 align-items-stretch">
            {{-- Step 1: Select Month --}}
            <div class="col-lg-4">
                <div class="step-card shadow-sm">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="step-num">1</span>
                        <h6 class="fw-bold mb-0 text-dark">{{ __('Target Billing Period') }}</h6>
                    </div>
                    <p class="text-muted small mb-3">{{ __('Select the calendar month and year to audit calculations and execute.') }}</p>
                    <form method="GET" action="{{ route('admin.payout.run.form') }}" class="row g-2 align-items-end">
                        <div class="col-6">
                            <label class="form-label small text-muted mb-1">{{ __('Month') }}</label>
                            <select name="month" class="form-select form-select-sm">
                                @foreach($months as $m)
                                    <option value="{{ $m }}" @selected((string) $month === (string) $m)>{{ $m }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label small text-muted mb-1">{{ __('Year') }}</label>
                            <select name="year" class="form-select form-select-sm">
                                @for($y = now()->year; $y >= 2024; $y--)
                                    <option value="{{ $y }}" @selected((string) $year === (string) $y)>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-12 mt-2">
                            <button type="submit" class="btn btn-outline-primary btn-sm w-100 shadow-sm">
                                <i class="fas fa-sync-alt me-1"></i> {{ __('Switch Period Preview') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Step 2: Review Calculations --}}
            <div class="col-lg-4">
                <div class="step-card shadow-sm">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="step-num">2</span>
                        <h6 class="fw-bold mb-0 text-dark">{{ __('Audit Calculations') }}</h6>
                    </div>
                    <p class="text-muted small mb-3">{{ __('Verify delivered orders, 10% Phase 1 personal commissions, and Phase 2 group tiers below.') }}</p>
                    <div class="p-3 bg-light rounded-12 border">
                        <div class="d-flex justify-content-between align-items-center mb-2 small">
                            <span class="text-muted">{{ __('Period Status:') }}</span>
                            <span class="badge {{ $isPaid ? 'bg-success text-white' : 'bg-warning text-dark' }} px-2 py-1 rounded-pill">
                                {{ $isPaid ? __('Finalized Snapshot') : __('Live Projection') }}
                            </span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center small">
                            <span class="text-muted">{{ __('Total Payoutable:') }}</span>
                            <span class="fw-bold text-success fs-6">₹{{ number_format(collect($nodes)->sum('total_payout'), 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Step 3: Execute Disbursements --}}
            <div class="col-lg-4">
                <div class="step-card shadow-sm d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="step-num" style="background: #f0fdf4; color: #15803d;">3</span>
                            <h6 class="fw-bold mb-0 text-dark">{{ __('Execute Wallet Credit') }}</h6>
                        </div>
                        <p class="text-muted small mb-3">
                            {{ __('Atomically creates audit snapshots and credits vendor wallets. Skipped if already credited.') }}
                        </p>
                    </div>
                    <div>
                        <form method="POST" action="{{ route('admin.payout.run') }}" onsubmit="return confirm('{{ __('Are you sure you want to execute and credit monthly payouts for') }} {{ sprintf('%04d-%02d', $year, $month) }}?')">
                            @csrf
                            <input type="hidden" name="month" value="{{ $month }}">
                            <input type="hidden" name="year" value="{{ $year }}">
                            <button type="submit" class="btn btn-success w-100 py-2 fw-bold shadow-sm">
                                <i class="fas fa-play me-2"></i> {{ __('Confirm & Execute Payout') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if($isPaid)
    <div class="alert alert-info border-0 shadow-sm rounded-12 mb-4 d-flex align-items-center gap-3">
        <i class="fas fa-info-circle fs-3 text-info"></i>
        <div>
            <div class="fw-bold">{{ __('Monthly payouts already processed for this billing period.') }}</div>
            <div class="small">{{ __('The tree below reflects the frozen audit snapshot. Re-executing will safely skip previously credited shops and only disburse newly eligible partners.') }}</div>
        </div>
    </div>
@endif

{{-- Preview Tree Card --}}
<div class="card border-0 shadow-sm rounded-12">
    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h5 class="card-title mb-0 fw-bold">{{ __('Live Preview Breakdown') }} ({{ sprintf('%04d-%02d', $year, $month) }})</h5>
            <small class="text-muted">{{ __('Inspect individual shop nodes before confirming disbursement.') }}</small>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <input type="text" id="treeSearchInput" class="form-control form-control-sm" style="width: 220px;" placeholder="{{ __('Filter tree nodes...') }}">
            <button type="button" class="btn btn-sm btn-outline-secondary" id="expandAllBtn">
                <i class="fas fa-folder-open me-1"></i> {{ __('Expand All') }}
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary" id="collapseAllBtn">
                <i class="fas fa-folder me-1"></i> {{ __('Collapse All') }}
            </button>
            <span class="badge bg-light text-dark border ms-1">{{ count($nodes) }} {{ __('roots') }}</span>
        </div>
    </div>
    <div class="card-body p-4">
        <div class="payout-tree-wrapper">
            @forelse($nodes as $node)
                <ul class="list-unstyled mb-3">
                    @include('admin.payout._node', ['node' => $node, 'year' => $year, 'month' => $month])
                </ul>
            @empty
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-sitemap fs-1 mb-3 d-block text-secondary"></i>
                    {{ __('No active shops found for this month.') }}
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

@push('scripts')
@include('admin.payout.partials._tree_scripts', ['year' => $year, 'month' => $month])
@endpush
