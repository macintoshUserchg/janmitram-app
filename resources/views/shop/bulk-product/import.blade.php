@extends('layouts.app')
@section('header-title', __('Bulk Product Imports'))
@section('content')
<!-- Integrated 2-Line Compact Header Banner Card -->
<div class="card border-0 shadow-sm rounded-12 mb-4 bg-gradient bg-primary text-white">
    <div class="card-body p-3">
        <div class="d-flex justify-content-between align-items-center mb-1">
            <h1 class="h4 mb-0 text-white fw-bold"><i class="fas fa-file-import me-2 text-warning"></i>{{ __('Product Bulk Import') }}</h1>
        </div>
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 text-white-50 small">
            <span>{{ __('Import products and update inventory using Excel spreadsheets.') }}</span>
        </div>
    </div>
</div>

@if ($isRootShop)
    <div class="container-fluid mt-3">

        @if (session('success'))
            <div class="alert alert-success" role="alert">
                <h4 class="alert-heading">{{ __('Well done!') }}</h4>
                <p class="mb-0"><strong>{{ session('success') }}</strong></p>
                <hr>
                <p class="mb-0">
                    <a href="{{ route('shop.product.index') }}" class="btn btn-primary">
                        {{ __('View Products') }}
                    </a>
                </p>
            </div>
        @endif

        @if (session('errors_file'))
            <div class="alert alert-warning" role="alert">
                <p class="mb-0">
                    <i class="fa-solid fa-triangle-exclamation me-2"></i>
                    <a href="{{ route('shop.bulk-product-import.errors', session('errors_file')) }}">
                        {{ __('Download error report') }}
                    </a>
                </p>
            </div>
        @endif

        <div class="card my-3">
            <div class="card-body text-center">
                <h4 class="text-muted mb-3">
                    {{ __('Select Excel (xlsx) File to Import') }}
                </h4>

                <div class="mb-4">
                    <a href="{{ route('shop.bulk-product-export.demo') }}" class="btn btn-primary py-2">
                        <i class="fa-solid fa-download me-1"></i>
                        {{ __('Download Template') }}
                    </a>
                </div>

                <form action="{{ route('shop.bulk-product-import.store') }}" method="POST" enctype="multipart/form-data" id="bulkForm">
                    @csrf

                    <div class="drop-zone mx-auto">
                        <span class="drop-zone__prompt">
                            <div class="icon">
                                <i class="fa-solid fa-cloud-arrow-up"></i>
                            </div>
                            {{ __('Drop file here or click to upload') }}
                        </span>
                        <input name="file" type="file" class="drop-zone__input" accept=".xlsx">
                    </div>
                    @error('file')
                        <p class="text text-danger m-0">{{ $message }}</p>
                    @enderror

                    <button type="submit" class="btn btn-primary btn-lg mt-3 py-2">
                        <i class="fa-solid fa-file-import me-1"></i>
                        {{ __('Import') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
@endif
@endsection

@push('scripts')
    <script src="{{ asset('assets/scripts/drop-zone.js') }}"></script>
@endpush
