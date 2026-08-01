@extends('layouts.app')

@section('header-title', __('Payout Management'))
@section('header-subtitle', __('Analyse the MLM network payouts hierarchically.'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h1 class="h3 mb-0 text-gray-800 fw-bold">{{ __('Payout Network') }}</h1>
        <p class="text-muted small mb-0">{{ __('Expand nodes to explore downline payouts.') }}</p>
    </div>
    <div class="d-flex gap-2">
        @hasPermission('admin.payout.run')
            <a href="{{ route('admin.payout.run.form', ['year' => $year, 'month' => $month]) }}" class="btn btn-primary shadow-sm">
                <i class="fas fa-play me-1"></i> {{ __('Run Payout') }}
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

{{-- Month / Year Filter --}}
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
                <button type="submit" class="btn btn-outline-primary btn-sm shadow-sm"><i class="fas fa-filter me-1"></i> {{ __('Apply') }}</button>
                <a href="{{ route('admin.payout.network') }}" class="btn btn-outline-secondary btn-sm shadow-sm">{{ __('Reset') }}</a>
            </div>
        </form>
    </div>
</div>

{{-- Tree --}}
<div class="card border-0 shadow-sm rounded-12">
    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0 fw-bold">{{ __('Network for') }} {{ sprintf('%04d-%02d', $year, $month) }}</h5>
        <span class="badge bg-light text-dark border">{{ count($nodes) }} {{ __('roots') }}</span>
    </div>
    <div class="card-body p-3">
        @forelse($nodes as $node)
            <ul class="list-unstyled mb-0">
                @include('admin.payout._node', ['node' => $node, 'year' => $year, 'month' => $month])
            </ul>
        @empty
            <div class="text-center py-5 text-muted">
                <i class="fas fa-sitemap fs-1 mb-3 d-block text-secondary"></i>
                {{ __('No active shops in the network for this month.') }}
            </div>
        @endforelse
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
        var li = btn.closest('.payout-node');
        var target = document.querySelector(btn.getAttribute('data-bs-target'));
        if (!target || target.dataset.loaded) return;

        var url = li.dataset.childrenUrl;
        if (!url) return;

        fetch(url)
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
                target.innerHTML = '<li class="text-danger small py-1">{{ __('Failed to load children.') }}</li>';
            });
    });

    function renderNode(node, year, month) {
        var url = node.has_children
            ? '{{ route('admin.payout.network.children', ['shop' => '__ID__']) }}?year=' + year + '&month=' + month
            : '';
        url = url.replace('__ID__', node.shop_id);
        var chevron = node.has_children
            ? '<button type="button" class="btn btn-sm btn-link p-0 payout-expand" data-bs-toggle="collapse" data-bs-target="#node-' + node.shop_id + '" aria-expanded="false"><i class="fas fa-chevron-right payout-chevron"></i></button>'
            : '<span class="d-inline-block" style="width:24px;"></span>';
        var level = node.level !== null
            ? '<span class="badge bg-success-subtle text-success rounded-pill">L' + node.level + '</span>'
            : '<span class="badge bg-light text-secondary border rounded-pill">—</span>';
        var children = node.has_children
            ? '<ul class="list-unstyled ms-4 collapse payout-children" id="node-' + node.shop_id + '"></ul>'
            : '';
        return '<li class="payout-node" data-shop-id="' + node.shop_id + '" data-children-url="' + url + '">'
            + '<div class="d-flex align-items-center gap-2 py-2 payout-node-row">' + chevron
            + '<div class="d-flex align-items-center gap-2 flex-wrap flex-grow-1">'
            + '<span class="fw-bold text-dark">' + esc(node.shop_name) + '</span>'
            + '<span class="text-muted small">' + esc(node.owner_name) + '</span>' + level + '</div>'
            + '<div class="d-flex align-items-center gap-3 text-nowrap small">'
            + '<span class="text-muted">{{ __('Personal') }} <span class="fw-semibold text-dark">' + fmt(node.personal_sales) + '</span></span>'
            + '<span class="text-muted">{{ __('Group') }} <span class="fw-semibold text-dark">' + fmt(node.group_sales) + '</span></span>'
            + '<span class="text-muted">{{ __('Size') }} <span class="fw-semibold text-dark">' + node.group_size + '</span></span>'
            + '<span class="text-muted">{{ __('Ph1') }} <span class="fw-semibold">' + fmt(node.phase1_amount) + '</span></span>'
            + '<span class="text-muted">{{ __('Ph2') }} <span class="fw-semibold">' + fmt(node.phase2_amount) + '</span></span>'
            + '<span class="fw-bold text-success">' + fmt(node.total_payout) + '</span></div></div>' + children + '</li>';
    }
    function fmt(v) { return Number(v).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); }
    function esc(s) { var d = document.createElement('div'); d.textContent = s || ''; return d.innerHTML; }
})();
</script>
@endpush
