@extends('layouts.app')

@section('header-title', __('Payout Management'))
@section('header-subtitle', __('Comprehensive user guide, compensation plan documentation, and interactive payout simulator.'))

@section('content')
{{-- Sub-Navigation Hub --}}
@include('admin.payout.partials._nav')

<!-- Overview Banner -->
<div class="card border-0 shadow-sm rounded-12 mb-4 bg-primary text-white overflow-hidden position-relative" style="background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 50%, #3b82f6 100%);">
    <div class="card-body p-4 p-lg-5">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <span class="badge bg-white text-primary px-3 py-2 rounded-pill fw-bold mb-3 shadow-sm">
                    <i class="fas fa-certificate me-1 text-warning"></i> {{ __('Official MLM Compensation Plan') }}
                </span>
                <h2 class="fw-bold mb-2 text-white">{{ __('Dual-Phase Monthly Payout Architecture') }}</h2>
                <p class="mb-0 text-white-50 fs-6 leading-relaxed">
                    {{ __('Janmitram empowers franchise shop owners with a hybrid compensation engine: Phase 1 rewards direct sales volume, while Phase 2 rewards multi-level network performance across 5 progressive achievement ranks.') }}
                </p>
            </div>
            <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
                <div class="d-inline-flex align-items-center justify-content-center bg-white bg-opacity-10 rounded-circle p-4 border border-white border-opacity-25 shadow">
                    <i class="fas fa-network-wired fa-4x text-white"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Key Highlights Row -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-12 bg-white h-100">
            <div class="card-body p-4 text-center">
                <div class="p-3 bg-primary-subtle text-primary rounded-circle d-inline-flex mb-3">
                    <i class="fas fa-percentage fs-3"></i>
                </div>
                <h6 class="text-muted text-uppercase small fw-bold mb-1">{{ __('Phase 1 Rate') }}</h6>
                <h3 class="fw-bold text-dark mb-0">10.0%</h3>
                <span class="small text-muted">{{ __('Flat on Delivered Orders') }}</span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-12 bg-white h-100">
            <div class="card-body p-4 text-center">
                <div class="p-3 bg-success-subtle text-success rounded-circle d-inline-flex mb-3">
                    <i class="fas fa-layer-group fs-3"></i>
                </div>
                <h6 class="text-muted text-uppercase small fw-bold mb-1">{{ __('Phase 2 Ranks') }}</h6>
                <h3 class="fw-bold text-success mb-0">L0 – L4</h3>
                <span class="small text-muted">{{ __('5 Group Achievement Tiers') }}</span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-12 bg-white h-100">
            <div class="card-body p-4 text-center">
                <div class="p-3 bg-info-subtle text-info rounded-circle d-inline-flex mb-3">
                    <i class="fas fa-wallet fs-3"></i>
                </div>
                <h6 class="text-muted text-uppercase small fw-bold mb-1">{{ __('Payout Settlement') }}</h6>
                <h3 class="fw-bold text-info mb-0">{{ __('Atomic') }}</h3>
                <span class="small text-muted">{{ __('Direct Vendor Wallet Credit') }}</span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-12 bg-white h-100">
            <div class="card-body p-4 text-center">
                <div class="p-3 bg-warning-subtle text-warning rounded-circle d-inline-flex mb-3">
                    <i class="fas fa-shield-alt fs-3"></i>
                </div>
                <h6 class="text-muted text-uppercase small fw-bold mb-1">{{ __('Retention Rule') }}</h6>
                <h3 class="fw-bold text-warning mb-0">90 {{ __('Days') }}</h3>
                <span class="small text-muted">{{ __('Inactivity Auto-Reparenting') }}</span>
            </div>
        </div>
    </div>
</div>

