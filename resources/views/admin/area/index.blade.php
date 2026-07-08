@extends('layouts.app')
@section('content')
    <div class="d-flex align-items-center flex-wrap gap-3 justify-content-between px-3">
        <h4>
            {{ __('Area List') }}
        </h4>
    </div>

    <div class="container-fluid mt-3">

        <div class="mb-3 card">
            <div class="card-body">

                <form action="" class="d-flex align-items-center justify-content-between gap-3 mb-3 flex-column flex-md-row">
                    <div class="input-group" style="max-width: 400px">
                        <input type="text" name="search" class="form-control" placeholder="{{ __('Search by name') }}"
                            value="{{ request('search') }}">
                        <button type="submit" class="input-group-text btn btn-primary">
                            <i class="fa fa-search"></i> {{ __('Search') }}
                        </button>
                    </div>
                    <button type="button" data-bs-toggle="modal" data-bs-target="#createArea" class="btn py-2 btn-primary">
                        <i class="fa fa-plus-circle"></i>
                        {{ __('Add Area') }}
                    </button>
                </form>

                <div class="table-responsive">
                    <table class="table border table-responsive-lg">
                        <thead>
                            <tr>
                                <th class="text-center">{{ __('SL') }}</th>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Delivery Amount') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th class="text-center">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        @forelse($areas as $key => $area)
                            <tr>
                                <td class="text-center">{{ ++$key }}</td>

                                <td>{{ $area->name }}</td>

                                <td>{{ $area->delivery_amount }}</td>

                                <td>
                                    @hasPermission('admin.area.toggle')
                                        <a href="{{ route('admin.area.toggle', $area->id) }}">
                                            <span class="badge {{ $area->is_active ? 'bg-success' : 'bg-danger' }}">
                                                {{ $area->is_active ? __('Active') : __('Inactive') }}
                                            </span>
                                        </a>
                                    @else
                                        <span class="badge {{ $area->is_active ? 'bg-success' : 'bg-danger' }}">
                                            {{ $area->is_active ? __('Active') : __('Inactive') }}
                                        </span>
                                    @endhasPermission
                                </td>
                                <td class="text-center">
                                    <div class="d-flex gap-2 justify-content-center">
                                        @hasPermission('admin.area.create')
                                            <button type="button" class="btn btn-outline-primary circleIcon btn-sm"
                                                onclick="openAreaUpdateModal({{ $area }})">
                                                <img src="{{ asset('assets/icons-admin/edit.svg') }}" alt="edit"
                                                    loading="lazy" />
                                            </button>
                                        @endhasPermission
                                        @hasPermission('admin.area.delete')
                                            <a href="{{ route('admin.area.destroy', $area->id) }}"
                                                class="circleIcon btn btn-outline-danger btn-sm deleteConfirm">
                                                <img src="{{ asset('assets/icons-admin/trash.svg') }}" alt="delete"
                                                    loading="lazy" />
                                            </a>
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
            {{ $areas->links() }}
        </div>

    </div>

    <!--=== Create Color Modal ===-->
    <form action="{{ route('admin.area.store') }}" method="POST">
        @csrf
        <div class="modal fade" id="createArea">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            {{ __('Add New Area') }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">

                        <div class="mb-3">
                            <x-input type="text" name="name" label="Name" placeholder="Name" required="true" />
                        </div>

                        <div class="mb-3">
                            <x-input type="number" name="delivery_amount" label="Delivery Amount"
                                placeholder="Delivery Amount" required="true" onlyNumber="true" />
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            {{ __('Close') }}
                        </button>
                        <button type="submit" class="btn btn-primary">
                            {{ __('Submit') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!--=== update color Modal ===-->
    <form action="" id="updateAreaForm" method="POST">
        @csrf
        @method('PUT')
        <input type="hidden" name="previous_url" value="{{ url()->previous() }}" />
        <div class="modal fade" id="updateArea" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            {{ __('Update Area') }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <x-input type="text" id="update_name" name="name" label="Name" placeholder="Area Name"
                                required="true" />
                        </div>

                        <div class="mb-3">
                            <x-input type="text" id="update_delivery_amount" name="delivery_amount"
                                label="Delivery Amount" placeholder="Delivery Amount" required="true" onlyNumber="true"
                                min="1" />
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            {{ __('Close') }}
                        </button>
                        <button type="submit" class="btn btn-primary">
                            {{ __('Update') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        const openAreaUpdateModal = (area) => {

            $("#update_name").val(area.name);
            $("#update_delivery_amount").val(area.delivery_amount);
            $("#updateAreaForm").attr('action', `{{ route('admin.area.update', ':id') }}`.replace(':id', area
                .id));

            $("#updateArea").modal('show');
        }
    </script>
@endpush
