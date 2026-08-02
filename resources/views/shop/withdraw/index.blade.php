@extends('layouts.app')
@section('header-title', __('Withdraws'))

@section('content')
<!-- Integrated 2-Line Compact Header Banner Card -->
<div class="card border-0 shadow-sm rounded-12 mb-4 bg-gradient bg-primary text-white">
    <div class="card-body p-3">
        <div class="d-flex justify-content-between align-items-center mb-1">
            <h1 class="h4 mb-0 text-white fw-bold"><i class="fas fa-wallet me-2 text-warning"></i>{{ __('Shop Withdrawals') }}</h1>
            <button type="button" data-bs-toggle="modal" data-bs-target="#withdrawModal" class="btn btn-sm btn-warning text-dark fw-bold">
                <i class="fa fa-plus-circle me-1"></i> {{ __('Request Withdrawal') }}
            </button>
        </div>
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 text-white-50 small">
            <span>{{ __('Request and track wallet payout disbursements for your shop.') }}</span>
        </div>
    </div>
</div>

<div class="container-fluid mt-3">

    <!-- Financial Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-12 bg-white h-100">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="fas fa-wallet fa-lg"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-medium">{{ __('Wallet Balance') }}</div>
                        <div class="h5 mb-0 fw-bold text-dark">{{ showCurrency($walletBalance ?? 0) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-12 bg-white h-100">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="fas fa-check-circle fa-lg"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-medium">{{ __('Withdrawable') }}</div>
                        <div class="h5 mb-0 fw-bold text-success">{{ showCurrency($withdrawableBalance ?? 0) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-12 bg-white h-100">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-warning-subtle text-warning d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="fas fa-hourglass-half fa-lg"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-medium">{{ __('Pending Requests') }}</div>
                        <div class="h5 mb-0 fw-bold text-warning">{{ showCurrency($pendingWithdraws ?? 0) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm rounded-12 bg-white h-100">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-info-subtle text-info d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="fas fa-chart-line fa-lg"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-medium">{{ __('Lifetime MLM Payouts') }}</div>
                        <div class="h5 mb-0 fw-bold text-info">{{ showCurrency($lifetimePayouts ?? 0) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-3 card border-0 shadow-sm rounded-12">
        <div class="card-body p-3">
            <div class="table-responsive">
                <table class="table border-left-right align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('SL') }}</th>
                            <th>{{ __('Amount') }}</th>
                            <th>{{ __('Request Date') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th class="text-center">{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($withdraws as $key => $withdraw)
                        @php
                            $serial = $withdraws->firstItem() + $key;
                        @endphp
                        <tr>
                            <td>{{ $serial }}</td>
                            <td class="fw-bold text-dark">{{ showCurrency($withdraw->amount) }}</td>

                            <td>
                                {{ $withdraw->created_at->format('M d, Y') }} <br>
                                <small class="text-muted">{{ $withdraw->created_at->diffForHumans() }}</small>
                            </td>

                            <td>
                                @if ($withdraw->status == 'pending')
                                    <span class="badge bg-warning-subtle text-warning border border-warning px-2.5 py-1">
                                        <i class="bi bi-exclamation-triangle me-1"></i>
                                        {{ __('Pending') }}
                                    </span>
                                @elseif($withdraw->status == 'approved')
                                    <span class="badge bg-success-subtle text-success border border-success px-2.5 py-1">
                                        <i class="bi bi-check2-all me-1"></i>
                                        {{ __('Approved') }}
                                    </span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger px-2.5 py-1">
                                        <i class="bi bi-x-octagon-fill me-1"></i>
                                        {{ __('Denied') }}
                                    </span>
                                @endif
                            </td>

                            <td class="text-center">
                                @if ($withdraw->status == 'pending')
                                    <a href="{{ route('shop.withdraw.delete', $withdraw->id) }}"
                                        class="btn btn-sm btn-outline-danger confirm">
                                        <i class="fas fa-times me-1"></i> {{__('Cancel Request')}}
                                    </a>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="text-center py-4 text-muted" colspan="100%">
                                <i class="fas fa-receipt fa-2x mb-2 d-block text-secondary"></i>
                                {{ __('No withdrawal records found.') }}
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="my-3">
        {{ $withdraws->withQueryString()->links() }}
    </div>

</div>

<!-- Withdraw Modal -->
<form id="withdrawForm" method="POST">
    @csrf
    <div class="modal fade" id="withdrawModal" tabindex="-1">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white fw-bold">
                        <i class="fas fa-hand-holding-usd me-2"></i>{{__('Request Payout Withdrawal')}}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-light border shadow-sm rounded-12 p-3 mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-muted small fw-medium">{{ __('Withdrawable Balance') }}:</span>
                            <span class="fw-bold text-success h6 mb-0">{{ showCurrency($withdrawableBalance ?? 0) }}</span>
                        </div>
                        @if(($generaleSetting?->min_withdraw ?? 0) > 0)
                            <div class="d-flex justify-content-between align-items-center text-muted small">
                                <span>{{ __('Min Withdrawal') }}:</span>
                                <span>{{ showCurrency($generaleSetting->min_withdraw) }}</span>
                            </div>
                        @endif
                    </div>

                    <div>
                        <label class="form-label fw-medium">
                           {{__('Withdraw Amount')}} <span class="text-danger">*</span>
                        </label>
                        <div class="input-group mb-2">
                            <span class="input-group-text">{{ $generaleSetting?->currency ?? '₹' }}</span>
                            <input type="text" name="amount" id="amount" class="form-control"
                                placeholder="Enter amount"
                                oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');"
                                required>
                        </div>
                        
                        <!-- Quick Percentage Fill Buttons -->
                        @if(($withdrawableBalance ?? 0) > 0)
                            <div class="d-flex gap-1 mb-2">
                                <button type="button" class="btn btn-xs btn-outline-secondary flex-fill quick-amount" data-pct="0.25">25%</button>
                                <button type="button" class="btn btn-xs btn-outline-secondary flex-fill quick-amount" data-pct="0.50">50%</button>
                                <button type="button" class="btn btn-xs btn-outline-secondary flex-fill quick-amount" data-pct="0.75">75%</button>
                                <button type="button" class="btn btn-xs btn-outline-primary flex-fill quick-amount" data-pct="1.00">100%</button>
                            </div>
                        @endif

                        <p class="text-danger small mb-0" id="amount-error"></p>
                    </div>

                        <div class="mt-3">
                            <label class="form-label">
                                {{__('Name')}} <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="name" id="name" class="form-control"
                                placeholder="{{__('Name')}}" required>
                            <span class="text-danger" id="name-error"></span>
                        </div>

                        <div class="mt-3">
                            <label class="form-label">
                                {{__('Contact Number')}} <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="contact_number" id="contact_number" class="form-control"
                                placeholder="Enter contact number"
                                oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');"
                                required>
                            <span class="text-danger" id="contact_number-error"></span>
                        </div>

                        <div class="mt-3">
                            <label class="form-label">
                                {{__('Any message')}}
                            </label>
                            <textarea name="message" placeholder="{{__('Any message')}}" class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            {{__('Close')}}
                        </button>
                        <button type="submit" id="submitBtn" class="btn btn-primary">
                            {{__('Submit') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        const withdrawableBalance = {{ (float) ($withdrawableBalance ?? 0) }};

        $('.quick-amount').on('click', function() {
            const pct = parseFloat($(this).data('pct'));
            if (withdrawableBalance > 0 && pct > 0) {
                const calculated = (withdrawableBalance * pct).toFixed(2);
                $('#amount').val(calculated);
            }
        });

        $(".confirm").on("click", function(e) {
            e.preventDefault();
            const url = $(this).attr("href");
            Swal.fire({
                title: "{{__('Are you sure?')}}",
                text: "{{__('If you cancel this request, it will be deleted permanently!')}}",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "{{__('Yes, Cancel it!')}}",
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        });

        $('#withdrawForm').on('submit', function(e) {
            e.preventDefault();
            const amount = $('#amount').val();
            const name = $('#name').val();
            const contact_number = $('#contact_number').val();
            const message = $('#message').val();
            $('#submitBtn').prop('disabled', true);
            $.ajax({
                url: "{{ route('shop.withdraw.store') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    amount: amount,
                    name: name,
                    contact_number: contact_number,
                    message: message,
                },
                success: function(response) {
                    Swal.fire({
                        title: "Success!",
                        text: response.message,
                        icon: "success",
                        confirmButtonColor: "#3085d6",
                        confirmButtonText: "Ok"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.reload();
                        }
                    });
                },
                error: function(response) {
                    $('#submitBtn').prop('disabled', false);
                    const errors = response.responseJSON.errors;
                    if (errors && errors.amount) {
                        $('#amount').addClass('is-invalid');
                        $('#amount-error').text(errors.amount[0]);
                    }
                    if (errors && errors.name) {
                        $('#name').addClass('is-invalid');
                        $('#name-error').text(errors.name[0]);
                    }
                    if (errors && errors.contact_number) {
                        $('#contact_number').addClass('is-invalid');
                        $('#contact_number-error').text(errors.contact_number[0]);
                    }

                    if (!errors) {
                        Swal.fire({
                            title: response.responseJSON.message,
                            icon: "warning",
                            confirmButtonColor: "#3085d6",
                            confirmButtonText: "Ok"
                        });
                    }
                }

            });
        });
    </script>
@endpush