<!-- Interactive Commission Calculator Simulator -->
<div class="card border-0 shadow-sm rounded-12 mb-4 bg-white overflow-hidden border-top border-4 border-primary">
    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
        <div>
            <h5 class="card-title mb-0 fw-bold"><i class="fas fa-calculator me-2 text-primary"></i>{{ __('Interactive Payout & Commission Simulator') }}</h5>
            <small class="text-muted">{{ __('Test various sales and team size scenarios to see real-time calculation breakdown.') }}</small>
        </div>
        <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill fw-bold">{{ __('Live Sandbox') }}</span>
    </div>
    <div class="card-body p-4">
        <div class="row g-4 align-items-center">
            <div class="col-lg-6 border-end-lg pe-lg-4">
                <div class="mb-3">
                    <label class="form-label fw-bold text-dark">{{ __('Personal Monthly Sales (Delivered Orders) (₹)') }}</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light fw-bold">₹</span>
                        <input type="number" id="calcPersonalSales" class="form-control form-control-lg fw-bold text-primary" value="25000" min="0" step="1000">
                    </div>
                    <small class="text-muted">{{ __('Phase 1 direct earnings = 10% of this amount.') }}</small>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold text-dark">{{ __('Cumulative Downline Group Sales (₹)') }}</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light fw-bold">₹</span>
                        <input type="number" id="calcGroupSales" class="form-control form-control-lg fw-bold text-success" value="85000" min="0" step="5000">
                    </div>
                    <small class="text-muted">{{ __('Total sales generated by all direct & indirect downline shops.') }}</small>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold text-dark">{{ __('Total Downline Team Size (Active Shops)') }}</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fas fa-store text-muted"></i></span>
                        <input type="number" id="calcGroupSize" class="form-control form-control-lg fw-bold text-dark" value="12" min="1" step="1">
                    </div>
                    <small class="text-muted">{{ __('Minimum shop count required to unlock specific achievement ranks.') }}</small>
                </div>
            </div>

            <div class="col-lg-6 ps-lg-4">
                <div class="p-4 bg-light rounded-12 border shadow-sm">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted text-uppercase small fw-bold">{{ __('Achieved Rank') }}</span>
                        <span id="calcRankBadge" class="badge bg-success fs-6 px-3 py-2 rounded-pill fw-bold">L1: Silver Associate</span>
                    </div>

                    <div class="border-bottom pb-3 mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">{{ __('Phase 1 (Personal Sales @ 10%):') }}</span>
                            <span class="fw-bold text-dark fs-6" id="calcPhase1Val">₹2,500.00</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">{{ __('Phase 2 (Group Sales Bonus):') }}</span>
                            <span class="fw-bold text-success fs-6" id="calcPhase2Val">₹3,400.00</span>
                        </div>
                        <div class="d-flex justify-content-between small text-muted">
                            <span>{{ __('Phase 2 Formula Applied:') }}</span>
                            <span id="calcFormula">4.0% of ₹85,000</span>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-2">
                        <div>
                            <span class="text-muted small fw-bold text-uppercase d-block">{{ __('Total Monthly Projected Payout') }}</span>
                            <span class="small text-muted">{{ __('Credited to Shop Wallet') }}</span>
                        </div>
                        <div class="text-end">
                            <h2 class="fw-bold text-primary mb-0" id="calcTotalPayout">₹5,900.00</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Phase Breakdown Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-12 h-100 bg-white">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="p-3 bg-primary-subtle text-primary rounded-12">
                        <i class="fas fa-user-check fs-3"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0 text-dark">{{ __('Phase 1: Personal Sales Commission') }}</h5>
                        <small class="text-muted">{{ __('Direct earnings on own shop sales') }}</small>
                    </div>
                </div>
                <p class="text-muted mb-3 leading-relaxed">
                    {{ __('Every active franchise branch earns a flat 10% commission on all Delivered customer and POS orders fulfilled by their shop during the calendar billing month.') }}
                </p>
                <div class="p-3 bg-light rounded-12 border d-flex justify-content-between align-items-center">
                    <span class="fw-semibold text-dark">{{ __('Commission Rate') }}</span>
                    <span class="badge bg-primary fs-6 px-3 py-2 rounded-pill">10.0% {{ __('of Delivered Volume') }}</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-12 h-100 bg-white">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="p-3 bg-success-subtle text-success rounded-12">
                        <i class="fas fa-users-cog fs-3"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0 text-dark">{{ __('Phase 2: Group Sales Tiers') }}</h5>
                        <small class="text-muted">{{ __('Hierarchical downline leadership bonus') }}</small>
                    </div>
                </div>
                <p class="text-muted mb-3 leading-relaxed">
                    {{ __('Sponsors who recruit and nurture franchise downlines unlock tiered bonuses evaluated against cumulative group sales volume and minimum active team member thresholds.') }}
                </p>
                <div class="p-3 bg-light rounded-12 border d-flex justify-content-between align-items-center">
                    <span class="fw-semibold text-dark">{{ __('Achievement Levels') }}</span>
                    <span class="badge bg-success fs-6 px-3 py-2 rounded-pill">L0 {{ __('through') }} L4 (Up to ₹1.5L Cap)</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tier Matrix Table Card -->
