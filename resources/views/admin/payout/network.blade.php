@extends('layouts.app')

@section('header-title', __('Payout Management'))
@section('header-subtitle', __('Analyse the MLM network payouts hierarchically.'))

@push('styles')
@include('admin.payout.partials._tree_styles')
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

<!-- Summary Metric KPI Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-12 bg-white h-100">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="p-3 bg-primary-subtle text-primary rounded-12">
                    <i class="fas fa-sitemap fs-3"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold">{{ __('Root Networks') }}</div>
                    <h3 class="fw-bold mb-0 text-dark">{{ count($nodes) }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-12 bg-white h-100">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="p-3 bg-info-subtle text-info rounded-12">
                    <i class="fas fa-chart-line fs-3"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold">{{ __('Network Group Sales') }}</div>
                    <h3 class="fw-bold mb-0 text-info">₹{{ number_format(collect($nodes)->sum('group_sales'), 2) }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-12 bg-white h-100">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="p-3 bg-success-subtle text-success rounded-12">
                    <i class="fas fa-wallet fs-3"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold">{{ __('Total Projected Payout') }}</div>
                    <h3 class="fw-bold mb-0 text-success">₹{{ number_format(collect($nodes)->sum('total_payout'), 2) }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-12 bg-white h-100">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="p-3 {{ $isPaid ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning' }} rounded-12">
                    <i class="fas {{ $isPaid ? 'fa-check-circle' : 'fa-clock' }} fs-3"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold">{{ __('Billing Period Status') }}</div>
                    <span class="badge {{ $isPaid ? 'bg-success text-white' : 'bg-warning text-dark' }} px-3 py-1 fs-6 rounded-pill">
                        {{ $isPaid ? __('Snapshot (Finalized)') : __('Live Current Month') }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

@if($isPaid)
    <div class="alert alert-info border-0 shadow-sm mb-4 rounded-12 d-flex align-items-center gap-3">
        <i class="fas fa-info-circle fs-4 text-info"></i>
        <div>
            <strong>{{ __('Finalized Historical Snapshot') }} ({{ sprintf('%04d-%02d', $year, $month) }}):</strong>
            {{ __('These values represent the frozen audit snapshot at payout execution time. New shops or orders created after payout execution are not reflected in this historical tree.') }}
        </div>
    </div>
@else
    <div class="alert alert-secondary border-0 shadow-sm mb-4 rounded-12 d-flex align-items-center gap-3">
        <i class="fas fa-clock fs-4 text-secondary"></i>
        <div>
            <strong>{{ __('Live Network Preview') }} ({{ sprintf('%04d-%02d', $year, $month) }}):</strong>
            {{ __('This is a live preview of the current organizational downline tree. Calculations will be permanently snapshot when the monthly payout is executed.') }}
        </div>
    </div>
@endif

{{-- Period Filter Bar --}}
<div class="card border-0 shadow-sm rounded-12 mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('admin.payout.network') }}" class="row g-2 align-items-end">
            <div class="col-auto">
                <label class="form-label small text-muted mb-1">{{ __('Month') }}</label>
                <select name="month" class="form-select form-select-sm">
                    @foreach($months as $m)
                        <option value="{{ $m }}" @selected((string) $month === (string) $m)>{{ $m }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <label class="form-label small text-muted mb-1">{{ __('Year') }}</label>
                <select name="year" class="form-select form-select-sm">
                    @for($y = now()->year; $y >= 2024; $y--)
                        <option value="{{ $y }}" @selected((string) $year === (string) $y)>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-outline-primary btn-sm shadow-sm"><i class="fas fa-filter me-1"></i> {{ __('Apply Filter') }}</button>
                <a href="{{ route('admin.payout.network') }}" class="btn btn-outline-secondary btn-sm shadow-sm">{{ __('Current Active Month') }}</a>
            </div>
        </form>
    </div>
</div>

{{-- Hierarchical Tree Card --}}
<div class="card border-0 shadow-sm rounded-12">
    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h5 class="card-title mb-0 fw-bold">{{ __('Hierarchical Network Tree') }} ({{ sprintf('%04d-%02d', $year, $month) }})</h5>
            <small class="text-muted">{{ __('Click arrow chevrons to expand downline partner nodes.') }}</small>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <input type="text" id="treeSearchInput" class="form-control form-control-sm" style="width: 220px;" placeholder="{{ __('Search tree nodes...') }}">
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
                    {{ __('No active shops found in the network for this month.') }}
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

@push('scripts')
@include('admin.payout.partials._tree_scripts', ['year' => $year, 'month' => $month])
@endpush
