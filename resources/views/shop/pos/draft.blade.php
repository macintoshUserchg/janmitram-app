@extends('layouts.app')

@section('header-title', __('Draft'))

@section('content')
<!-- Integrated 2-Line Compact Header Banner Card -->
<div class="card border-0 shadow-sm rounded-12 mb-4 bg-gradient bg-primary text-white">
    <div class="card-body p-3">
        <div class="d-flex justify-content-between align-items-center mb-1">
            <h1 class="h4 mb-0 text-white fw-bold"><i class="fas fa-file-invoice me-2 text-warning"></i>{{ __('POS Draft Orders') }}</h1>
            <a href="{{ route('shop.pos.index') }}" class="btn btn-sm btn-warning text-dark fw-bold">
                <i class="fas fa-cash-register me-1"></i> {{ __('Open POS Counter') }}
            </a>
        </div>
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 text-white-50 small">
            <span>{{ __('Resume and manage saved POS counter transactions.') }}</span>
        </div>
    </div>
</div>

    <div class="card">
        <div class="card-body">

            <div class="cardTitleBox">
                <h5 class="card-title chartTitle">
                    {{ __('Draft Items') }}
                </h5>
            </div>

            <div class="table-responsive">
                <table class="table table-responsive-lg">
                    <thead>
                        <tr>
                            <th>{{ __('SL') }}</th>
                            <th>{{ __('Created Date') }}</th>
                            <th>{{ __('Customer') }}</th>
                            <th>{{ __('Total Products') }}</th>
                            <th>{{ __('Sub Total') }}</th>
                            <th>{{ __('Discount') }}</th>
                            <th>{{ __('Total') }}</th>
                            <th>{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($posCarts as $posCart)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    {{ $posCart->created_at->format('d M Y, h:i A') }}
                                    <br>
                                    <small>
                                        {{ $posCart->created_at->diffForHumans() }}
                                    </small>
                                </td>
                                <td>
                                    {{ $posCart?->user?->name ?? 'N/A' }}
                                </td>
                                <td>
                                    <span class="badge bg-primary">
                                        {{ $posCart->products->count() }}
                                    </span>
                                    {{ __('Items') }}
                                </td>
                                <td>
                                    {{ showCurrency($posCart->subtotal) }}
                                </td>
                                <td>
                                    {{ showCurrency($posCart->discount) }}
                                </td>
                                <td>
                                    {{ showCurrency($posCart->total) }}
                                </td>
                                <td>
                                    <a href="{{ route('shop.pos.index', 'name=' . $posCart->name) }}"
                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                        data-bs-title="{{ __('Edit') }}" class="circleIcon btn-outline-info">
                                        <img src="{{ asset('assets/icons-admin/edit.svg') }}" alt="view"
                                            loading="lazy" />
                                    </a>

                                    <a href="{{ route('shop.pos.draft.delete', $posCart->id) }}"
                                        class="circleIcon btn-outline-danger deleteConfirm">
                                        <img src="{{ asset('assets/icons-admin/trash.svg') }}" alt="view" loading="lazy" />
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>

        </div>
    </div>

@endsection