<div class="card border-0 shadow-sm rounded-12 mb-4 bg-white">
    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h5 class="card-title mb-0 fw-bold"><i class="fas fa-table-list me-2 text-primary"></i>{{ __('Phase 2 Group Achievement Matrix') }}</h5>
            <small class="text-muted">{{ __('Evaluated lowest level first on qualifying monthly Group Sales and Team Size.') }}</small>
        </div>
        <span class="badge bg-light text-dark border px-3 py-2 rounded-pill">{{ __('5 Official Tiers') }}</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">{{ __('Level') }}</th>
                        <th>{{ __('Rank Title') }}</th>
                        <th>{{ __('Min Group Sales') }}</th>
                        <th>{{ __('Max Group Sales') }}</th>
                        <th class="text-center">{{ __('Min Team Size') }}</th>
                        <th class="text-end">{{ __('Commission / Calculation') }}</th>
                        <th class="text-end pe-4">{{ __('Max Level Cap') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="ps-4">
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-2 fw-bold">L0</span>
                        </td>
                        <td class="fw-bold text-dark">{{ __('Star Promoter') }}</td>
                        <td class="fw-semibold text-dark">₹33,000</td>
                        <td class="text-muted">₹75,000</td>
                        <td class="text-center"><span class="badge bg-light text-dark border">≥ 10 {{ __('shops') }}</span></td>
                        <td class="text-end fw-bold text-success">₹3,000 {{ __('Flat Bonus') }}</td>
                        <td class="text-end pe-4 text-muted">—</td>
                    </tr>
                    <tr>
                        <td class="ps-4">
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-2 fw-bold">L1</span>
                        </td>
                        <td class="fw-bold text-dark">{{ __('Silver Associate') }}</td>
                        <td class="fw-semibold text-dark">₹75,000</td>
                        <td class="text-muted">₹300,000</td>
                        <td class="text-center"><span class="badge bg-light text-dark border">≥ 10 {{ __('shops') }}</span></td>
                        <td class="text-end fw-bold text-success">4.0% {{ __('of Group Sales') }}</td>
                        <td class="text-end pe-4 text-muted">—</td>
                    </tr>
                    <tr>
                        <td class="ps-4">
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-2 fw-bold">L2</span>
                        </td>
                        <td class="fw-bold text-dark">{{ __('Gold Leader') }}</td>
                        <td class="fw-semibold text-dark">₹300,000</td>
                        <td class="text-muted">₹3,000,000</td>
                        <td class="text-center"><span class="badge bg-light text-dark border">≥ 100 {{ __('shops') }}</span></td>
                        <td class="text-end fw-bold text-success">1.0% {{ __('of Group Sales') }}</td>
                        <td class="text-end pe-4 text-muted">—</td>
                    </tr>
                    <tr>
                        <td class="ps-4">
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-2 fw-bold">L3</span>
                        </td>
                        <td class="fw-bold text-dark">{{ __('Diamond Director') }}</td>
                        <td class="fw-semibold text-dark">₹3,000,000</td>
                        <td class="text-muted">₹30,000,000</td>
                        <td class="text-center"><span class="badge bg-light text-dark border">≥ 10,000 {{ __('shops') }}</span></td>
                        <td class="text-end fw-bold text-success">0.2% {{ __('of Group Sales') }}</td>
                        <td class="text-end pe-4 text-muted">—</td>
                    </tr>
                    <tr>
                        <td class="ps-4">
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-2 fw-bold">L4</span>
                        </td>
                        <td class="fw-bold text-dark">{{ __('Crown Ambassador') }}</td>
                        <td class="fw-semibold text-dark">₹30,000,000+</td>
                        <td class="text-muted">—</td>
                        <td class="text-center"><span class="badge bg-light text-dark border">≥ 100,000 {{ __('shops') }}</span></td>
                        <td class="text-end fw-bold text-success">0.04% {{ __('of Group Sales') }}</td>
                        <td class="text-end pe-4 fw-bold text-danger">₹150,000 {{ __('Cap') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- End-to-End Operational Lifecycle & Procedures -->
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-12 h-100 bg-white">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="p-3 bg-primary-subtle text-primary rounded-12">
                        <i class="fas fa-play-circle fs-3"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0 text-dark">{{ __('1. Monthly Execution') }}</h5>
                        <small class="text-muted">{{ __('Admin Payout Processing') }}</small>
                    </div>
                </div>
                <ol class="small text-muted ps-3 mb-3 leading-relaxed">
                    <li class="mb-1">{{ __('Go to "Run Payout" and select target billing month.') }}</li>
                    <li class="mb-1">{{ __('Review live tree calculations and projected totals.') }}</li>
                    <li>{{ __('Execute payout — snapshots are recorded and vendor wallets are credited atomically.') }}</li>
                </ol>
                <div class="bg-light p-3 rounded-12 border small text-muted font-monospace">
                    <i class="fas fa-terminal text-primary me-1"></i>
                    <code>php artisan mlm:calculate-payouts --month=M --year=Y</code>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-12 h-100 bg-white">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="p-3 bg-success-subtle text-success rounded-12">
                        <i class="fas fa-file-invoice-dollar fs-3"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0 text-dark">{{ __('2. Payout Slips & Withdrawals') }}</h5>
                        <small class="text-muted">{{ __('Settlement & Bank Transfers') }}</small>
                    </div>
                </div>
                <ul class="small text-muted ps-3 mb-3 leading-relaxed">
                    <li class="mb-1">{{ __('Shop owners download official PDF Payout Slips breakdown.') }}</li>
                    <li class="mb-1">{{ __('Vendors request withdrawals from their wallet balance.') }}</li>
                    <li>{{ __('Admin verifies KYC, transfers bank NEFT/IMPS, and marks approved.') }}</li>
                </ul>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.withdraw.index') }}" class="btn btn-sm btn-outline-success w-100 fw-bold">
                        <i class="fas fa-money-check-alt me-1"></i> {{ __('Manage Withdrawals') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-12 h-100 bg-white">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="p-3 bg-warning-subtle text-warning rounded-12">
                        <i class="fas fa-user-clock fs-3"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0 text-dark">{{ __('3. 90-Day Retention') }}</h5>
                        <small class="text-muted">{{ __('Inactivity Auto-Detachment') }}</small>
                    </div>
                </div>
                <p class="text-muted small leading-relaxed mb-3">
                    {{ __('Members with no Delivered orders in 90 days are automatically detached on the 1st of each month. Their downlines are reparented cleanly without losing network structure.') }}
                </p>
                <div class="bg-light p-3 rounded-12 border small text-muted font-monospace">
                    <i class="fas fa-terminal text-warning me-1"></i>
                    <code>php artisan mlm:deactivate-inactive</code>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    (function() {
        const tiers = [
            { level: 0, title: 'L0: Star Promoter', min: 33000, max: 75000, size: 10, rate: 3000, isFlat: true, cap: null },
            { level: 1, title: 'L1: Silver Associate', min: 75000, max: 300000, size: 10, rate: 0.04, isFlat: false, cap: null },
            { level: 2, title: 'L2: Gold Leader', min: 300000, max: 3000000, size: 100, rate: 0.01, isFlat: false, cap: null },
            { level: 3, title: 'L3: Diamond Director', min: 3000000, max: 30000000, size: 10000, rate: 0.002, isFlat: false, cap: null },
            { level: 4, title: 'L4: Crown Ambassador', min: 30000000, max: Infinity, size: 100000, rate: 0.0004, isFlat: false, cap: 150000 }
        ];

        function formatCurrency(val) {
            return '₹' + Number(val).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        function calculate() {
            const personal = Math.max(0, parseFloat($('#calcPersonalSales').val()) || 0);
            const group = Math.max(0, parseFloat($('#calcGroupSales').val()) || 0);
            const size = Math.max(1, parseInt($('#calcGroupSize').val(), 10) || 1);

            // Phase 1 (10% of personal sales)
            const phase1 = personal * 0.10;

            // Phase 2
            let matchedTier = null;
            let phase2 = 0;
            let formulaText = 'No Tier Met (Requires min ₹33,000 Group Sales & 10 Shops)';

            for (const t of tiers) {
                if (group >= t.min && group < t.max && size >= t.size) {
                    matchedTier = t;
                    if (t.isFlat) {
                        phase2 = t.rate;
                        formulaText = 'Flat Bonus ₹' + t.rate.toLocaleString('en-IN');
                    } else {
                        phase2 = group * t.rate;
                        if (t.cap && phase2 > t.cap) {
                            phase2 = t.cap;
                            formulaText = (t.rate * 100).toFixed(2) + '% (Capped at ₹' + t.cap.toLocaleString('en-IN') + ')';
                        } else {
                            formulaText = (t.rate * 100).toFixed(2) + '% of ' + formatCurrency(group);
                        }
                    }
                    break;
                }
            }

            const total = phase1 + phase2;

            $('#calcPhase1Val').text(formatCurrency(phase1));
            $('#calcPhase2Val').text(formatCurrency(phase2));
            $('#calcTotalPayout').text(formatCurrency(total));
            $('#calcFormula').text(formulaText);

            if (matchedTier) {
                $('#calcRankBadge')
                    .removeClass('bg-secondary bg-primary bg-info')
                    .addClass('bg-success')
                    .text(matchedTier.title);
            } else {
                $('#calcRankBadge')
                    .removeClass('bg-success bg-primary bg-info')
                    .addClass('bg-secondary')
                    .text('Standard Member (No Phase 2 Tier)');
            }
        }

        $('#calcPersonalSales, #calcGroupSales, #calcGroupSize').on('input change', calculate);
        calculate();
    })();
</script>
@endpush
@endsection
