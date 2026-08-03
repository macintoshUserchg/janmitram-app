<?php

namespace App\Http\Requests;

use App\Models\VerifyManage;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Cache;

class ShopCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user = null;
        $required = 'required';
        if ($this->routeIs('admin.shop.update')) {
            $user = $this->shop?->user;
            $required = 'nullable';
        }

        // KYC / bank fields are required only on the public shop registration and
        // admin shop create routes. Shared callers (admin update, API seller
        // register, downline create) do not collect KYC, so resolve to nullable.
        $kycRequired = $this->routeIs('shop.register.submit') || $this->routeIs('admin.shop.store')
            ? 'required' : 'nullable';

        $verifyManage = Cache::rememberForever('verify_manage', function () {
            return VerifyManage::first();
        });

        $phoneRequired = $verifyManage?->phone_required ? 'required' : 'nullable';
        $phoneRequired = $verifyManage ? $phoneRequired : 'required';

        $min = $verifyManage?->phone_min_length ?? 9;
        $max = $verifyManage?->phone_max_length ?? 16;

        $phoneValidate = [$phoneRequired, 'min_digits:'.$min, 'max_digits:'.$max, 'unique:users,phone,'.$user?->id];

        // validation rules
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'phone' => $phoneValidate,
            'email' => ['required', 'string', 'email:rfc', 'max:255', 'unique:users,email,'.$user?->id],
            'gender' => ['nullable', 'string'],
            'password' => [$required, 'min:6', 'confirmed'],
            'password_confirmation' => [$required, 'min:6'],
            'address' => ['nullable', 'string'],
            'profile_photo' => [$required, 'image', 'mimes:jpg,png,jpeg,gif', 'max:2048'],
            'shop_name' => ['required', 'string', 'max:100'],
            'shop_logo' => [$required, 'image', 'mimes:jpg,png,jpeg,gif', 'max:2048'],
            'shop_banner' => [$required, 'image', 'mimes:jpg,png,jpeg,gif', 'max:2048'],
            'description' => ['nullable', 'string', 'max:200'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'warehouse_id' => ['nullable', 'exists:warehouses,id'],
            'parent_shop_id' => ['nullable', 'exists:shops,id'],
            'ref' => ['nullable', 'string'],
            'sponsor_code' => ['nullable', 'string'],
            'date_of_birth' => [$kycRequired, 'date', 'before:today'],
            'aadhaar_card' => [$kycRequired, 'file', 'mimes:jpg,png,jpeg,gif,pdf', 'max:5120'],
            'aadhaar_number' => [$kycRequired, 'string', 'regex:/^[1-9]\d{11}$/'],
            'pan_card' => [$kycRequired, 'file', 'mimes:jpg,png,jpeg,gif,pdf', 'max:5120'],
            'pan_number' => [$kycRequired, 'string', 'regex:/^[A-Z]{5}[0-9]{4}[A-Z]$/'],
            'qualification' => [$kycRequired, 'string', 'max:255'],
            'bank_name' => [$kycRequired, 'string', 'max:255'],
            'ifsc' => [$kycRequired, 'string', 'regex:/^[A-Z]{4}0[A-Z0-9]{6}$/'],
            'account_number' => [$kycRequired, 'string', 'max:255'],
            'other_documents' => ['nullable', 'file', 'mimes:jpg,png,jpeg,gif,pdf', 'max:5120'],
        ];
    }

    /**
     * Normalize KYC input before validation: uppercase PAN/IFSC and strip
     * spaces from Aadhaar, so the format regexes match real user input.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('pan_number')) {
            $this->merge(['pan_number' => strtoupper(trim((string) $this->pan_number))]);
        }

        if ($this->has('ifsc')) {
            $this->merge(['ifsc' => strtoupper(trim((string) $this->ifsc))]);
        }

        if ($this->has('aadhaar_number')) {
            $this->merge(['aadhaar_number' => preg_replace('/\s+/', '', (string) $this->aadhaar_number)]);
        }
    }

    public function messages(): array
    {
        $request = request();
        if ($request->is('api/*')) {
            $header = strtolower($request->header('accept-language'));
            $lan = (preg_match('/^[a-z]+$/', $header)) ? $header : 'en';
            app()->setLocale($lan);
        }

        return [
            'first_name.required' => __('The first name field is required.'),
            'phone.required' => __('The phone field is required.'),
            'phone.unique' => __('The phone has already been taken.'),
            'email.required' => __('The email field is required.'),
            'email.unique' => __('The email has already been taken.'),
            'password.required' => __('The password field is required.'),
            'password.min' => __('The password must be at least 6 characters.'),
            'password.confirmed' => __('The password and confirmation password do not match.'),
            'profile_photo.image' => __('The profile photo must be an image.'),
            'profile_photo.max' => __('The profile photo must not be greater than 2 MB.'),
            'shop_name.required' => __('The shop name field is required.'),
            'shop_logo.image' => __('The shop logo must be an image.'),
            'shop_logo.max' => __('The shop logo must not be greater than 2 MB.'),
            'shop_banner.image' => __('The shop banner must be an image.'),
            'shop_banner.max' => __('The shop banner must not be greater than 2 MB.'),
            'description.max' => __('The description may not be greater than 200 characters.'),
            'password_confirmation.min' => __('The password confirmation must be at least 6 characters.'),
            'password_confirmation.required' => __('The password confirmation field is required.'),
            'address.max' => __('The address may not be greater than 255 characters.'),
            'date_of_birth.required' => __('The date of birth field is required.'),
            'date_of_birth.before' => __('The date of birth must be a valid date before today.'),
            'aadhaar_card.required' => __('The Aadhaar card document is required.'),
            'aadhaar_card.max' => __('The Aadhaar card document must not be greater than 5 MB.'),
            'aadhaar_number.required' => __('The Aadhaar number is required.'),
            'aadhaar_number.regex' => __('Please enter a valid 12-digit Aadhaar number.'),
            'pan_card.required' => __('The PAN card document is required.'),
            'pan_card.max' => __('The PAN card document must not be greater than 5 MB.'),
            'pan_number.required' => __('The PAN number is required.'),
            'pan_number.regex' => __('Please enter a valid 10-character PAN number (e.g. ABCDE1234F).'),
            'qualification.required' => __('The qualification field is required.'),
            'bank_name.required' => __('The bank name is required.'),
            'ifsc.required' => __('The IFSC code is required.'),
            'ifsc.regex' => __('Please enter a valid IFSC code (e.g. HDFC0001234).'),
            'account_number.required' => __('The bank account number is required.'),
            'other_documents.max' => __('The other documents file must not be greater than 5 MB.'),
        ];
    }
}
