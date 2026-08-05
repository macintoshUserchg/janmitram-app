@extends('layouts.app')

@section('header-title', __('Profile Details'))

@section('content')
<!-- Integrated 2-Line Compact Header Banner Card -->
<div class="card border-0 shadow-sm rounded-12 mb-4 bg-gradient bg-primary text-white">
    <div class="card-body p-3">
        <div class="d-flex justify-content-between align-items-center mb-1">
            <h1 class="h4 mb-0 text-white fw-bold"><i class="fas fa-store me-2 text-warning"></i>{{ __('My Shop Profile') }}</h1>
            <a href="{{ route('shop.profile.edit', $shop->id) }}" class="btn btn-sm btn-light text-primary fw-bold">
                <i class="fas fa-edit me-1"></i> {{ __('Edit Profile') }}
            </a>
        </div>
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 text-white-50 small">
            <span>{{ __('Manage shop settings, business information, and location details.') }}</span>
        </div>
    </div>
</div>

    <div class="row mb-3">
        <div class="col-lg-8 mt-3">
            <div class="card rounded-12 position-relative overflow-hidden">
                <div class="card-body shop details p-2 border-bottom pb-3">
                    <div class="banner position-relative">
                        <img class="img-fit" src="{{ $shop->banner }}" />
                    </div>
                    <a href="{{ route('shop.profile.edit', $shop->id) }}" class="editBtn svg-bg">
                        <img src="{{ asset('assets/icons-admin/edit.svg') }}" alt="edit" loading="lazy" />
                        <span>{{ __('Edit') }}</span>
                    </a>
                    <div class="main-content d-flex align-items-start align-items-md-center flex-column flex-md-row gap-2">
                        <div class="logo">
                            <img class="img-fit" src="{{ $shop->logo }}" />
                        </div>
                        <div class="personal">
                            <span class="name h4 mb-1">{{ $shop->name }}</span>
                            <div class="d-flex gap-2 align-items-center ">
                                <div>
                                    @foreach (range(1, 5) as $rating)
                                        @if ($shop->averageRating >= $rating)
                                            <i class="fa-solid fa-star text-warning"></i>
                                        @else
                                            <i class="fa-regular fa-star text-secondary"></i>
                                        @endif
                                    @endforeach
                                </div>
                                <div>
                                    <span class="fw-bold">{{ $shop->averageRating }}</span>
                                    ({{ $shop->reviews->count() }} {{ __('Reviews') }})
                                </div>
                            </div>
                            <div class="mt-2">
                                <a href="{{ url('shops/'.$shop->id) }}" target="blank"
                                    class="btn btn-outline-primary btn-sm">
                                    {{ __('View Live') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <h4 class="m-0 p-3 border-bottom">{{ __('User Information') }}</h4>
                <div class="card-body pt-0" style="overflow-x:auto">
                    <table class="table mb-0 table-responsive">
                        <tr>
                            <td style="width: 180px">{{ __('Name') }}:</td>
                            <td>{{ $shop->user?->name }}</td>
                        </tr>
                        <tr>
                            <td style="width: 180px">{{ __('Phone') }}:</td>
                            <td>{{ $shop->user?->phone }}</td>
                        </tr>
                        <tr>
                            <td style="width: 180px">{{ __('Email') }}:</td>
                            <td>{{ $shop->user?->email }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="card mt-3">
                <h4 class="m-0 p-3 border-bottom">{{ __('Shop Information') }}</h4>
                <div class="card-body pt-0" style="overflow-x:auto">
                    <table class="table mb-0 table-responsive">
                        <tr>
                            <td style="width: 180px">{{ __('Name') }}:</td>
                            <td>{{ $shop->name }}</td>
                        </tr>
                        <tr>
                            <td style="width: 180px">{{ __('Referral Code') }}:</td>
                            <td>
                                <span class="badge bg-primary text-white font-monospace fs-6 me-2">{{ $shop->referral_code }}</span>
                                <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" onclick="navigator.clipboard.writeText('{{ $shop->referral_code }}'); alert('{{ __('Referral code copied!') }}');">
                                    <i class="fa-regular fa-copy me-1"></i>{{ __('Copy Code') }}
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 180px">{{ __('Referral Link') }}:</td>
                            <td>
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <a href="{{ $shop->referral_url }}" target="_blank" class="text-truncate" style="max-width: 320px;">
                                        {{ $shop->referral_url }}
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" onclick="navigator.clipboard.writeText('{{ $shop->referral_url }}'); alert('{{ __('Referral link copied!') }}');">
                                        <i class="fa-regular fa-copy me-1"></i>{{ __('Copy Link') }}
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @if ($shop->parent)
                            <tr>
                                <td style="width: 180px">{{ __('Sponsor / Parent Shop') }}:</td>
                                <td>
                                    <span class="fw-bold">{{ $shop->parent->name }} ({{ $shop->parent->referral_code }})</span>
                                </td>
                            </tr>
                        @endif
                        <tr>
                            <td style="width: 180px">{{ __('Estimated Delivery') }}:</td>
                            <td>{{ $shop->estimated_delivery_time }}</td>
                        </tr>
                        <tr>
                            <td style="width: 180px">{{ __('Shop Description') }}:</td>
                            <td>{{ $shop->description }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-4 mt-3">
            <div class="card h-100">
                <h4 class="m-0 p-3 border-bottom">{{ __('Product Information') }}</h4>
                <div class="card-body pt-0" style="overflow-x:auto">
                    <table class="table mb-0 table-responsive">
                        <tr>
                            <td style="width: 180px">{{ __('Total Products') }}:</td>
                            <td>
                                <span class="fw-bold">{{ $shop->products->count() }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 180px">{{ __('Total Orders') }}:</td>
                            <td>
                                <span class="fw-bold">{{ $shop->orders->count() }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 180px; text-transform: capitalize">{{ __('reviews') }}</td>
                            <td>
                                <span class="fw-bold">{{ $shop->reviews->count() }}</span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
