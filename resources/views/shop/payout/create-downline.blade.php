@extends('layouts.app')

@section('header-title', __('Register New Downline Partner'))
@section('header-subtitle', __('Directly add a new shop owner under your network node.'))

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border-0 shadow-sm rounded-12 overflow-hidden mb-4">
                <div class="card-header bg-primary text-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 fw-bold">
                        <i class="fas fa-user-plus me-2"></i> {{ __('New Downline Partner Registration') }}
                    </h5>
                    <a href="{{ route('shop.payout.network') }}" class="btn btn-sm btn-light shadow-sm">
                        <i class="fas fa-arrow-left me-1"></i> {{ __('Back to Downline Tree') }}
                    </a>
                </div>
                <div class="card-body p-4">

                    <!-- Sponsor Confirmation Card -->
                    <div class="alert alert-info border-0 shadow-sm d-flex align-items-center mb-4 rounded-12 p-3">
                        <div class="fs-3 me-3 text-primary">
                            <i class="fas fa-sitemap"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark fs-6">{{ __('Locked Sponsor Node') }}: {{ $shop->name }} (#{{ $shop->id }})</div>
                            <div class="small text-muted">
                                {{ __('Referral Code') }}: <span class="badge bg-primary px-2 py-1">{{ $shop->referral_code }}</span> | {{ __('This new shop will be automatically placed in your direct Level 1 downline.') }}
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('shop.payout.network.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- User & Account Information -->
                        <h6 class="fw-bold text-uppercase text-secondary mb-3 border-bottom pb-2">
                            <i class="fas fa-user-circle me-1"></i> {{ __('1. Partner Account Information') }}
                        </h6>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">{{ __('First Name') }} <span class="text-danger">*</span></label>
                                <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror" value="{{ old('first_name') }}" placeholder="e.g. Rahul" required>
                                @error('first_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">{{ __('Last Name') }}</label>
                                <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror" value="{{ old('last_name') }}" placeholder="e.g. Sharma">
                                @error('last_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">{{ __('Email Address') }} <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="e.g. rahul@example.com" required>
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">{{ __('Mobile Phone Number') }} <span class="text-danger">*</span></label>
                                <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" placeholder="e.g. 9876543210" required>
                                @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">{{ __('Password') }} <span class="text-danger">*</span></label>
                                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Minimum 6 characters" required>
                                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">{{ __('Confirm Password') }} <span class="text-danger">*</span></label>
                                <input type="password" name="password_confirmation" class="form-control" placeholder="Re-enter password" required>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-semibold">{{ __('Profile Photo') }} <span class="text-danger">*</span></label>
                                <input type="file" name="profile_photo" class="form-control @error('profile_photo') is-invalid @enderror" accept="image/*" required>
                                @error('profile_photo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <!-- Shop Details -->
                        <h6 class="fw-bold text-uppercase text-secondary mb-3 border-bottom pb-2">
                            <i class="fas fa-store me-1"></i> {{ __('2. Shop Profile & Location') }}
                        </h6>

                        <div class="row g-3 mb-4">
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">{{ __('Downline Shop Name') }} <span class="text-danger">*</span></label>
                                <input type="text" name="shop_name" class="form-control @error('shop_name') is-invalid @enderror" value="{{ old('shop_name') }}" placeholder="e.g. Janmitram Express Store" required>
                                @error('shop_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-semibold">{{ __('Shop Address') }}</label>
                                <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="2" placeholder="Full shop physical address">{{ old('address') }}</textarea>
                                @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">{{ __('Shop Logo') }} <span class="text-danger">*</span></label>
                                <input type="file" name="shop_logo" class="form-control @error('shop_logo') is-invalid @enderror" accept="image/*" required>
                                @error('shop_logo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">{{ __('Shop Banner') }} <span class="text-danger">*</span></label>
                                <input type="file" name="shop_banner" class="form-control @error('shop_banner') is-invalid @enderror" accept="image/*" required>
                                @error('shop_banner') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <input type="hidden" name="latitude" value="{{ old('latitude', '28.6139') }}">
                            <input type="hidden" name="longitude" value="{{ old('longitude', '77.2090') }}">
                        </div>

                        <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                            <a href="{{ route('shop.payout.network') }}" class="btn btn-outline-secondary px-4">
                                {{ __('Cancel') }}
                            </a>
                            <button type="submit" class="btn btn-primary px-5 fw-semibold shadow-sm">
                                <i class="fas fa-check-circle me-1"></i> {{ __('Register & Add Downline Shop') }}
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
