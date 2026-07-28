@extends('layouts.app')

@section('header-title', __('Invoice Management'))
@section('header-subtitle', __('Centralized registry for stock dispatch invoices generated between Central Logistics Hubs and Shops.'))

@section('content')
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-12 bg-white p-3">
            <div class="d-flex align-items-center">
                <div class="rounded-circle bg-primary-subtle text-primary p-3 me-3">
                    <i class="fas fa-file-invoice fa-2x"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1 small text-uppercase fw-semibold">{{ __('Total Invoices') }}</h6>
                    <h3 class="mb-0 fw-bold">{{ number_format($totalInvoices) }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-12 bg-white p-3">
            <div class="d-flex align-items-center">
                <div class="rounded-circle bg-success-subtle text-success p-3 me-3">
                    <i class="fas fa-boxes fa-2x"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1 small text-uppercase fw-semibold">{{ __('Total Dispatched Units') }}</h6>
                    <h3 class="mb-0 fw-bold">{{ number_format($totalDispatchedUnits) }} {{ __('units') }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-12 bg-white p-3">
            <div class="d-flex align-items-center">
                <div class="rounded-circle bg-info-subtle text-info p-3 me-3">
                    <i class="fas fa-coins fa-2x"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1 small text-uppercase fw-semibold">{{ __('Total Dispatch Valuation') }}</h6>
                    <h3 class="mb-0 fw-bold">{{ showCurrency($totalValuation) }}</h3>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-12 bg-white">
    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h5 class="card-title mb-0 fw-bold"><i class="fas fa-receipt me-2 text-primary"></i>{{ __('Stock Dispatch Invoices Registry') }}</h5>
        
        <form action="{{ route('admin.invoice.index') }}" method="GET" class="d-flex gap-2">
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                <input type="text" name="search" class="form-control bg-light border-start-0" placeholder="{{ __('Search Invoice #, Shop, Warehouse...') }}" value="{{ request('search') }}">
            </div>
            <button type="submit" class="btn btn-primary fw-bold px-3">{{ __('Search') }}</button>
            @if(request('search'))
                <a href="{{ route('admin.invoice.index') }}" class="btn btn-outline-secondary">{{ __('Reset') }}</a>
            @endif
        </form>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">{{ __('Invoice #') }}</th>
                        <th>{{ __('Dispatch Date') }}</th>
                        <th>{{ __('Receiving Shop (To)') }}</th>
                        <th>{{ __('Fulfilling Hub (From)') }}</th>
                        <th class="text-center">{{ __('SKUs & Units') }}</th>
                        <th class="text-end">{{ __('Total Valuation') }}</th>
                        <th class="text-center pe-3">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $stockRequest)
                        @php
                            $totalQty = $stockRequest->items->sum('quantity');
                            $totalValue = $stockRequest->items->sum(function($item) {
                                return $item->quantity * ($item->product?->price ?? 0);
                            });
                        @endphp
                        <tr>
                            <td class="ps-3">
                                <span class="fw-bold text-primary">#INV-SR-{{ str_pad((string)$stockRequest->id, 5, '0', STR_PAD_LEFT) }}</span>
                                <div class="small text-muted">Ref Request #{{ $stockRequest->id }}</div>
                            </td>
                            <td>
                                <div>{{ $stockRequest->updated_at ? $stockRequest->updated_at->format('d M, Y') : '—' }}</div>
                                <div class="small text-muted">{{ $stockRequest->updated_at ? $stockRequest->updated_at->format('h:i A') : '' }}</div>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $stockRequest->shop?->name ?? '—' }}</div>
                                <div class="small text-muted">Shop ID: #{{ $stockRequest->shop_id }}</div>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $stockRequest->warehouse?->name ?? '—' }}</div>
                                <div class="small text-muted">Central Logistics Hub</div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-secondary-subtle text-secondary px-2 py-1">
                                    {{ $stockRequest->items->count() }} {{ __('SKUs') }} / {{ $totalQty }} {{ __('Units') }}
                                </span>
                            </td>
                            <td class="text-end fw-bold text-dark">
                                {{ showCurrency($totalValue) }}
                            </td>
                            <td class="text-center pe-3">
                                <div class="btn-group">
                                    <a href="{{ route('admin.stock-request.show', $stockRequest->id) }}" class="btn btn-sm btn-outline-secondary" title="{{ __('View Request Details') }}">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.stock-request.invoice', [$stockRequest->id, 'download' => 'pdf']) }}" target="_blank" class="btn btn-sm btn-outline-danger" title="{{ __('Download PDF Invoice') }}">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
                                    <a href="{{ route('admin.stock-request.invoice', $stockRequest->id) }}" target="_blank" class="btn btn-sm btn-outline-primary" title="{{ __('Print Dispatch Note') }}">
                                        <i class="fas fa-print"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="fas fa-receipt fa-2x mb-2 opacity-50 d-block"></i>
                                {{ __('No stock dispatch invoices found.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($invoices->hasPages())
        <div class="card-footer bg-white py-3 border-top">
            {{ $invoices->links() }}
        </div>
    @endif
</div>
@endsection
