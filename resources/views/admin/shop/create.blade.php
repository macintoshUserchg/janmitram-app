@extends('layouts.app')

@section('header-title', __('Add New Shop'))

@section('content')
    <div class="page-title">
        <div class="d-flex gap-2 align-items-center">
            <i class="fa-solid fa-shop"></i> {{ __('Add New Shop') }}
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
            <h6 class="alert-heading fw-bold mb-1"><i class="fas fa-exclamation-triangle me-2"></i>{{ __('Please correct the following errors:') }}</h6>
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form id="createShopForm" action="{{ route('admin.shop.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <!--######## User Information ##########-->
        <div class="card mt-3">
            <div class="card-body">

                <div class="d-flex gap-2 border-bottom pb-2">
                    <i class="fa-solid fa-user"></i>
                    <h5>
                        {{ __('User Information') }}
                    </h5>
                </div>

                <div class="row">
                    <div class="col-lg-6">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mt-3">
                                    <x-input label="First Name" name="first_name" type="text" placeholder="Enter Name"
                                        required="true" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mt-3">
                                    <x-input label="Last Name" name="last_name" type="text" placeholder="Enter Name" />
                                </div>
                            </div>
                        </div>

                        <div class="mt-3">
                            <x-input label="Phone Number" name="phone" type="number" placeholder="Enter phone number"
                                required="true" />
                        </div>

                        <div class="mt-3">
                            <x-select label="Gender" name="gender">
                                <option value="male">{{ __('Male') }}</option>
                                <option value="female">{{ __('Female') }}</option>
                            </x-select>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mt-3 d-flex align-items-center justify-content-center">
                            <div class="ratio1x1">
                                <img id="previewProfile" src="https://placehold.co/500x500/png" alt=""
                                    width="100%">
                            </div>
                        </div>
                        <div class="mt-3">
                            <x-file name="profile_photo" label="User profile (Ratio 1:1)" preview="previewProfile"
                                required="true" />
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!--######## Account Information ##########-->
        <div class="card mt-4">
            <div class="card-body">

                <div class="d-flex gap-2 border-bottom pb-2">
                    <i class="fa-solid fa-user"></i>
                    <h5>
                        {{ __('Account Information') }}
                    </h5>
                </div>
                <div class="row mt-3">
                    <div class="col-md-4">
                        <x-input type="email" name="email" label="Email" placeholder="Enter Email Address"
                            required="true" />
                    </div>

                    <div class="col-md-4 mt-3 mt-md-0">
                        <x-input type="password" name="password" label="Password" placeholder="Enter Password"
                            required="true" />
                    </div>

                    <div class="col-md-4 mt-3 mt-md-0">
                        <x-input type="password" name="password_confirmation" label="Confirm Password"
                            placeholder="Enter Confirm Password" required="true" />
                    </div>
                </div>
            </div>
        </div>

        <!--######## Shop Information ##########-->
        <div class="card mt-4 mb-4">
            <div class="card-body">

                <div class="d-flex gap-2 border-bottom pb-2">
                    <i class="fa-solid fa-user"></i>
                    <h5>
                        {{ __('Shop Information') }}
                    </h5>
                </div>
                <div class="row mt-3">
                    <div class="col-md-4">
                        <x-input type="text" name="shop_name" label="Shop Name" placeholder="Enter Shop Name"
                            required="true" />
                    </div>

                    <div class="col-md-4 mt-3 mt-md-0">
                        <x-input type="text" name="address" label="Address" placeholder="Enter Address" />
                    </div>

                    <div class="col-md-3 mt-3 mt-md-0">
                        <x-select label="Linked Warehouse" name="warehouse_id">
                            <option value="">{{ __('-- Central Hub (Default) --') }}</option>
                            @foreach($warehouses as $wh)
                                <option value="{{ $wh->id }}" {{ old('warehouse_id') == $wh->id ? 'selected' : '' }}>
                                    {{ $wh->name }} {{ $wh->is_default ? '('.__('Central Hub').')' : '' }}
                                </option>
                            @endforeach
                        </x-select>
                    </div>

                    <div class="col-md-3 mt-3 mt-md-0">
                        <x-select label="Sponsor / Parent Shop (MLM Tree)" name="parent_shop_id">
                            <option value="">{{ __('-- Root Node (No Sponsor) --') }}</option>
                            @foreach($parentShops ?? [] as $pShop)
                                @php
                                    $capacityText = $pShop->isMainShop()
                                        ? __(' [Main Shop: Unlimited]')
                                        : ($pShop->canAcceptDirectDownline()
                                            ? ' [' . $pShop->directDownlinesCount() . '/' . \App\Models\Shop::MAX_DIRECT_DOWNLINES . ' ' . __('downlines') . ']'
                                            : ' [' . __('Full: 10/10 capacity') . ']');
                                @endphp
                                <option value="{{ $pShop->id }}" {{ old('parent_shop_id') == $pShop->id ? 'selected' : '' }}>
                                    {{ $pShop->name }} ({{ $pShop->referral_code }}){{ $capacityText }}
                                </option>
                            @endforeach
                        </x-select>
                    </div>

                    <div class="col-md-6 mt-4">
                        <div class="d-flex align-items-center justify-content-center mb-2">
                            <div class="ratio1x1">
                                <img src="https://placehold.co/500x500/png" id="previewShopLogo" alt=""
                                    width="100%">
                            </div>
                        </div>
                        <x-file name="shop_logo" label="Shop logo(Ratio 1:1)" preview="previewShopLogo" required="true" />
                    </div>

                    <div class="col-md-6 mt-4">
                        <div class="d-flex align-items-center justify-content-center mb-2">
                            <div class="ratio4x1">
                                <img src="https://placehold.co/2000x500/png" id="shopBanner" alt=""
                                    width="100%">
                            </div>
                        </div>
                        <x-file name="shop_banner" label="Shop banner Ratio 4:1 (2000 x 500 px)" preview="shopBanner"
                            required="true" />
                    </div>
                </div>

                <div class="mt-3">
                    <label for="">
                        {{ __('Description') }}
                    </label>
                    <textarea name="description" class="form-control" id="description" rows="2" placeholder="Enter Description"
                        onkeyup="checkDescription()">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text text-danger m-0" id="errorDescription">{{ $message }}</p>
                    @enderror
                    <p class="text text-danger m-0" id="descriptionError"></p>
                </div>

                <div class="row mt-3">
                    <div class="col-md-6">
                        <x-input type="text" id="latitude" name="latitude" label="Latitude (Optional)" placeholder="e.g. 27.0056949" :value="old('latitude', '27.005694931660006')" />
                    </div>
                    <div class="col-md-6">
                        <x-input type="text" id="longitude" name="longitude" label="Longitude (Optional)" placeholder="e.g. 75.7775497" :value="old('longitude', '75.77754972401056')" />
                    </div>
                </div>
                <div id="map" style="height:450px; border-radius: 10px;margin-top: 20px"></div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" id="submitBtn" class="btn btn-primary py-2.5 px-5 fw-semibold shadow-sm d-inline-flex align-items-center justify-content-center gap-2">
                        <i class="fas fa-paper-plane me-1"></i>
                        <span>{{ __('Submit Shop') }}</span>
                    </button>
                </div>

            </div>
        </div>

        <!--######## KYC & Bank Details ##########-->
        <div class="card mt-4 mb-4">
            <div class="card-body">

                <div class="d-flex gap-2 border-bottom pb-2">
                    <i class="fa-solid fa-id-card"></i>
                    <h5>
                        {{ __('KYC & Bank Details') }}
                    </h5>
                </div>
                <div class="row mt-3">
                    <div class="col-md-4">
                        <div class="mt-3">
                            <x-file name="aadhaar_card" label="Aadhaar Card (JPG / PNG / PDF, max 5MB)" required="true" />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mt-3">
                            <x-input type="text" name="aadhaar_number" label="Aadhaar Number" placeholder="12-digit Aadhaar number" required="true" />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mt-3">
                            <x-file name="pan_card" label="PAN Card (JPG / PNG / PDF, max 5MB)" required="true" />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mt-3">
                            <x-input type="text" name="pan_number" label="PAN Number" placeholder="e.g. ABCDE1234F" required="true" />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mt-3">
                            <x-input type="date" name="date_of_birth" label="Date of Birth" required="true" />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mt-3">
                            <x-input type="text" name="qualification" label="Qualification" placeholder="e.g. B.Com, 12th Pass" required="true" />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mt-3">
                            <x-input type="text" name="bank_name" label="Bank Name" placeholder="Enter bank name" required="true" />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mt-3">
                            <x-input type="text" name="ifsc" label="Bank IFSC Code" placeholder="e.g. HDFC0001234" required="true" />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mt-3">
                            <x-input type="text" name="account_number" label="Bank Account Number" placeholder="Enter account number" required="true" />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mt-3">
                            <x-file name="other_documents" label="Other Documents (JPG / PNG / PDF, max 5MB, optional)" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
@push('scripts')
    <script>
        function checkDescription() {
            var errDescription = document.getElementById('errorDescription');
            if (errDescription) {
                errDescription.remove();
            }

            if (document.getElementById('description').value.length > 200) {
                document.getElementById('descriptionError').innerHTML =
                    'Description must be less than or equal to 200 characters';
            } else {
                document.getElementById('descriptionError').innerHTML = '';
            }
        }
    </script>
    <script>
        $(document).ready(function() {
            window.janmitramMapObj = initJanmitramMap({
                containerId: 'map',
                lat: {{ old('latitude', 27.005694931660006) }},
                lng: {{ old('longitude', 75.77754972401056) }},
                iconUrl: '{{ asset('assets/icons/home.png') }}',
                latInputId: 'latitude',
                lngInputId: 'longitude'
            });

            $('#createShopForm').on('submit', function() {
                if (this.checkValidity()) {
                    $('#submitBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> {{ __("Submitting Shop...") }}');
                }
            });
        });
    </script>
@endpush
