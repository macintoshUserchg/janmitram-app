@extends('layouts.app')
@section('header-title', __('Bulk Product Exports'))
@section('content')
<!-- Integrated 2-Line Compact Header Banner Card -->
<div class="card border-0 shadow-sm rounded-12 mb-4 bg-gradient bg-primary text-white">
    <div class="card-body p-3">
        <div class="d-flex justify-content-between align-items-center mb-1">
            <h1 class="h4 mb-0 text-white fw-bold"><i class="fas fa-file-export me-2 text-warning"></i>{{ __('Product Bulk Export') }}</h1>
        </div>
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 text-white-50 small">
            <span>{{ __('Export shop catalog and inventory data into Excel files.') }}</span>
        </div>
    </div>
</div>

@if ($isRootShop)
    <div class="container-fluid mt-3">
        <div class="card" style="border-color: rgba(231, 234, 243, 0.5019607843);">
            <div class="card-body">
                <div class="d-flex gap-2 pb-2 mt-4">
                    <h5>
                        <i class="fa-solid fa-file-export"></i>
                        {{ __('Export Products') }}
                    </h5>
                </div>

                <form action="{{ route('shop.bulk-product-export.export') }}" method="POST">
                    @csrf
                    <div class="d-flex gap-2 flex-wrap justify-content-end">
                        <a href="{{ route('shop.bulk-product-export.demo') }}" class="btn btn-outline-primary py-2 px-3">
                            <i class="fa-solid fa-download me-1"></i>
                            {{ __('Download Template') }}
                        </a>
                        <button type="submit" class="btn btn-primary py-2 px-3">
                            <i class="fa-solid fa-file-export me-1"></i>
                            {{ __('Export') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif
@endsection
