<script>
(function () {
    // Lazy-load children for collapsed nodes
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
                        html += renderNode(node, {{ $year ?? 'now()->year' }}, {{ $month ?? 'now()->month' }});
                    });
                    target.innerHTML = html;
                    target.dataset.loaded = '1';
                })
                .catch(function () {
                    target.innerHTML = '<li class="text-danger small py-1 ms-4"><i class="fas fa-exclamation-triangle me-1"></i> {{ __('Failed to load downline children.') }}</li>';
                });
        }
    });

    // Expand All button
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

    // Collapse All button
    var collapseBtn = document.getElementById('collapseAllBtn');
    if (collapseBtn) {
        collapseBtn.addEventListener('click', function () {
            document.querySelectorAll('.payout-children.show').forEach(function(el) {
                var bsCollapse = bootstrap.Collapse.getInstance(el) || new bootstrap.Collapse(el, {toggle: false});
                bsCollapse.hide();
            });
        });
    }

    // Live Tree Search & Highlighting
    var searchInput = document.getElementById('treeSearchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            var q = this.value.trim().toLowerCase();
            var nodes = document.querySelectorAll('.payout-tree-node');
            if (!q) {
                nodes.forEach(function (node) {
                    node.classList.remove('is-hidden', 'is-matched');
                });
                return;
            }

            nodes.forEach(function (node) {
                var text = node.querySelector('.payout-tree-card')?.textContent?.toLowerCase() || '';
                if (text.includes(q)) {
                    node.classList.remove('is-hidden');
                    node.classList.add('is-matched');
                    // Automatically expand parent ul containers
                    var parent = node.parentElement.closest('.payout-children');
                    if (parent && !parent.classList.contains('show')) {
                        var bsCollapse = bootstrap.Collapse.getInstance(parent) || new bootstrap.Collapse(parent, {toggle: false});
                        bsCollapse.show();
                    }
                } else {
                    node.classList.remove('is-matched');
                }
            });
        });
    }

    function renderNode(node, year, month) {
        var url = node.has_children
            ? '{{ route('admin.payout.network.children', ['shop' => '__ID__']) }}?year=' + year + '&month=' + month
            : '';
        url = url.replace('__ID__', node.shop_id);

        var chevron = node.has_children
            ? '<button type="button" class="btn btn-sm btn-light border p-1 rounded-circle payout-expand text-primary me-1" data-bs-toggle="collapse" data-bs-target="#node-' + node.shop_id + '" aria-expanded="false" title="{{ __('Toggle downline') }}"><i class="fas fa-chevron-right payout-chevron fs-6" style="width: 14px; height: 14px; display: inline-flex; align-items: center; justify-content: center;"></i></button>'
            : '<span class="d-inline-block text-center text-muted me-1" style="width: 24px;"><i class="fas fa-store-alt opacity-50"></i></span>';

        var levelBadge = '';
        if (node.level !== null) {
            var rankNames = {
                0: 'L0 - {{ __('Star Promoter') }}',
                1: 'L1 - {{ __('Silver Associate') }}',
                2: 'L2 - {{ __('Gold Leader') }}',
                3: 'L3 - {{ __('Diamond Director') }}',
                4: 'L4 - {{ __('Crown Ambassador') }}'
            };
            var rankTitle = rankNames[node.level] || ('Level ' + node.level);
            levelBadge = '<span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1 fw-bold">' + rankTitle + '</span>';
        } else {
            levelBadge = '<span class="badge bg-light text-secondary border rounded-pill px-2 py-1">—</span>';
        }

        var downlinesCount = Math.max(0, (node.group_size || 1) - 1);
        var downlineLabel = downlinesCount === 1 ? '{{ __('downline') }}' : '{{ __('downlines') }}';
        var children = node.has_children
            ? '<ul class="list-unstyled collapse payout-children" id="node-' + node.shop_id + '"></ul>'
            : '';

        return '<li class="payout-tree-node" data-shop-id="' + node.shop_id + '" data-children-url="' + url + '">'
            + '<div class="payout-tree-card"><div class="d-flex align-items-center justify-content-between flex-wrap gap-2">'
            + '<div class="d-flex align-items-center gap-2 flex-grow-1">' + chevron
            + '<div class="d-flex align-items-center gap-2 flex-wrap">'
            + '<span class="fw-bold text-dark fs-6">' + esc(node.shop_name) + '</span>'
            + '<span class="text-muted small">(' + esc(node.owner_name) + ')</span>' + levelBadge + '</div></div>'
            + '<div class="d-flex align-items-center gap-2 flex-wrap text-nowrap small">'
            + '<span class="badge bg-light text-dark border px-2 py-1 fw-normal">{{ __('Personal') }}: <span class="fw-bold text-dark">₹' + fmt(node.personal_sales) + '</span></span>'
            + '<span class="badge bg-light text-dark border px-2 py-1 fw-normal">{{ __('Group Sales') }}: <span class="fw-bold text-primary">₹' + fmt(node.group_sales) + '</span></span>'
            + '<span class="badge bg-light text-dark border px-2 py-1 fw-normal" title="{{ __('Total team size: 1 self + downline shops') }}"><i class="fas fa-users text-secondary me-1"></i>{{ __('Team') }}: <span class="fw-bold text-dark">' + node.group_size + '</span> <span class="text-muted small">(' + downlinesCount + ' ' + downlineLabel + ')</span></span>'
            + '<span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 fw-normal">{{ __('Phase 1') }}: <span class="fw-bold">₹' + fmt(node.phase1_amount) + '</span></span>'
            + '<span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1 fw-normal">{{ __('Phase 2') }}: <span class="fw-bold">₹' + fmt(node.phase2_amount) + '</span></span>'
            + '<span class="badge bg-success text-white px-3 py-1 fw-bold fs-6">₹' + fmt(node.total_payout) + '</span></div></div></div>' + children + '</li>';
    }

    function fmt(v) { return Number(v).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); }
    function esc(s) { var d = document.createElement('div'); d.textContent = s || ''; return d.innerHTML; }
})();
</script>
