<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Become A Seller - {{ config('app.name', 'Janmitram') }}</title>

    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/leaflet.css') }}">
</head>
<style>
    body {
        background-color: #FFFFFF !important;
    }

    .wrapper {
        min-height: 100svh;
        display: flex;
    }

    .promotionSection {
        width: 35%;
        background-image: url("{{ asset('assets/images/shop-register.png') }}");
        background-position: center;
        background-repeat: no-repeat;
        background-size: cover;
    }

    .registerFormSection {
        width: 65%;
        display: flex;
        flex-direction: column;
        row-gap: 24px;
    }

    @media (max-width: 767px) {
        .wrapper {
            flex-direction: column;
        }

        .promotionSection {
            display: none;
        }

        .registerFormSection {
            width: 100%;
        }
    }

    .step-indicators {
        display: flex;
        column-gap: 32px;
    }

    .indicator {
        width: 32px;
        height: 32px;
        padding: 4px;
        border-width: 1px;
        border-style: solid;
        border-color: var(--bs-border-color);
        border-radius: 50%;
    }

    .indicator.active {
        border-color: var(--theme-color);
    }

    .indicator-devider {
        width: 100%;
        top: 16px;
        left: 32px;
        border-bottom-width: 2px;
        border-bottom-style: dashed;
        border-color: var(--bs-border-color);
    }

    .step {
        display: flex;
        flex-direction: column;
        row-gap: 32px;
    }

    .information {
        position: relative;
        display: flex;
        flex-direction: column;
        padding: 32px 16px 16px;
        gap: 20px;
        isolation: isolate;
        border: 1px solid #D7DAE0;
        border-radius: 16px;
    }

    .title {
        font-weight: 500;
        font-size: 24px;
        line-height: 32px;
        padding: 0px 2px;
        position: absolute;
        left: 64px;
        top: -16px;
        background: #FFFFFF;
    }
</style>

