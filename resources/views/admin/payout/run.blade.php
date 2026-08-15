@extends('layouts.app')

@section('header-title', __('Payout Management'))
@section('header-subtitle', __('Review the month, then confirm the payout run.'))

@push('styles')
<style>
.payout-tree-wrapper {
    position: relative;
    padding-left: 0;
}
.payout-tree-wrapper ul {
    position: relative;
    padding-left: 1.75rem;
    list-style: none;
    margin-bottom: 0;
}
.payout-tree-wrapper ul::before {
    content: '';
    position: absolute;
    top: 0;
    bottom: 1.25rem;
    left: 0.85rem;
    width: 2px;
    background-color: #cbd5e1;
}
.payout-tree-wrapper li {
    position: relative;
    margin-top: 0.75rem;
    margin-bottom: 0.75rem;
}
.payout-tree-wrapper ul > li::before {
    content: '';
    position: absolute;
    top: 1.5rem;
    left: -0.9rem;
    width: 0.9rem;
    height: 2px;
    background-color: #cbd5e1;
}
.payout-tree-card {
    background-color: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 0.75rem;
    padding: 0.75rem 1rem;
    transition: all 0.2s ease-in-out;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}
.payout-tree-card:hover {
    border-color: #3b82f6;
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.12);
}
.payout-chevron {
    transition: transform 0.2s ease-in-out;
}
.payout-expand[aria-expanded="true"] .payout-chevron {
    transform: rotate(90deg);
}
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1 text-dark">{{ __('Run Monthly Payout') }}</h4>
        <p class="text-muted small mb-0">{{ __('Select a target month, review projected earnings, and execute payouts to shop wallets.') }}</p>
    </div>
    <div class="d-flex gap-2">
        @hasPermission('admin.payout.network')
            <a href="{{ route('admin.payout.network', ['year' => $year, 'month' => $month]) }}" class="btn btn-outline-secondary shadow-sm">
                <i class="fas fa-sitemap me-1"></i> {{ __('Payout Network') }}
            </a>
        @endhasPermission
        @hasPermission('admin.payout.index')
            <a href="{{ route('admin.payout.index') }}" class="btn btn-outline-secondary shadow-sm">
                <i class="fas fa-history me-1"></i> {{ __('Payout History') }}
            </a>
        @endhasPermission
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4">{!! nl2br(e(session('error'))) !!}</div>
@endif

<!-- Metric Summary Bar -->
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
                    <div class="text-muted small fw-semibold">{{ __('Root Networks') }}</div>
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
                    <div class="text-muted small fw-semibold">{{ __('Projected Sales') }}</div>
                    <h3 class="fw-bold mb-0 text-dark">{{ number_format(collect($nodes)->sum('group_sales'), 2) }}</h3>
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
                    <div class="text-muted small fw-semibold">{{ __('Projected Payout') }}</div>
                    <h3 class="fw-bold mb-0 text-success">{{ number_format(collect($nodes)->sum('total_payout'), 2) }}</h3>
                </div>
            </div>
        </div>
    </div>
</div>

@if($isPaid)
    <div class="alert alert-info border-0 shadow-sm mb-4">
        <i class="fas fa-info-circle me-1"></i>
        {{ __('This month is already paid — the tree below reflects the finalized payout snapshot, not the current live network. Running the payout again will be skipped for shops that were already credited.') }}
    </div>
@else
    <div class="alert alert-secondary border-0 shadow-sm mb-4">
        <i class="fas fa-clock me-1"></i>
        {{ __('This is a live preview of the current downline tree. Values will be finalized when the payout for this month is run.') }}
    </div>
@endif

<!-- Month Selection & Run Action Card -->
<div class="card border-0 shadow-sm rounded-12 mb-4">
    <div class="card-body py-3">
        <div class="row align-items-center g-3">
            <div class="col-lg-6">
                <form method="GET" action="{{ route('admin.payout.run.form') }}" class="row g-2 align-items-end">
                    <div class="col-auto">
                        <label class="form-label small text-muted mb-1">{{ __('Select Month') }}</label>
                        <select name="month" class="form-select form-select-sm">
                            @foreach($months as $m)
                                <option value="{{ $m }}" @selected((string) $month === (string) $m)>{{ $m }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto">
                        <label class="form-label small text-muted mb-1">{{ __('Select Year') }}</label>
                        <select name="year" class="form-select form-select-sm">
                            @for($y = now()->year; $y >= 2024; $y--)
                                <option value="{{ $y }}" @selected((string) $year === (string) $y)>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-outline-primary btn-sm shadow-sm"><i class="fas fa-search me-1"></i> {{ __('Update Preview') }}</button>
                    </div>
                </form>
            </div>
            <div class="col-lg-6 text-lg-end">
                <form method="POST" action="{{ route('admin.payout.run') }}" class="d-inline" onsubmit="return confirm('{{ __('Are you sure you want to execute payouts for') }} {{ sprintf('%04d-%02d', $year, $month) }}?')">
                    @csrf
                    <input type="hidden" name="month" value="{{ $month }}">
                    <input type="hidden" name="year" value="{{ $year }}">
                    <button type="submit" class="btn btn-success btn-lg shadow-sm">
                        <i class="fas fa-play me-2"></i> {{ __('Confirm & Execute Payout for') }} {{ sprintf('%04d-%02d', $year, $month) }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@if($isPaid)
    <div class="alert alert-info border-0 shadow-sm rounded-12 mb-4 d-flex align-items-center gap-3">
        <i class="fas fa-info-circle fs-3 text-info"></i>
        <div>
            <div class="fw-bold">{{ __('Payouts already processed for this period.') }}</div>
            <div class="small">{{ __('Monthly payouts for') }} {{ sprintf('%04d-%02d', $year, $month) }} {{ __('were previously executed. Submitting again will process any new or skipped shops.') }}</div>
        </div>
    </div>
@endif

{{-- Preview tree (read-only) --}}
<div class="card border-0 shadow-sm rounded-12">
    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h5 class="card-title mb-0 fw-bold">{{ __('Live Preview Breakdown') }} ({{ sprintf('%04d-%02d', $year, $month) }})</h5>
            <small class="text-muted">{{ __('Inspect shop-level payouts before confirming execution.') }}</small>
        </div>
        <span class="badge bg-light text-dark border">{{ count($nodes) }} {{ __('roots') }}</span>
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
                    {{ __('No active shops for this month.') }}
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
