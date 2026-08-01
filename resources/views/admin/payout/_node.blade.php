{{-- Node: render one shop in the tree. $node: array shape from networkForMonth(). --}}
<li class="payout-node" data-shop-id="{{ $node['shop_id'] }}"
    data-children-url="{{ $node['has_children'] ? route('admin.payout.network.children', ['shop' => $node['shop_id']]) . '?year=' . $year . '&month=' . $month : '' }}">
    <div class="d-flex align-items-center gap-2 py-2 payout-node-row">
        @if($node['has_children'])
            <button type="button" class="btn btn-sm btn-link p-0 payout-expand"
                data-bs-toggle="collapse" data-bs-target="#node-{{ $node['shop_id'] }}"
                aria-expanded="false">
                <i class="fas fa-chevron-right payout-chevron"></i>
            </button>
        @else
            <span class="d-inline-block" style="width:24px;"></span>
        @endif
        <div class="d-flex align-items-center gap-2 flex-wrap flex-grow-1">
            <span class="fw-bold text-dark">{{ $node['shop_name'] }}</span>
            <span class="text-muted small">{{ $node['owner_name'] }}</span>
            @if($node['level'] !== null)
                <span class="badge bg-success-subtle text-success rounded-pill">L{{ $node['level'] }}</span>
            @else
                <span class="badge bg-light text-secondary border rounded-pill">—</span>
            @endif
        </div>
        <div class="d-flex align-items-center gap-3 text-nowrap small">
            <span class="text-muted">{{ __('Personal') }} <span class="fw-semibold text-dark">{{ number_format($node['personal_sales'], 2) }}</span></span>
            <span class="text-muted">{{ __('Group') }} <span class="fw-semibold text-dark">{{ number_format($node['group_sales'], 2) }}</span></span>
            <span class="text-muted">{{ __('Size') }} <span class="fw-semibold text-dark">{{ $node['group_size'] }}</span></span>
            <span class="text-muted">{{ __('Ph1') }} <span class="fw-semibold">{{ number_format($node['phase1_amount'], 2) }}</span></span>
            <span class="text-muted">{{ __('Ph2') }} <span class="fw-semibold">{{ number_format($node['phase2_amount'], 2) }}</span></span>
            <span class="fw-bold text-success">{{ number_format($node['total_payout'], 2) }}</span>
        </div>
    </div>
    @if($node['has_children'])
        <ul class="list-unstyled ms-4 collapse payout-children" id="node-{{ $node['shop_id'] }}">
            @foreach($node['children'] ?? [] as $child)
                @include('admin.payout._node', ['node' => $child, 'year' => $year, 'month' => $month])
            @endforeach
        </ul>
    @endif
</li>
