{{-- Node: render one shop in the hierarchical tree structure. --}}
<li class="payout-tree-node" data-shop-id="{{ $node['shop_id'] }}"
    data-children-url="{{ $node['has_children'] ? route('admin.payout.network.children', ['shop' => $node['shop_id']]) . '?year=' . $year . '&month=' . $month : '' }}">
    <div class="payout-tree-card">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2 flex-grow-1">
                @if($node['has_children'])
                    <button type="button" class="btn btn-sm btn-light border p-1 rounded-circle payout-expand text-primary me-1"
                        data-bs-toggle="collapse" data-bs-target="#node-{{ $node['shop_id'] }}"
                        aria-expanded="false" title="{{ __('Toggle downline') }}">
                        <i class="fas fa-chevron-right payout-chevron fs-6" style="width: 14px; height: 14px; display: inline-flex; align-items: center; justify-content: center;"></i>
                    </button>
                @else
                    <span class="d-inline-block text-center text-muted me-1" style="width: 24px;"><i class="fas fa-store-alt opacity-50"></i></span>
                @endif

                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="fw-bold text-dark fs-6">{{ $node['shop_name'] }}</span>
                    <span class="text-muted small">({{ $node['owner_name'] }})</span>
                    @if($node['level'] !== null)
                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1">Level {{ $node['level'] }}</span>
                    @else
                        <span class="badge bg-light text-secondary border rounded-pill px-2 py-1">—</span>
                    @endif
                </div>
            </div>

            <div class="d-flex align-items-center gap-2 flex-wrap text-nowrap small">
                <span class="badge bg-light text-dark border px-2 py-1 fw-normal">
                    {{ __('Personal') }}: <span class="fw-bold text-dark">₹{{ number_format($node['personal_sales'], 2) }}</span>
                </span>
                <span class="badge bg-light text-dark border px-2 py-1 fw-normal">
                    {{ __('Group Sales') }}: <span class="fw-bold text-primary">₹{{ number_format($node['group_sales'], 2) }}</span>
                </span>
                <span class="badge bg-light text-dark border px-2 py-1 fw-normal" title="{{ __('Downline shops counted at payout time for this month (frozen snapshot for paid months).') }}">
                    {{ __('Group Size') }}: <span class="fw-bold text-dark">{{ $node['group_size'] }}</span>
                </span>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 fw-normal">
                    {{ __('Phase 1') }}: <span class="fw-bold">₹{{ number_format($node['phase1_amount'], 2) }}</span>
                </span>
                <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1 fw-normal">
                    {{ __('Phase 2') }}: <span class="fw-bold">₹{{ number_format($node['phase2_amount'], 2) }}</span>
                </span>
                <span class="badge bg-success text-white px-3 py-1 fw-bold fs-6">
                    ₹{{ number_format($node['total_payout'], 2) }}
                </span>
            </div>
        </div>
    </div>

    @if($node['has_children'])
        <ul class="list-unstyled collapse payout-children" id="node-{{ $node['shop_id'] }}">
            @foreach($node['children'] ?? [] as $child)
                @include('admin.payout._node', ['node' => $child, 'year' => $year, 'month' => $month])
            @endforeach
        </ul>
    @endif
</li>
