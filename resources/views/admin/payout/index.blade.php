@extends('layouts.app')

@section('header-title', __('Payout Management'))
@section('header-subtitle', __('Monthly MLM payouts for shop owners.'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1 text-dark">{{ __('Payout History') }}</h4>
        <p class="text-muted small mb-0">{{ __('Audit log of credited Phase 1 and Phase 2 monthly payouts.') }}</p>
    </div>
    <div class="d-flex gap-2">
        @hasPermission('admin.payout.network')
            <a href="{{ route('admin.payout.network') }}" class="btn btn-outline-secondary shadow-sm">
                <i class="fas fa-sitemap me-1"></i> {{ __('Payout Network') }}
            </a>
        @endhasPermission
        @hasPermission('admin.payout.run')
            <a href="{{ route('admin.payout.run.form') }}" class="btn btn-primary shadow-sm">
                <i class="fas fa-play me-1"></i> {{ __('Run Payout') }}
            </a>
        @endhasPermission
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4">{{ session('error') }}</div>
@endif

<!-- Summary Metric Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-12 bg-white h-100">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="p-3 bg-primary-subtle text-primary rounded-12">
                    <i class="fas fa-file-invoice-dollar fs-3"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold">{{ __('Total Payout Records') }}</div>
                    <h3 class="fw-bold mb-0 text-dark">{{ $payouts->total() }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-12 bg-white h-100">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="p-3 bg-success-subtle text-success rounded-12">
                    <i class="fas fa-coins fs-3"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold">{{ __('Total Credited') }}</div>
                    <h3 class="fw-bold mb-0 text-success">{{ number_format((float) $payouts->sum('total_payout'), 2) }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-12 bg-white h-100">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div class="p-3 bg-info-subtle text-info rounded-12">
                    <i class="fas fa-shopping-bag fs-3"></i>
                </div>
                <div>
                    <div class="text-muted small fw-semibold">{{ __('Personal Sales') }}</div>
                    <h3 class="fw-bold mb-0 text-info">{{ number_format((float) $payouts->sum('personal_sales'), 2) }}</h3>
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
                    <div class="text-muted small fw-semibold">{{ __('Group Sales') }}</div>
                    <h3 class="fw-bold mb-0 text-dark">{{ number_format((float) $payouts->sum('group_sales'), 2) }}</h3>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Month / Year Filter -->
<div class="card border-0 shadow-sm rounded-12 mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('admin.payout.index') }}" class="row g-2 align-items-end">
            <div class="col-auto">
                <label class="form-label small text-muted mb-1">{{ __('Month') }}</label>
                <select name="month" class="form-select form-select-sm">
                    <option value="">{{ __('All Months') }}</option>
                    @foreach($months as $m)
                        <option value="{{ $m }}" @selected((string) $month === (string) $m)>{{ $m }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <label class="form-label small text-muted mb-1">{{ __('Year') }}</label>
                <select name="year" class="form-select form-select-sm">
                    <option value="">{{ __('All Years') }}</option>
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
        <h5 class="card-title mb-0 fw-bold">{{ __('Historical Payout Records') }}</h5>
        <span class="badge bg-light text-dark border">{{ $payouts->total() }} {{ __('Total') }}</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">{{ __('SL') }}</th>
                        <th>{{ __('Month') }}</th>
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
                    @forelse($payouts as $key => $payout)
                        <tr>
                            <td class="ps-4 text-muted small">{{ $payouts->firstItem() + $key }}</td>
                            <td>
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
                                    <span class="badge bg-light text-secondary border rounded-pill">—</span>
                                @endif
                            </td>
                            <td class="text-end text-primary fw-semibold">{{ number_format((float) $payout->phase1_amount, 2) }}</td>
                            <td class="text-end text-info fw-semibold">{{ number_format((float) $payout->phase2_amount, 2) }}</td>
                            <td class="text-end pe-4 fw-bold text-success fs-6">{{ number_format((float) $payout->total_payout, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center py-5 text-muted">
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
@endsection
