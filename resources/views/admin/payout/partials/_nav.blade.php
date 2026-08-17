{{-- Modular Navigation Hub for Admin Payout Management --}}
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h4 class="fw-bold mb-1 text-dark d-flex align-items-center gap-2">
            @if(request()->routeIs('admin.payout.network'))
                <i class="fas fa-sitemap text-primary"></i> {{ __('Payout Network Tree') }}
            @elseif(request()->routeIs('admin.payout.index'))
                <i class="fas fa-history text-primary"></i> {{ __('Payout Audit History') }}
            @elseif(request()->routeIs('admin.payout.run*'))
                <i class="fas fa-bolt text-warning"></i> {{ __('Execute Monthly Payout') }}
            @elseif(request()->routeIs('admin.payout.guide'))
                <i class="fas fa-book-open text-info"></i> {{ __('Payout User Guide & Compensation Plan') }}
            @else
                <i class="fas fa-coins text-primary"></i> {{ __('Payout Management') }}
            @endif
        </h4>
        <p class="text-muted small mb-0">
            @if(request()->routeIs('admin.payout.network'))
                {{ __('Explore multi-generational MLM sales hierarchies, tier milestones, and live downline earnings.') }}
            @elseif(request()->routeIs('admin.payout.index'))
                {{ __('Official ledger of finalized monthly disbursements, Phase 1 direct sales, and Phase 2 network bonuses.') }}
            @elseif(request()->routeIs('admin.payout.run*'))
                {{ __('Audit monthly sales projections, inspect qualified shop nodes, and disburse wallet earnings.') }}
            @elseif(request()->routeIs('admin.payout.guide'))
                {{ __('Operational blueprint, 5-tier achievement matrix, and compensation plan calculations.') }}
            @else
                {{ __('Manage monthly shop payouts, downline hierarchies, and compensation tiers.') }}
            @endif
        </p>
    </div>

    {{-- Sub-Navigation Pill Bar --}}
    <div class="bg-white p-1 rounded-pill border shadow-sm d-inline-flex flex-wrap gap-1">
        @hasPermission('admin.payout.network')
            <a href="{{ route('admin.payout.network', isset($year) && isset($month) ? ['year' => $year, 'month' => $month] : []) }}"
               class="btn btn-sm rounded-pill px-3 py-1 fw-semibold {{ request()->routeIs('admin.payout.network') ? 'btn-primary shadow-sm' : 'btn-light text-secondary' }}">
                <i class="fas fa-sitemap me-1"></i> {{ __('Network Tree') }}
            </a>
        @endhasPermission

        @hasPermission('admin.payout.index')
            <a href="{{ route('admin.payout.index') }}"
               class="btn btn-sm rounded-pill px-3 py-1 fw-semibold {{ request()->routeIs('admin.payout.index') ? 'btn-primary shadow-sm' : 'btn-light text-secondary' }}">
                <i class="fas fa-history me-1"></i> {{ __('History Ledger') }}
            </a>
        @endhasPermission

        @hasPermission('admin.payout.run')
            <a href="{{ route('admin.payout.run.form', isset($year) && isset($month) ? ['year' => $year, 'month' => $month] : []) }}"
               class="btn btn-sm rounded-pill px-3 py-1 fw-semibold {{ request()->routeIs('admin.payout.run*') ? 'btn-primary shadow-sm' : 'btn-light text-secondary' }}">
                <i class="fas fa-play me-1"></i> {{ __('Run Payout') }}
            </a>
        @endhasPermission

        @hasPermission('admin.payout.guide')
            <a href="{{ route('admin.payout.guide') }}"
               class="btn btn-sm rounded-pill px-3 py-1 fw-semibold {{ request()->routeIs('admin.payout.guide') ? 'btn-primary shadow-sm' : 'btn-light text-secondary' }}">
                <i class="fas fa-book me-1"></i> {{ __('Plan Guide') }}
            </a>
        @endhasPermission
    </div>
</div>