<body>

    <div class="wrapper">
        <div class="promotionSection">
        </div>

        <div class="registerFormSection ps-3 pe-3 pe-md-0 py-4">
            <div class="d-flex column-gap-2 align-items-center d-none" id="backBtn" style="cursor: pointer">
                <i class="fa fa-arrow-left"></i>Back
            </div>
            <div class="d-flex flex-column" style="row-gap: 40px">
                <div class="d-flex justify-content-between align-items-center pe-md-4 pb-2 border-bottom">
                    <h3 class="mb-0" style="font-weight: 600">
                        </i> {{ __('Register as a seller') }}
                    </h3>
                    <div class="step-indicators">
                        <div class="position-relative">
                            <div class="indicator active d-flex justify-content-center align-items-center"
                                id="indicator1">
                                1
                            </div>
                            User information
                            <div class="indicator-devider position-absolute">
                            </div>
                        </div>
                        <div>
                            <div class="indicator d-flex justify-content-center align-items-center" id="indicator2">
                                2
                            </div>
                            Shop information
                        </div>
                    </div>
                </div>
                <div class="pe-md-4">
                    <form action="{{ route('shop.register.submit') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="step" id="step1">
                            <div style="display: flex; flex-direction: column; row-gap: 24px">
                                <div class="information">
                                    <div class="title">
                                        {{ __('User Information') }}
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="col-md-7">
                                                    <x-file name="profile_photo" label="User profile (Ratio 1:1)"
                                                        preview="previewProfile" required="true" />
                                                    <p class="text text-danger m-0" id="profile_photo_error"></p>
                                                </div>
                                                <div class="col-md-5 mt-4 mt-md-0">
                                                    <div class="d-flex align-items-center justify-content-center">
                                                        <div class="ratio1x1">
                                                            <img id="previewProfile"
                                                                src="https://placehold.co/500x500/png" alt=""
                                                                width="100%">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mt-3">
                                                        <x-input label="First Name" name="first_name" type="text"
                                                            placeholder="Enter Name" required="true" />
                                                        <p class="text text-danger m-0" id="first_name_error"></p>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mt-3">
                                                        <x-input label="Last Name" name="last_name" type="text"
                                                            placeholder="Enter Name" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-me-12">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mt-3">
                                                        <x-input label="Phone Number" name="phone" type="number"
                                                            placeholder="Enter phone number" required="true" />
                                                        <p class="text text-danger m-0" id="phone_error"></p>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mt-3">
                                                        <x-select label="Gender" name="gender">
                                                            <option value="male">{{ __('Male') }}</option>
                                                            <option value="female">{{ __('Female') }}</option>
                                                        </x-select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="information">
                                    <div class="title">
                                        {{ __('Account Information') }}
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <x-input type="email" name="email" label="Email Address"
                                                placeholder="Enter Email Address" required="true" />
                                            <p class="text text-danger m-0" id="email_error"></p>
                                        </div>
                                        <div class="col-md-12">
                                            {{-- <div class="row">
                                                <div class="col-md-6 mt-3">
                                                    <x-input type="password" name="password" label="Password"
                                                        placeholder="Enter Password" required="true" />
                                                    <p class="text text-danger m-0" id="password_error"></p>
                                                </div>
                                                <div class="col-md-6 mt-3">
                                                    <x-input type="password" name="password_confirmation"
                                                        label="Confirm Password" placeholder="Enter Confirm Password"
                                                        required="true" />
                                                    <p class="text text-danger m-0" id="password_confirmation_error">
                                                    </p>
                                                </div>
                                            </div> --}}
                                            <div class="row">
                                                <div class="col-md-6 mt-3">
                                                    <label class="form-label">Password</label>
                                                    <div class="input-group">
                                                        <input type="password" name="password" id="password"
                                                            class="form-control" placeholder="Enter Password"
                                                            required>
                                                        <span class="input-group-text" style="cursor:pointer"
                                                            onclick="togglePassword('password','toggleIcon1')">
                                                            <i class="fa fa-eye" id="toggleIcon1"></i>
                                                        </span>
                                                    </div>
                                                    <p class="text text-danger m-0" id="password_error"></p>
                                                </div>
                                                <div class="col-md-6 mt-3">
                                                    <label class="form-label">Confirm Password</label>
                                                    <div class="input-group">
                                                        <input type="password" name="password_confirmation"
                                                            id="password_confirmation" class="form-control"
                                                            placeholder="Enter Confirm Password" required>
                                                        <span class="input-group-text" style="cursor:pointer"
                                                            onclick="togglePassword('password_confirmation','toggleIcon2')">
                                                            <i class="fa fa-eye" id="toggleIcon2"></i>
                                                        </span>
                                                    </div>
                                                    <p class="text text-danger m-0" id="password_confirmation_error">
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-primary py-2.5" id="nextBtn">Next</button>
                        </div>
                        <div class="step" id="step2" style="display: none">
                            <div class="information">
                                <div class="title">
                                    {{ __('Shop Information') }}
                                </div>
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <div class="p-3 border rounded-3 bg-light">
                                            <label class="form-label fw-bold text-dark mb-1">
                                                <i class="fa fa-sitemap me-1 text-primary"></i> {{ __('Sponsor / Referral Code (Optional)') }}
                                            </label>
                                            <input type="text" name="ref" id="sponsor_ref_input" class="form-control"
                                                placeholder="e.g. JAN-00002"
                                                value="{{ old('ref', $sponsorCode ?? request()->query('ref')) }}">
                                            @if(isset($sponsorShop) && $sponsorShop)
                                                <div class="mt-2 text-success small fw-semibold" id="sponsor_info_badge">
                                                    <i class="fa fa-check-circle me-1"></i> {{ __('Sponsor Network Partner') }}: <strong>{{ $sponsorShop->name }}</strong> (#{{ $sponsorShop->id }})
                                                </div>
                                            @else
                                                <div class="form-text text-muted small mt-1">
                                                    {{ __('Enter the Referral Code (e.g. JAN-00002) of the partner who referred you.') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <x-input type="text" name="shop_name" label="Shop Name"
                                            placeholder="Enter Shop Name" required="true" />
                                    </div>

                                    <div class="col-md-12 mt-4">
                                        <x-input type="text" name="address" label="Address"
                                            placeholder="Enter Address" />
                                    </div>

                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-7 mt-4">
                                                <x-file name="shop_logo" label="Shop Profile Image ( Ratio 1:1 )"
                                                    preview="previewShopLogo" required="true" />
                                            </div>
                                            <div class="col-md-5 mt-4">
                                                <div class="d-flex align-items-center justify-content-center">
                                                    <div class="ratio1x1">
                                                        <img src="https://placehold.co/500x500/png"
                                                            id="previewShopLogo" alt="" width="100%">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12 mt-4">
                                        <x-file name="shop_banner" label="Shop banner Ratio 4:1 (2000 x 500 px)"
                                            preview="shopBanner" required="true" />
                                        <div class="d-flex align-items-center justify-content-center mt-2">
                                            <div class="ratio4x1">
                                                <img src="https://placehold.co/2000x500/png" id="shopBanner"
                                                    alt="" width="100%">
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" id="latitude" name="latitude"
                                        value="{{ old('latitude') }}">
                                    <input type="hidden" id="longitude" name="longitude"
                                        value="{{ old('longitude') }}">
                                    <div id="map" style="height:400px; border-radius: 10px;margin-top: 20px">
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary py-2.5">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/scripts/jquery-3.6.3.min.js') }}"></script>
    <script src="{{ asset('assets/scripts/leaflet.js') }}"></script>
    <script src="{{ asset('assets/scripts/janmitram-map-helper.js') }}"></script>
    <script>
        $(function() {
            $('#nextBtn').on('click', function() {
                if (!validateStep()) {
                    return;
                }

                $('#step1').hide();
                $('#step2').show();
                $('#backBtn').removeClass('d-none');
                $('#indicator1').removeClass('active');
                $('#indicator2').addClass('active');

                if (window.janmitramMapObj) {
                    window.janmitramMapObj.invalidateSize();
                }
            });

            $('#backBtn').on('click', function() {
                $('#step2').hide();
                $(this).addClass('d-none');;
                $('#step1').show();
                $('#indicator2').removeClass('active');
                $('#indicator1').addClass('active');
            });

            $('#step1 input[required]').on('input', function() {
                $(this).removeClass('is-invalid');
                $('#' + $(this).attr('name') + '_error').text('')
            });

            @if($errors->has('shop_name') || $errors->has('shop_logo') || $errors->has('shop_banner') || $errors->has('address') || $errors->has('description'))
                $('#step1').hide();
                $('#step2').show();
                $('#backBtn').removeClass('d-none');
                $('#indicator1').removeClass('active');
                $('#indicator2').addClass('active');
                if (window.janmitramMapObj) {
                    window.janmitramMapObj.invalidateSize();
                }
            @endif
        });

        function validateStep() {
            let valid = true;

            const profilePhoto = $('input[name=profile_photo]');
            const firstName = $('input[name=first_name]');
            const phone = $('input[name=phone]');
            const email = $('input[name=email]');
            const password = $('input[name=password]');
            const passwordConfirmation = $('input[name=password_confirmation]');

            function setError(input, errorId, message) {
                $(errorId).text(message);
                input.addClass('is-invalid');
                valid = false;
            }

            function clearError(input, errorId) {
                $(errorId).text('');
                input.removeClass('is-invalid');
            }

            if (!profilePhoto.val()) {
                setError(profilePhoto, '#profile_photo_error', '{{ __("Profile photo is required.") }}');
            } else {
                clearError(profilePhoto, '#profile_photo_error');
            }

            if (!firstName.val().trim()) {
                setError(firstName, '#first_name_error', '{{ __("First name is required.") }}');
            } else {
                clearError(firstName, '#first_name_error');
            }

            const phoneVal = phone.val().trim();
            const phoneRegex = /^\d{9,16}$/;
            if (!phoneVal) {
                setError(phone, '#phone_error', '{{ __("Phone number is required.") }}');
            } else if (!phoneRegex.test(phoneVal)) {
                setError(phone, '#phone_error', '{{ __("Please enter a valid phone number (9-16 digits).") }}');
            } else {
                clearError(phone, '#phone_error');
            }

            const emailVal = email.val().trim();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailVal) {
                setError(email, '#email_error', '{{ __("Email address is required.") }}');
            } else if (!emailRegex.test(emailVal)) {
                setError(email, '#email_error', '{{ __("Please enter a valid email address (e.g. name@domain.com).") }}');
            } else {
                clearError(email, '#email_error');
            }

            if (!password.val()) {
                setError(password, '#password_error', '{{ __("Password is required.") }}');
            } else if (password.val().length < 6) {
                setError(password, '#password_error', '{{ __("Password must be at least 6 characters long.") }}');
            } else {
                clearError(password, '#password_error');
            }

            if (!passwordConfirmation.val()) {
                setError(passwordConfirmation, '#password_confirmation_error', '{{ __("Password confirmation is required.") }}');
            } else if (password.val() !== passwordConfirmation.val()) {
                setError(passwordConfirmation, '#password_confirmation_error', '{{ __("Passwords do not match.") }}');
            } else {
                clearError(passwordConfirmation, '#password_confirmation_error');
            }

            return valid;
        }

        function validateStep2() {
            let valid = true;
            const shopName = $('input[name=shop_name]');
            const shopLogo = $('input[name=shop_logo]');
            const shopBanner = $('input[name=shop_banner]');

            function setError(input, errorId, message) {
                if ($(errorId).length) {
                    $(errorId).text(message);
                } else {
                    if (!input.next('.step2-err').length) {
                        input.after('<p class="text text-danger m-0 step2-err">' + message + '</p>');
                    }
                }
                input.addClass('is-invalid');
                valid = false;
            }

            function clearError(input, errorId) {
                if ($(errorId).length) {
                    $(errorId).text('');
                }
                input.next('.step2-err').remove();
                input.removeClass('is-invalid');
            }

            if (!shopName.val().trim()) {
                setError(shopName, '#shop_name_error', '{{ __("Shop name is required.") }}');
            } else {
                clearError(shopName, '#shop_name_error');
            }

            if (!shopLogo.val() && !$('#previewShopLogo').attr('src').includes('http')) {
                setError(shopLogo, '#shop_logo_error', '{{ __("Shop logo is required.") }}');
            } else {
                clearError(shopLogo, '#shop_logo_error');
            }

            if (!shopBanner.val() && !$('#shopBanner').attr('src').includes('http')) {
                setError(shopBanner, '#shop_banner_error', '{{ __("Shop banner is required.") }}');
            } else {
                clearError(shopBanner, '#shop_banner_error');
            }

            return valid;
        }

        $(document).ready(function() {
            $('form').on('submit', function(e) {
                if (!validateStep() || !validateStep2()) {
                    e.preventDefault();
                    if (!validateStep()) {
                        $('#step2').hide();
                        $('#step1').show();
                        $('#indicator2').removeClass('active');
                        $('#indicator1').addClass('active');
                    }
                    return false;
                }
            });

            $('#sponsor_ref_input').on('input change', function() {
                var code = $(this).val().trim();
                var badge = $('#sponsor_info_badge');

                if (!code) {
                    badge.html('');
                    return;
                }

                $.get('{{ route("shop.verify-sponsor") }}', { code: code }, function(res) {
                    if (res.valid) {
                        badge.removeClass('text-danger text-muted').addClass('text-success').html('<i class="fa fa-check-circle me-1"></i> ' + res.message);
                    } else {
                        badge.removeClass('text-success text-muted').addClass('text-danger').html('<i class="fa fa-exclamation-triangle me-1"></i> ' + (res.message || '{{ __("Sponsor code not found") }}'));
                    }
                });
            });
        });

        var previewFile = (event, previewID) => {
            var reader = new FileReader();
            reader.onload = function() {
                var output = document.getElementById(previewID);
                output.src = reader.result;
            };
            reader.readAsDataURL(event.target.files[0]);
        };

        function checkDescription() {
            if (document.getElementById('description').value.length > 200) {
                document.getElementById('descriptionError').innerHTML =
                    'Description must be less than or equal to 220 characters';
            } else {
                document.getElementById('descriptionError').innerHTML = '';
            }
        }
    </script>
    <script>
        $(document).ready(function() {
            window.janmitramMapObj = initJanmitramMap({
                containerId: 'map',
                lat: {{ old('latitude', 28.6139) }},
                lng: {{ old('longitude', 77.2090) }},
                iconUrl: '{{ asset('assets/icons/home.png') }}',
                latInputId: 'latitude',
                lngInputId: 'longitude'
            });
        });
    </script>
    <script>
        function togglePassword(fieldId, iconId) {
            let field = document.getElementById(fieldId);
            let icon = document.getElementById(iconId);

            if (field.type === "password") {
                field.type = "text";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            } else {
                field.type = "password";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            }
        }
    </script>
</body>

</html>
