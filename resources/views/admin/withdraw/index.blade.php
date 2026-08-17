@extends('layouts.app')

@section('header-title', __('Withdrawal Management'))
@section('header-subtitle', __('Audit vendor wallet withdrawal requests, verify KYC bank particulars, and disburse funds.'))

@push('styles')
<style>
.kpi-card {
    transition: all 0.2s ease-in-out;
    border-radius: 0.75rem;
    border: 1px solid #e2e8f0;
    background: #ffffff;
}
.kpi-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.06);
}
.bank-badge {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 0.5rem;
    padding: 0.35rem 0.6rem;
    font-size: 0.8125rem;
}
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h4 class="fw-bold mb-1 text-dark d-flex align-items-center gap-2">
            <i class="fas fa-hand-holding-usd text-primary"></i> {{ __('Withdrawal Management') }}
        </h4>
        <p class="text-muted small mb-0">
            {{ __('Review shop withdrawal requests, audit KYC bank account details, and disburse funds across financial periods.') }}
        </p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('admin.withdraw.export', request()->query()) }}" class="btn btn-success shadow-sm">
            <i class="fas fa-file-excel me-1"></i> {{ __('Export to CSV') }}
        </a>
        @hasPermission('admin.business-setting.withdraw')
            <a href="{{ route('admin.business-setting.withdraw') }}" class="btn btn-outline-secondary shadow-sm">
                <i class="fas fa-cog me-1"></i> {{ __('Withdraw Settings') }}
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

<!-- Dynamic Summary KPI Metric Cards (Reflects Filtered Context) -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <a href="{{ route('admin.withdraw.index', array_merge(request()->query(), ['status' => ''])) }}" class="text-decoration-none">
            <div class="kpi-card p-3 shadow-sm h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="p-3 bg-primary-subtle text-primary rounded-12">
                        <i class="fas fa-wallet fs-3"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">{{ __('Total Requested') }}</div>
                        <h4 class="fw-bold mb-0 text-dark">₹{{ number_format($summary['total_amount'], 2) }}</h4>
                        <small class="text-muted">{{ $summary['total_count'] }} {{ __('total requests') }}</small>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-3">
        <a href="{{ route('admin.withdraw.index', array_merge(request()->query(), ['status' => 'pending'])) }}" class="text-decoration-none">
            <div class="kpi-card p-3 shadow-sm h-100 {{ request('status') === 'pending' ? 'border-warning border-2' : '' }}">
                <div class="d-flex align-items-center gap-3">
                    <div class="p-3 bg-warning-subtle text-warning rounded-12">
                        <i class="fas fa-clock fs-3"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">{{ __('Pending Requests') }}</div>
                        <h4 class="fw-bold mb-0 text-warning">₹{{ number_format($summary['pending_amount'], 2) }}</h4>
                        <small class="text-muted">{{ $summary['pending_count'] }} {{ __('pending review') }}</small>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-3">
        <a href="{{ route('admin.withdraw.index', array_merge(request()->query(), ['status' => 'approved'])) }}" class="text-decoration-none">
            <div class="kpi-card p-3 shadow-sm h-100 {{ request('status') === 'approved' ? 'border-success border-2' : '' }}">
                <div class="d-flex align-items-center gap-3">
                    <div class="p-3 bg-success-subtle text-success rounded-12">
                        <i class="fas fa-check-circle fs-3"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">{{ __('Approved / Disbursed') }}</div>
                        <h4 class="fw-bold mb-0 text-success">₹{{ number_format($summary['approved_amount'], 2) }}</h4>
                        <small class="text-muted">{{ $summary['approved_count'] }} {{ __('disbursed') }}</small>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-3">
        <a href="{{ route('admin.withdraw.index', array_merge(request()->query(), ['status' => 'denied'])) }}" class="text-decoration-none">
            <div class="kpi-card p-3 shadow-sm h-100 {{ request('status') === 'denied' ? 'border-danger border-2' : '' }}">
                <div class="d-flex align-items-center gap-3">
                    <div class="p-3 bg-danger-subtle text-danger rounded-12">
                        <i class="fas fa-times-circle fs-3"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">{{ __('Denied Requests') }}</div>
                        <h4 class="fw-bold mb-0 text-danger">₹{{ number_format($summary['denied_amount'], 2) }}</h4>
                        <small class="text-muted">{{ $summary['denied_count'] }} {{ __('rejected') }}</small>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>

