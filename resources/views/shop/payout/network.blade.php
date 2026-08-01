@extends('layouts.app')

@section('header-title', __('My Downline Network'))
@section('header-subtitle', __('Analyse your downline shop tree and network sales distribution.'))

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
        <h4 class="fw-bold mb-1 text-dark">{{ __('My Downline Network') }}</h4>
        <p class="text-muted small mb-0">{{ __('Explore your downline shops, sales performance, and group tree structure.') }}</p>
    </div>
    <div class="d-flex gap-2">
        @hasPermission('shop.payout.index')
            <a href="{{ route('shop.payout.index') }}" class="btn btn-outline-secondary shadow-sm">
                <i class="fas fa-arrow-left me-1"></i> {{ __('Back to My Payouts') }}
            </a>
        @endhasPermission
    </div>
</div>

{{-- Month / Year Filter --}}
<div class="card border-0 shadow-sm rounded-12 mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('shop.payout.network') }}" class="row g-2 align-items-end">
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
                <button type="submit" class="btn btn-outline-primary btn-sm shadow-sm"><i class="fas fa-filter me-1"></i> {{ __('Apply') }}</button>
                <a href="{{ route('shop.payout.network') }}" class="btn btn-outline-secondary btn-sm shadow-sm">{{ __('Reset') }}</a>
            </div>
        </form>
    </div>
</div>

{{-- Hierarchical Tree Card --}}
<div class="card border-0 shadow-sm rounded-12">
    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h5 class="card-title mb-0 fw-bold">{{ __('My Network Tree') }} ({{ sprintf('%04d-%02d', $year, $month) }})</h5>
            <small class="text-muted">{{ __('Click chevrons to expand your downline nodes.') }}</small>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <button type="button" class="btn btn-sm btn-outline-secondary" id="expandAllBtn">
                <i class="fas fa-folder-open me-1"></i> {{ __('Expand All') }}
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary" id="collapseAllBtn">
                <i class="fas fa-folder me-1"></i> {{ __('Collapse All') }}
            </button>
            <span class="badge bg-light text-dark border ms-1">{{ count($nodes) }} {{ __('root') }}</span>
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
                    {{ __('No active shop network data available for this month.') }}
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    // Lazy-load children for collapsed nodes.
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.payout-expand');
        if (!btn) return;
        var li = btn.closest('.payout-tree-node');
        var target = document.querySelector(btn.getAttribute('data-bs-target'));
        if (!target) return;

        if (!target.dataset.loaded && li.dataset.childrenUrl) {
            fetch(li.dataset.childrenUrl)
                .then(function (r) { return r.json(); })
                .then(function (children) {
                    if (!children || !children.length) { target.dataset.loaded = '1'; return; }
                    var html = '';
                    children.forEach(function (node) {
                        html += renderNode(node, {{ $year }}, {{ $month }});
                    });
                    target.innerHTML = html;
                    target.dataset.loaded = '1';
                })
                .catch(function () {
                    target.innerHTML = '<li class="text-danger small py-1 ms-4">{{ __('Failed to load downline children.') }}</li>';
                });
        }
    });

    var expandBtn = document.getElementById('expandAllBtn');
    if (expandBtn) {
        expandBtn.addEventListener('click', function () {
            document.querySelectorAll('.payout-expand').forEach(function(btn) {
                var target = document.querySelector(btn.getAttribute('data-bs-target'));
                if (target && !target.classList.contains('show')) {
                    btn.click();
                }
            });
        });
    }

    var collapseBtn = document.getElementById('collapseAllBtn');
    if (collapseBtn) {
        collapseBtn.addEventListener('click', function () {
            document.querySelectorAll('.payout-children.show').forEach(function(el) {
                var bsCollapse = bootstrap.Collapse.getInstance(el) || new bootstrap.Collapse(el, {toggle: false});
                bsCollapse.hide();
            });
        });
    }

    function renderNode(node, year, month) {
        var url = node.has_children
            ? '{{ route('shop.payout.network.children', ['shop' => '__ID__']) }}?year=' + year + '&month=' + month
            : '';
        url = url.replace('__ID__', node.shop_id);
        var chevron = node.has_children
            ? '<button type="button" class="btn btn-sm btn-light border p-1 rounded-circle payout-expand text-primary me-1" data-bs-toggle="collapse" data-bs-target="#node-' + node.shop_id + '" aria-expanded="false" title="{{ __('Toggle downline') }}"><i class="fas fa-chevron-right payout-chevron fs-6" style="width: 14px; height: 14px; display: inline-flex; align-items: center; justify-content: center;"></i></button>'
            : '<span class="d-inline-block text-center text-muted me-1" style="width: 24px;"><i class="fas fa-store-alt opacity-50"></i></span>';
        var level = node.level !== null
            ? '<span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1">Level ' + node.level + '</span>'
            : '<span class="badge bg-light text-secondary border rounded-pill px-2 py-1">—</span>';
        var children = node.has_children
            ? '<ul class="list-unstyled collapse payout-children" id="node-' + node.shop_id + '"></ul>'
            : '';
        return '<li class="payout-tree-node" data-shop-id="' + node.shop_id + '" data-children-url="' + url + '">'
            + '<div class="payout-tree-card"><div class="d-flex align-items-center justify-content-between flex-wrap gap-2">'
            + '<div class="d-flex align-items-center gap-2 flex-grow-1">' + chevron
            + '<div class="d-flex align-items-center gap-2 flex-wrap">'
            + '<span class="fw-bold text-dark fs-6">' + esc(node.shop_name) + '</span>'
            + '<span class="text-muted small">(' + esc(node.owner_name) + ')</span>' + level + '</div></div>'
            + '<div class="d-flex align-items-center gap-2 flex-wrap text-nowrap small">'
            + '<span class="badge bg-light text-dark border px-2 py-1 fw-normal">{{ __('Personal') }}: <span class="fw-bold text-dark">₹' + fmt(node.personal_sales) + '</span></span>'
            + '<span class="badge bg-light text-dark border px-2 py-1 fw-normal">{{ __('Group Sales') }}: <span class="fw-bold text-primary">₹' + fmt(node.group_sales) + '</span></span>'
            + '<span class="badge bg-light text-dark border px-2 py-1 fw-normal">{{ __('Group Size') }}: <span class="fw-bold text-dark">' + node.group_size + '</span></span>'
            + '<span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 fw-normal">{{ __('Phase 1') }}: <span class="fw-bold">₹' + fmt(node.phase1_amount) + '</span></span>'
            + '<span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1 fw-normal">{{ __('Phase 2') }}: <span class="fw-bold">₹' + fmt(node.phase2_amount) + '</span></span>'
            + '<span class="badge bg-success text-white px-3 py-1 fw-bold fs-6">₹' + fmt(node.total_payout) + '</span></div></div></div>' + children + '</li>';
    }
    function fmt(v) { return Number(v).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); }
    function esc(s) { var d = document.createElement('div'); d.textContent = s || ''; return d.innerHTML; }
})();
</script>
@endpush
