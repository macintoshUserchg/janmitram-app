@extends('layouts.app')
@section('content')
    <div class="d-flex align-items-center flex-wrap gap-3 justify-content-between px-3">
        <h4>{{ __('All Customers') }}</h4>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            <form action="{{ route('admin.customer.index') }}" method="GET" class="d-flex gap-2">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="{{ __('Search customer, phone, email...') }}" value="{{ request('search') }}" style="width: 220px;">
                </div>
                <button type="submit" class="btn btn-sm btn-primary">{{ __('Search') }}</button>
                @if(request('search'))
                    <a href="{{ route('admin.customer.index') }}" class="btn btn-sm btn-outline-secondary">{{ __('Reset') }}</a>
                @endif
            </form>

            @hasPermission('admin.customer.create')
                <a href="{{ route('admin.customer.create') }}" class="btn btn-sm py-2 btn-primary">
                    <i class="bi bi-patch-plus"></i>
                    {{ __('Add Customer') }}
                </a>
            @endhasPermission
        </div>
    </div>

    <div class="container-fluid mt-3">

        <div class="mb-3 card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table border table-responsive-lg">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 70px;">
                                    @include('admin.partials.sortable-header', ['label' => __('SL'), 'column' => 'id', 'route' => 'admin.customer.index', 'sort' => $sort ?? 'id', 'direction' => $direction ?? 'desc'])
                                </th>
                                <th>{{ __('Profile') }}</th>
                                <th style="min-width: 150px">
                                    @include('admin.partials.sortable-header', ['label' => __('Name'), 'column' => 'name', 'route' => 'admin.customer.index', 'sort' => $sort ?? 'id', 'direction' => $direction ?? 'desc'])
                                </th>
                                <th style="min-width: 130px">
                                    @include('admin.partials.sortable-header', ['label' => __('Phone'), 'column' => 'phone', 'route' => 'admin.customer.index', 'sort' => $sort ?? 'id', 'direction' => $direction ?? 'desc'])
                                </th>
                                <th style="min-width: 150px">
                                    @include('admin.partials.sortable-header', ['label' => __('Email'), 'column' => 'email', 'route' => 'admin.customer.index', 'sort' => $sort ?? 'id', 'direction' => $direction ?? 'desc'])
                                </th>
                                <th class="text-center" style="min-width: 110px">
                                    @include('admin.partials.sortable-header', ['label' => __('Gender'), 'column' => 'gender', 'route' => 'admin.customer.index', 'sort' => $sort ?? 'id', 'direction' => $direction ?? 'desc'])
                                </th>
                                <th class="text-center" style="min-width: 130px">
                                    @include('admin.partials.sortable-header', ['label' => __('Date of Birth'), 'column' => 'date_of_birth', 'route' => 'admin.customer.index', 'sort' => $sort ?? 'id', 'direction' => $direction ?? 'desc'])
                                </th>
                                <th class="text-center">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        @forelse($customers as $key => $customer)
                            <tr>
                                <td class="text-center">{{ ++$key }}</td>

                                <td>
                                    <img src="{{ $customer->thumbnail }}" width="50">
                                </td>

                                <td>{{ Str::limit($customer->fullName, 50, '...') }}</td>

                                <td>
                                    {{ $customer->phone ?? '--' }}
                                </td>

                                <td>
                                    {{ $customer->email ?? '--' }}
                                </td>

                                <td class="text-center">
                                    {{ $customer->gender ?? '--' }}
                                </td>

                                <td class="text-center">
                                    {{ $customer->date_of_birth ?? '--' }}
                                </td>

                                <td class="text-center">
                                    <div class="d-flex gap-2 justify-content-center">
                                        @hasPermission('admin.customer.edit')
                                            <a href="{{ route('admin.customer.edit', $customer->id) }}"
                                                class="btn btn-outline-primary circleIcon" data-bs-toggle="tooltip"
                                                data-bs-placement="left" data-bs-title="{{ __('Edit') }}">
                                                <img src="{{ asset('assets/icons-admin/edit.svg') }}" alt="edit"
                                                    loading="lazy" />
                                            </a>
                                        @endhasPermission

                                        @hasPermission('admin.customer.destroy')
                                            <a href="{{ route('admin.customer.destroy', $customer->id) }}"
                                                class="btn btn-outline-danger circleIcon deleteConfirm" data-bs-toggle="tooltip"
                                                data-bs-placement="left" data-bs-title="{{ __('Delete') }}">
                                                <img src="{{ asset('assets/icons-admin/trash.svg') }}" alt="delete"
                                                    loading="lazy" />
                                            </a>
                                        @endhasPermission

                                        @hasPermission('admin.customer.reset-password')
                                            <button type="button" class="btn btn-outline-info circleIcon"
                                                data-bs-toggle="tooltip" data-bs-placement="left"
                                                data-bs-title="{{ __('Reset Password') }}"
                                                onclick="openResetPasswordModal('{{ $customer->id }}','{{ $customer->fullName }}')">
                                                <img src="{{ asset('assets/icons-admin/role-permission.svg') }}" alt="key"
                                                    loading="lazy" />
                                            </button>
                                        @endhasPermission
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="text-center" colspan="100%">{{ __('No Data Found') }}</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="my-3">
            {{ $customers->withQueryString()->links() }}
        </div>

        <form action="" method="POST" id="resetPasswordForm">
            @csrf
            <div class="modal fade" id="resetPasswordModal" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title fs-5">{{ __('Reset Password') }} <span id="userName"></span></h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="password1" class="form-label">
                                    {{ __('Password') }}
                                </label>
                                <div class="position-relative passwordInput">
                                    <input type="password" name="password" id="password1" class="form-control"
                                        required="true" placeholder="Enter Password">
                                    <span class="eye" onclick="showHidePassword(1)">
                                        <i class="fa fa-eye-slash" id="togglePassword1"></i>
                                    </span>
                                </div>
                                @error('password')
                                    <p class="text text-danger m-0">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password2" class="form-label">
                                    {{ __('Confirm Password') }}
                                </label>
                                <div class="position-relative passwordInput">
                                    <input type="password" name="password_confirmation" id="password2" class="form-control"
                                        required="true" placeholder="Enter Password again">
                                    <span class="eye" onclick="showHidePassword(2)">
                                        <i class="fa fa-eye-slash" id="togglePassword2"></i>
                                    </span>
                                </div>
                                <span id="passwordMatch" class="text text-danger d-none"></span>
                                @error('password_confirmation')
                                    <p class="text text-danger m-0">{{ $message }}</p>
                                @enderror
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                {{ __('Close') }}
                            </button>
                            <button type="submit" id="submit" class="btn btn-primary">
                                {{ __('Save changes') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

    </div>
@endsection
@push('scripts')
    <script>
        function openResetPasswordModal(userId, userName) {
            $('#resetPasswordModal').modal('show');
            $('#userName').html('(' + userName + ')');
            $('#resetPasswordForm').attr('action', `{{ route('admin.customer.reset-password', ':id') }}`.replace(':id',
                userId));
        }

        function showHidePassword(num) {
            const toggle = document.getElementById("togglePassword" + num);
            const password = document.getElementById("password" + num);

            // toggle the type attribute
            const type = password.getAttribute("type") === "password" ? "text" : "password";
            password.setAttribute("type", type);
            // toggle the icon
            toggle.classList.toggle("fa-eye");
            toggle.classList.toggle("fa-eye-slash");
        }

        document.getElementById('password2').addEventListener('keyup', function(e) {
            $password1 = document.getElementById('password1').value;
            $password2 = document.getElementById('password2').value;

            $message = document.getElementById('passwordMatch');

            if ($password1 == $password2) {
                document.getElementById('password2').classList.remove('is-invalid');
                $message.classList.add('d-none');
                document.getElementById('submit').disabled = false;
            } else {
                document.getElementById('password2').classList.add('is-invalid');
                $message.classList.remove('d-none');
                $message.innerHTML = "Password doesn't match";
                document.getElementById('submit').disabled = true;
            }
        });
    </script>
@endpush