<!-- Advanced Multi-Filter & Financial Period Selection Bar -->
<div class="card border-0 shadow-sm rounded-12 mb-4 bg-white">
    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h6 class="card-title mb-0 fw-bold d-flex align-items-center gap-2">
            <i class="fas fa-filter text-primary"></i> {{ __('Financial Period & Filter Criteria') }}
        </h6>
        <div class="small text-muted">
            {{ __('Showing :count of :total records matching filter', ['count' => $withdraws->count(), 'total' => $withdraws->total()]) }}
        </div>
    </div>
    <div class="card-body py-3">
        <form method="GET" action="{{ route('admin.withdraw.index') }}" id="withdrawFilterForm">
            <div class="row g-3 align-items-end">
                {{-- 1. Period Preset --}}
                <div class="col-md-3">
                    <label class="form-label small text-muted mb-1">{{ __('Financial Period') }}</label>
                    <select name="period" id="periodSelect" class="form-select form-select-sm">
                        <option value="all" @selected(request('period', 'all') === 'all')>{{ __('All Time') }}</option>
                        <option value="today" @selected(request('period') === 'today')>{{ __('Today') }}</option>
                        <option value="this_month" @selected(request('period') === 'this_month')>{{ __('This Month') }}</option>
                        <option value="last_month" @selected(request('period') === 'last_month')>{{ __('Last Month') }}</option>
                        <option value="this_quarter" @selected(request('period') === 'this_quarter')>{{ __('This Quarter (Q)') }}</option>
                        <option value="this_fy" @selected(request('period') === 'this_fy')>{{ __('Current Financial Year (FY)') }}</option>
                        <option value="last_fy" @selected(request('period') === 'last_fy')>{{ __('Previous Financial Year (FY)') }}</option>
                        <optgroup label="{{ __('Specific Financial Years') }}">
                            @foreach($financialYears as $fy)
                                <option value="{{ $fy['value'] }}" @selected(request('period') === $fy['value'])>{{ $fy['label'] }}</option>
                            @endforeach
                        </optgroup>
                        <option value="custom" @selected(request('period') === 'custom')>{{ __('Custom Date Range...') }}</option>
                    </select>
                </div>

                {{-- 2. Custom Date Range Inputs --}}
                <div class="col-md-2 date-range-box" id="startDateBox" style="{{ request('period') === 'custom' ? '' : 'display:none;' }}">
                    <label class="form-label small text-muted mb-1">{{ __('From Date') }}</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control form-control-sm">
                </div>
                <div class="col-md-2 date-range-box" id="endDateBox" style="{{ request('period') === 'custom' ? '' : 'display:none;' }}">
                    <label class="form-label small text-muted mb-1">{{ __('To Date') }}</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control form-control-sm">
                </div>

                {{-- 3. Status Filter --}}
                <div class="col-md-2">
                    <label class="form-label small text-muted mb-1">{{ __('Status') }}</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">{{ __('All Statuses') }}</option>
                        <option value="pending" @selected(request('status') === 'pending')>{{ __('Pending') }}</option>
                        <option value="approved" @selected(request('status') === 'approved')>{{ __('Approved') }}</option>
                        <option value="denied" @selected(request('status') === 'denied')>{{ __('Denied') }}</option>
                    </select>
                </div>

                {{-- 4. Shop Filter --}}
                <div class="col-md-2">
                    <label class="form-label small text-muted mb-1">{{ __('Filter by Shop') }}</label>
                    <select name="shop_id" class="form-select form-select-sm">
                        <option value="">{{ __('All Shops') }}</option>
                        @foreach($shops as $s)
                            <option value="{{ $s->id }}" @selected((string) request('shop_id') === (string) $s->id)>{{ $s->name }} (#{{ $s->id }})</option>
                        @endforeach
                    </select>
                </div>

                {{-- 5. Free Search Input --}}
                <div class="col-md-3">
                    <label class="form-label small text-muted mb-1">{{ __('Search Keyword') }}</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="{{ __('Shop, Owner, Phone, #ID...') }}">
                </div>

                {{-- 6. Amount Range & Filter Buttons --}}
                <div class="col-md-2">
                    <label class="form-label small text-muted mb-1">{{ __('Min (₹)') }}</label>
                    <input type="number" step="0.01" name="min_amount" value="{{ request('min_amount') }}" class="form-control form-control-sm" placeholder="0.00">
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted mb-1">{{ __('Max (₹)') }}</label>
                    <input type="number" step="0.01" name="max_amount" value="{{ request('max_amount') }}" class="form-control form-control-sm" placeholder="100000.00">
                </div>

                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm flex-grow-1 shadow-sm">
                        <i class="fas fa-filter me-1"></i> {{ __('Apply Filters') }}
                    </button>
                    <a href="{{ route('admin.withdraw.index') }}" class="btn btn-outline-secondary btn-sm shadow-sm" title="{{ __('Clear all filters') }}">
                        <i class="fas fa-undo"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Financial Audit Table Card -->
<div class="card border-0 shadow-sm rounded-12 bg-white">
    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h5 class="card-title mb-0 fw-bold">{{ __('Withdrawal Requests & Disbursal Ledger') }}</h5>
        <span class="badge bg-light text-dark border">{{ $withdraws->total() }} {{ __('Total Entries') }}</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4" style="min-width: 120px;">
                            @include('admin.partials.sortable-header', ['label' => __('ID'), 'column' => 'id', 'route' => 'admin.withdraw.index', 'sort' => $sort ?? 'id', 'direction' => $direction ?? 'desc'])
                        </th>
                        <th style="min-width: 160px;">
                            @include('admin.partials.sortable-header', ['label' => __('Request Date & Time'), 'column' => 'created_at', 'route' => 'admin.withdraw.index', 'sort' => $sort ?? 'id', 'direction' => $direction ?? 'desc'])
                        </th>
                        <th style="min-width: 160px;">
                            @include('admin.partials.sortable-header', ['label' => __('Shop & Owner'), 'column' => 'shop_name', 'route' => 'admin.withdraw.index', 'sort' => $sort ?? 'id', 'direction' => $direction ?? 'desc'])
                        </th>
                        <th style="min-width: 140px;">
                            @include('admin.partials.sortable-header', ['label' => __('Contact'), 'column' => 'name', 'route' => 'admin.withdraw.index', 'sort' => $sort ?? 'id', 'direction' => $direction ?? 'desc'])
                        </th>
                        <th>{{ __('KYC Bank / Payment Particulars') }}</th>
                        <th class="text-end" style="min-width: 140px;">
                            @include('admin.partials.sortable-header', ['label' => __('Amount (₹)'), 'column' => 'amount', 'route' => 'admin.withdraw.index', 'sort' => $sort ?? 'id', 'direction' => $direction ?? 'desc'])
                        </th>
                        <th class="text-center" style="min-width: 120px;">
                            @include('admin.partials.sortable-header', ['label' => __('Status'), 'column' => 'status', 'route' => 'admin.withdraw.index', 'sort' => $sort ?? 'id', 'direction' => $direction ?? 'desc'])
                        </th>
                        <th>{{ __('Remarks') }}</th>
                        <th class="text-center pe-4" style="width: 100px;">{{ __('Action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($withdraws as $withdraw)
                        @php
                            $shop = $withdraw->shop;
                            $user = $shop?->user;
                            $kyc = $shop?->kyc;
                        @endphp
                        <tr>
                            {{-- ID --}}
                            <td class="ps-4">
                                <span class="badge bg-light text-dark border fw-bold">#W-{{ str_pad((string) $withdraw->id, 5, '0', STR_PAD_LEFT) }}</span>
                            </td>

                            {{-- Request Date & Time --}}
                            <td>
                                <div class="fw-semibold text-dark">{{ $withdraw->created_at->format('M d, Y') }}</div>
                                <div class="text-muted small">{{ $withdraw->created_at->format('h:i A') }} ({{ $withdraw->created_at->diffForHumans() }})</div>
                            </td>

                            {{-- Shop & Owner --}}
                            <td>
                                <div class="fw-bold text-dark fs-6">{{ $shop ? $shop->name : ($withdraw->name ?? '—') }}</div>
                                <div class="text-muted small">
                                    <i class="fas fa-user text-secondary me-1"></i>{{ $user ? $user->fullName : '—' }}
                                    @if($shop?->parent)
                                        <span class="badge bg-light text-secondary border ms-1" title="{{ __('Sponsor') }}">{{ $shop->parent->referral_code }}</span>
                                    @endif
                                </div>
                            </td>

                            {{-- Contact Phone --}}
                            <td>
                                <div><i class="fas fa-phone-alt text-secondary me-1 small"></i>{{ $withdraw->contact_number ?? $user?->phone ?? '—' }}</div>
                                @if($user?->email)
                                    <div class="text-muted small">{{ $user->email }}</div>
                                @endif
                            </td>

                            {{-- Bank Particulars --}}
                            <td>
                                @if($kyc && ($kyc->bank_name || $kyc->account_number))
                                    <div class="bank-badge">
                                        <div class="fw-bold text-dark"><i class="fas fa-university text-primary me-1"></i>{{ $kyc->bank_name ?? __('Bank') }}</div>
                                        <div class="small text-muted font-monospace">A/C: {{ $kyc->account_number ? substr($kyc->account_number, 0, 4) . '••••' . substr($kyc->account_number, -4) : '—' }}</div>
                                        @if($kyc->ifsc)
                                            <div class="small text-muted">IFSC: <span class="fw-bold">{{ $kyc->ifsc }}</span></div>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-muted small"><i class="fas fa-info-circle me-1"></i>{{ $withdraw->withdraw_method ?? __('Direct Transfer') }}</span>
                                @endif
                            </td>

                            {{-- Amount --}}
                            <td class="text-end">
                                <span class="fw-bold text-dark fs-6">₹{{ number_format((float) $withdraw->amount, 2) }}</span>
                            </td>

                            {{-- Status Badge --}}
                            <td class="text-center">
                                @if ($withdraw->status == 'pending')
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-3 py-2 rounded-pill fw-semibold">
                                        <i class="fas fa-clock me-1"></i> {{ __('Pending') }}
                                    </span>
                                @elseif($withdraw->status == 'approved')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill fw-semibold">
                                        <i class="fas fa-check-circle me-1"></i> {{ __('Approved') }}
                                    </span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 rounded-pill fw-semibold">
                                        <i class="fas fa-times-circle me-1"></i> {{ __('Denied') }}
                                    </span>
                                @endif
                            </td>

                            {{-- Remarks --}}
                            <td>
                                <span class="text-muted small" title="{{ $withdraw->reason }}">
                                    {{ Str::limit($withdraw->reason ?? '—', 35) }}
                                </span>
                            </td>

                            {{-- Action --}}
                            <td class="text-center pe-4">
                                @hasPermission('admin.withdraw.show')
                                    <a href="{{ route('admin.withdraw.show', $withdraw->id) }}"
                                        class="btn btn-sm btn-outline-primary shadow-sm"
                                        title="{{ __('Audit & Process Withdrawal') }}">
                                        <i class="fas fa-eye me-1"></i> {{ __('Audit') }}
                                    </a>
                                @endhasPermission
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="fas fa-hand-holding-usd fs-1 mb-3 d-block text-secondary"></i>
                                {{ __('No withdrawal requests match the selected financial period and filters.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($withdraws->hasPages())
        <div class="card-footer bg-white border-top py-3">
            {{ $withdraws->links() }}
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var periodSelect = document.getElementById('periodSelect');
    var startDateBox = document.getElementById('startDateBox');
    var endDateBox = document.getElementById('endDateBox');

    if (periodSelect) {
        periodSelect.addEventListener('change', function () {
            if (this.value === 'custom') {
                startDateBox.style.display = '';
                endDateBox.style.display = '';
            } else {
                startDateBox.style.display = 'none';
                endDateBox.style.display = 'none';
            }
        });
    }
});
</script>
@endpush
