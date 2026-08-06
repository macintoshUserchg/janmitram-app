@extends('layouts.app')

@section('header-title', $warehouse->name . ' — Stock Inventory')
@section('header-subtitle', __('Monitor and manage physical stock entries in this warehouse hub.'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <a href="{{ route('admin.warehouse.index') }}" class="btn btn-outline-secondary btn-sm mb-2">
            <i class="fas fa-arrow-left me-1"></i> {{ __('Back to Warehouses') }}
        </a>
        <h1 class="h3 mb-0 text-gray-800 fw-bold">{{ $warehouse->name }}</h1>
        <p class="text-muted small mb-0">{{ __('Physical Stock Allocation & Inventory Ledger Control') }}</p>
    </div>
    <div class="d-flex gap-2">
        @if($stocks->count() > 0)
            <form action="{{ route('admin.warehouse.stock.clear', $warehouse->id) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to remove ALL stock items from this warehouse?') }}')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger shadow-sm">
                    <i class="fas fa-trash-alt me-1"></i> {{ __('Clear All Stock') }}
                </button>
            </form>
        @endif
        <a href="{{ route('admin.warehouse.stock', $warehouse->id) }}" class="btn btn-success shadow-sm">
            <i class="fas fa-plus me-1"></i> {{ __('Add Stock Item') }}
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4">{{ session('success') }}</div>
@endif

<!-- Warehouse Profile Card -->
<div class="card border-0 shadow-sm rounded-12 mb-4 bg-white">
    <div class="card-body p-4">
        <div class="row g-3 align-items-center">
            <div class="col-md-3 border-end">
                <div class="text-muted small fw-semibold text-uppercase">{{ __('Warehouse Classification') }}</div>
                <div class="mt-1">
                    @if($warehouse->is_default)
                        <span class="badge bg-success-subtle text-success fs-6 px-3 py-2 rounded-pill">
                            <i class="fas fa-star me-1"></i> {{ __('Central Hub') }}
                        </span>
                    @else
                        <span class="badge bg-light text-secondary border fs-6 px-3 py-2 rounded-pill">
                            <i class="fas fa-network-wired me-1"></i> {{ __('Sub Warehouse') }}
                        </span>
                    @endif
                </div>
            </div>
            <div class="col-md-3 border-end">
                <div class="text-muted small fw-semibold text-uppercase">{{ __('Owner / Linked Entity') }}</div>
                <div class="fw-bold text-dark fs-6 mt-1">
                    {{ $warehouse->shop?->name ?? __('Root Central System') }}
                </div>
            </div>
            <div class="col-md-3 border-end">
                <div class="text-muted small fw-semibold text-uppercase">{{ __('Physical Location / Address') }}</div>
                <div class="text-dark fs-6 mt-1">
                    <i class="fas fa-map-marker-alt text-danger me-1"></i> {{ $warehouse->address ?? __('Main Logistics Center') }}
                </div>
            </div>
            <div class="col-md-3 text-md-center">
                <div class="text-muted small fw-semibold text-uppercase">{{ __('Total Stock Items') }}</div>
                <div class="h3 fw-bold text-primary mb-0 mt-1">{{ $stocks->total() }} {{ __('SKUs') }}</div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-12">
    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0 fw-bold">{{ __('Stock Inventory Items') }}</h5>
        <span class="badge bg-primary px-3 py-2">{{ $stocks->total() }} {{ __('Items Stocked') }}</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4" style="width: 70px;">
                            @include('admin.partials.sortable-header', ['label' => '#ID', 'column' => 'id', 'route' => 'admin.warehouse.show', 'routeParam' => $warehouse->id, 'sort' => $sort, 'direction' => $direction])
                        </th>
                        <th>{{ __('Product Specification') }}</th>
                        <th>{{ __('Color Variant') }}</th>
                        <th>{{ __('Size Variant') }}</th>
                        <th class="text-center">{{ __('Available Stock') }}</th>
                        <th>
                            @include('admin.partials.sortable-header', ['label' => __('Last Updated'), 'column' => 'updated_at', 'route' => 'admin.warehouse.show', 'routeParam' => $warehouse->id, 'sort' => $sort, 'direction' => $direction])
                        </th>
                        <th class="text-center pe-4">{{ __('Action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stocks as $key => $stock)
                        <tr>
                            <td class="ps-4 text-muted fw-bold">#{{ $stock->id }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    @if($stock->product?->thumbnail)
                                        <img src="{{ $stock->product->thumbnail }}" width="45" height="45" class="rounded object-fit-cover shadow-sm">
                                    @endif
                                    <div>
                                        <div class="fw-bold text-dark fs-6">{{ $stock->product?->name }}</div>
                                        <span class="small text-muted font-monospace">{{ __('ID:') }} #{{ $stock->product_id }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($stock->color)
                                    <span class="badge bg-light text-dark border"><i class="fas fa-palette me-1 text-primary"></i> {{ $stock->color->name }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if($stock->size)
                                    <span class="badge bg-light text-dark border"><i class="fas fa-ruler me-1 text-info"></i> {{ $stock->size->name }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $stock->quantity > 10 ? 'bg-success' : ($stock->quantity > 0 ? 'bg-warning text-dark' : 'bg-danger') }} fs-6 px-3 py-2 rounded-pill">
                                    {{ $stock->quantity }} {{ __('units') }}
                                </span>
                            </td>
                            <td class="small text-muted">{{ $stock->updated_at?->diffForHumans() }}</td>
                            <td class="text-center pe-4">
                                <div class="d-flex justify-content-center gap-1">
                                    @if($warehouse->isCentralHub())
                                        <button type="button"
                                            class="btn btn-sm btn-outline-primary rounded-circle p-2"
                                            style="width:34px; height:34px;"
                                            data-bs-toggle="modal" data-bs-target="#updateStockModal"
                                            data-stock-id="{{ $stock->id }}"
                                            data-stock-name="{{ $stock->product?->name }}"
                                            data-stock-qty="{{ $stock->quantity }}"
                                            data-stock-color="{{ $stock->color?->name }}" data-stock-size="{{ $stock->size?->name }}"
                                            data-bs-tooltip="tooltip" title="{{ __('Update Quantity') }}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    @endif
                                    <form action="{{ route('admin.warehouse-stock.destroy', $stock->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Remove this item from warehouse stock?') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle p-2" style="width:34px; height:34px;" data-bs-toggle="tooltip" title="{{ __('Remove Item') }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fas fa-boxes fs-1 mb-3 d-block text-secondary"></i>
                                {{ __('No stock inventory records found in this warehouse.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white border-top py-0 px-0">
        @include('admin.partials.pagination', ['paginator' => $stocks])
    </div>
</div>

<!-- Update Stock Quantity Modal -->
<div class="modal fade" id="updateStockModal" tabindex="-1" aria-labelledby="updateStockModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="updateStockForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="updateStockModalLabel"><i class="fas fa-edit text-primary me-2"></i>{{ __('Update Stock Quantity') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small mb-3">
                        <span id="updateStockProduct"></span>
                        <span id="updateStockVariant" class="badge bg-light text-dark border ms-1"></span>
                    </p>
                    <div class="mb-3">
                        <label for="updateStockQty" class="form-label fw-semibold">{{ __('Quantity') }}</label>
                        <input type="number" class="form-control" id="updateStockQty" name="quantity" min="0" value="0" required>
                    </div>
                    <label for="updateStockNotes" class="form-label fw-semibold">{{ __('Notes / Reason') }} <span class="text-muted">({{ __('optional') }})</span></label>
                    <textarea name="notes" id="updateStockNotes" class="form-control" rows="2" placeholder="{{ __('Reason for this quantity change...') }}"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>{{ __('Update Quantity') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var modal = document.getElementById('updateStockModal');
        if (!modal) return;

        modal.addEventListener('show.bs.modal', function (event) {
            var btn = event.relatedTarget;
            if (!btn) return;

            var id = btn.getAttribute('data-stock-id');
            var name = btn.getAttribute('data-stock-name');
            var qty = btn.getAttribute('data-stock-qty');
            var color = btn.getAttribute('data-stock-color');
            var size = btn.getAttribute('data-stock-size');

            var form = document.getElementById('updateStockForm');
            form.setAttribute('action', "{{ url('admin/warehouse-stock') }}/" + id + "/quantity");

            document.getElementById('updateStockProduct').textContent = name || '';
            var label = [];
            if (color && color !== 'null') label.push(color);
            if (size && size !== 'null') label.push(size);
            document.getElementById('updateStockVariant').textContent = label.length ? label.join(' / ') : '';

            document.getElementById('updateStockQty').value = qty;
            document.getElementById('updateStockNotes').value = '';
        });
    });
</script>
@endpush
@endsection
