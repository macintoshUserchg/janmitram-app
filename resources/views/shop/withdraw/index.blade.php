@extends('layouts.app')
@section('header-title', __('Wallet Withdrawals'))

@push('styles')
<style>
.kpi-card {
    transition: all 0.2s ease-in-out;
    border-radius: 0.75rem;
    border: 1px solid #e2e8f0;
    background: #ffffff;
}
.kpi-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.06);
}
.bank-preview-box {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 0.75rem;
    padding: 0.875rem 1rem;
}
</style>
@endpush

@section('content')
<!-- Header Banner Card -->
<div class="card border-0 shadow-sm rounded-12 mb-4 bg-primary text-white position-relative overflow-hidden" style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <span class="badge bg-white text-primary px-3 py-1 rounded-pill fw-bold mb-2">{{ __('Vendor Payouts') }}</span>
                <h3 class="h4 mb-1 text-white fw-bold d-flex align-items-center gap-2">
                    <i class="fas fa-hand-holding-usd text-warning"></i> {{ __('Shop Wallet Withdrawals') }}
                </h3>
                <p class="mb-0 text-white-50 small">
                    {{ __('Request and track earnings disbursements directly to your registered KYC bank account.') }}
                </p>
            </div>
            <div>
                @if(($withdrawableBalance ?? 0) >= ($generaleSetting?->min_withdraw ?? 0) && ($withdrawableBalance ?? 0) > 0)
                    <button type="button" data-bs-toggle="modal" data-bs-target="#withdrawModal" class="btn btn-warning text-dark fw-bold px-3 py-2 shadow-sm">
                        <i class="fas fa-plus-circle me-1"></i> {{ __('Request Withdrawal') }}
                    </button>
                @else
                    <button type="button" class="btn btn-light text-muted fw-bold px-3 py-2 disabled" title="{{ __('Minimum withdrawable balance required: ') }} {{ showCurrency($generaleSetting?->min_withdraw ?? 0) }}">
                        <i class="fas fa-lock me-1"></i> {{ __('Request Withdrawal') }}
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="container-fluid px-0">

    <!-- KYC Bank Destination Notice -->
    @if($kyc && ($kyc->bank_name || $kyc->account_number))
        <div class="alert alert-light border shadow-sm rounded-12 p-3 mb-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="p-2.5 bg-success-subtle text-success rounded-circle">
                    <i class="fas fa-university fs-4"></i>
                </div>
                <div>
                    <div class="fw-bold text-dark small">{{ __('Registered KYC Bank Disbursal Account') }}</div>
                    <div class="text-muted small">
                        <strong>{{ $kyc->bank_name ?? __('Bank') }}</strong> —
                        A/C: <span class="font-monospace fw-semibold">{{ $kyc->account_number ? substr($kyc->account_number, 0, 4) . '••••' . substr($kyc->account_number, -4) : '—' }}</span>
                        @if($kyc->ifsc)
                            | IFSC: <span class="font-monospace fw-semibold">{{ $kyc->ifsc }}</span>
                        @endif
                    </div>
                </div>
            </div>
            <div>
                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1.5 rounded-pill">
                    <i class="fas fa-check-circle me-1"></i> {{ __('Verified Account') }}
                </span>
            </div>
        </div>
    @else
        <div class="alert alert-warning border-0 shadow-sm rounded-12 p-3 mb-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="p-2.5 bg-warning-subtle text-warning rounded-circle">
                    <i class="fas fa-exclamation-triangle fs-4"></i>
                </div>
                <div>
                    <div class="fw-bold text-dark small">{{ __('Bank KYC Details Pending') }}</div>
                    <div class="text-muted small">
                        {{ __('Please ensure your bank name, account number, and IFSC code are up to date in your shop settings for swift disbursals.') }}
                    </div>
                </div>
            </div>
            @hasPermission('shop.profile.edit')
                <a href="{{ route('shop.profile.edit') }}" class="btn btn-sm btn-outline-dark fw-semibold">
                    <i class="fas fa-edit me-1"></i> {{ __('Update Bank KYC') }}
                </a>
            @endhasPermission
        </div>
    @endif

    <!-- Financial Summary KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="kpi-card p-3 shadow-sm h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="fas fa-wallet fa-lg"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-medium">{{ __('Total Wallet Balance') }}</div>
                        <h4 class="mb-0 fw-bold text-dark">{{ showCurrency($walletBalance ?? 0) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="kpi-card p-3 shadow-sm h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="fas fa-check-circle fa-lg"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-medium">{{ __('Withdrawable Now') }}</div>
                        <h4 class="mb-0 fw-bold text-success">{{ showCurrency($withdrawableBalance ?? 0) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="kpi-card p-3 shadow-sm h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-warning-subtle text-warning d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="fas fa-hourglass-half fa-lg"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-medium">{{ __('Pending Requests') }}</div>
                        <h4 class="mb-0 fw-bold text-warning">{{ showCurrency($pendingWithdraws ?? 0) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="kpi-card p-3 shadow-sm h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-info-subtle text-info d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="fas fa-chart-line fa-lg"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-medium">{{ __('Lifetime MLM Payouts') }}</div>
                        <h4 class="mb-0 fw-bold text-info">{{ showCurrency($lifetimePayouts ?? 0) }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & History Table Card -->
    <div class="card border-0 shadow-sm rounded-12 bg-white mb-4">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <h5 class="card-title mb-0 fw-bold">{{ __('Withdrawal History') }}</h5>
                <span class="badge bg-light text-dark border">{{ $withdraws->total() }} {{ __('Records') }}</span>
            </div>

            {{-- Quick Filter Pills --}}
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <form method="GET" action="{{ route('shop.withdraw.index') }}" class="d-flex align-items-center gap-2">
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" style="width: 180px;" placeholder="{{ __('Search #ID, note...') }}">
                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()" style="width: 140px;">
                        <option value="">{{ __('All Statuses') }}</option>
                        <option value="pending" @selected(request('status') === 'pending')>{{ __('Pending') }}</option>
                        <option value="approved" @selected(request('status') === 'approved')>{{ __('Approved') }}</option>
                        <option value="denied" @selected(request('status') === 'denied')>{{ __('Denied') }}</option>
                    </select>
                    @if(request()->hasAny(['status', 'search']))
                        <a href="{{ route('shop.withdraw.index') }}" class="btn btn-sm btn-outline-secondary" title="{{ __('Reset Filter') }}">
                            <i class="fas fa-undo"></i>
                        </a>
                    @endif
                </form>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4" style="width: 60px;">{{ __('ID') }}</th>
                            <th class="text-end">{{ __('Amount') }}</th>
                            <th>{{ __('Disbursal Destination') }}</th>
                            <th>{{ __('Request Date & Time') }}</th>
                            <th class="text-center">{{ __('Status') }}</th>
                            <th>{{ __('Remarks') }}</th>
                            <th class="text-center pe-4" style="width: 130px;">{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($withdraws as $withdraw)
                        <tr>
                            <td class="ps-4">
                                <span class="badge bg-light text-dark border fw-bold">#W-{{ str_pad((string) $withdraw->id, 5, '0', STR_PAD_LEFT) }}</span>
                            </td>

                            <td class="text-end">
                                <span class="fw-bold text-dark fs-6">{{ showCurrency($withdraw->amount) }}</span>
                            </td>

                            <td>
                                @if($kyc && ($kyc->bank_name || $kyc->account_number))
                                    <div class="small">
                                        <i class="fas fa-university text-primary me-1"></i><strong>{{ $kyc->bank_name ?? __('Bank') }}</strong>
                                        <div class="text-muted font-monospace">A/C: {{ $kyc->account_number ? substr($kyc->account_number, 0, 4) . '••••' . substr($kyc->account_number, -4) : '—' }}</div>
                                    </div>
                                @else
                                    <span class="text-muted small"><i class="fas fa-info-circle me-1"></i>{{ $withdraw->withdraw_method ?? __('Direct Transfer') }}</span>
                                @endif
                            </td>

                            <td>
                                <div class="fw-semibold text-dark">{{ $withdraw->created_at->format('M d, Y') }}</div>
                                <small class="text-muted">{{ $withdraw->created_at->format('h:i A') }} ({{ $withdraw->created_at->diffForHumans() }})</small>
                            </td>

                            <td class="text-center">
                                @if ($withdraw->status == 'pending')
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-3 py-1.5 rounded-pill fw-semibold">
                                        <i class="fas fa-clock me-1"></i> {{ __('Pending Review') }}
                                    </span>
                                @elseif($withdraw->status == 'approved')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1.5 rounded-pill fw-semibold">
                                        <i class="fas fa-check-circle me-1"></i> {{ __('Approved / Disbursed') }}
                                    </span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1.5 rounded-pill fw-semibold">
                                        <i class="fas fa-times-circle me-1"></i> {{ __('Denied') }}
                                    </span>
                                @endif
                            </td>

                            <td>
                                <span class="text-muted small" title="{{ $withdraw->reason }}">
                                    {{ Str::limit($withdraw->reason ?? '—', 35) }}
                                </span>
                            </td>

                            <td class="text-center pe-4">
                                @if ($withdraw->status == 'pending')
                                    <a href="{{ route('shop.withdraw.delete', $withdraw->id) }}"
                                        class="btn btn-sm btn-outline-danger confirm shadow-sm"
                                        title="{{ __('Cancel this withdrawal request') }}">
                                        <i class="fas fa-times me-1"></i> {{ __('Cancel') }}
                                    </a>
                                @else
                                    <span class="text-muted small"><i class="fas fa-lock opacity-50"></i></span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="text-center py-5 text-muted" colspan="100%">
                                <i class="fas fa-hand-holding-usd fs-1 mb-3 d-block text-secondary"></i>
                                {{ __('No withdrawal records found.') }}
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($withdraws->hasPages())
            <div class="card-footer bg-white border-top py-3">
                {{ $withdraws->links() }}
            </div>
        @endif
    </div>

</div>

<!-- Withdraw Request Modal -->
<form id="withdrawForm" method="POST">
    @csrf
    <div class="modal fade" id="withdrawModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-12 overflow-hidden">
                <div class="modal-header bg-primary text-white py-3">
                    <h5 class="modal-title text-white fw-bold d-flex align-items-center gap-2">
                        <i class="fas fa-hand-holding-usd text-warning"></i> {{ __('Request Payout Withdrawal') }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <!-- Balance Notice -->
                    <div class="alert alert-light border shadow-sm rounded-12 p-3 mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-muted small fw-medium">{{ __('Withdrawable Balance') }}:</span>
                            <span class="fw-bold text-success fs-5">{{ showCurrency($withdrawableBalance ?? 0) }}</span>
                        </div>
                        @if(($generaleSetting?->min_withdraw ?? 0) > 0)
                            <div class="d-flex justify-content-between align-items-center text-muted small">
                                <span>{{ __('Min Withdrawal Limit') }}:</span>
                                <span class="fw-semibold text-dark">{{ showCurrency($generaleSetting->min_withdraw) }}</span>
                            </div>
                        @endif
                    </div>

                    <!-- Destination Account Box -->
                    @if($kyc && ($kyc->bank_name || $kyc->account_number))
                        <div class="bank-preview-box mb-3">
                            <div class="fw-bold text-dark small mb-1"><i class="fas fa-university text-primary me-1"></i>{{ __('Disbursal Bank Account') }}</div>
                            <div class="small text-muted">
                                {{ $kyc->bank_name ?? __('Bank') }} (A/C: <span class="font-monospace fw-bold">{{ $kyc->account_number ? substr($kyc->account_number, 0, 4) . '••••' . substr($kyc->account_number, -4) : '—' }}</span>)
                                @if($kyc->ifsc)
                                    | IFSC: <span class="font-monospace fw-bold">{{ $kyc->ifsc }}</span>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Amount Input -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                           {{ __('Withdrawal Amount (₹)') }} <span class="text-danger">*</span>
                        </label>
                        <div class="input-group mb-2">
                            <span class="input-group-text bg-light fw-bold">{{ $generaleSetting?->currency ?? '₹' }}</span>
                            <input type="text" name="amount" id="amount" class="form-control form-control-lg fw-bold"
                                placeholder="0.00"
                                oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');"
                                required>
                        </div>
                        
                        <!-- Quick Percentage Buttons -->
                        @if(($withdrawableBalance ?? 0) > 0)
                            <div class="d-flex gap-1 mb-2">
                                <button type="button" class="btn btn-xs btn-outline-secondary flex-fill quick-amount rounded-pill" data-pct="0.25">25%</button>
                                <button type="button" class="btn btn-xs btn-outline-secondary flex-fill quick-amount rounded-pill" data-pct="0.50">50%</button>
                                <button type="button" class="btn btn-xs btn-outline-secondary flex-fill quick-amount rounded-pill" data-pct="0.75">75%</button>
                                <button type="button" class="btn btn-xs btn-outline-primary flex-fill quick-amount rounded-pill" data-pct="1.00">100% (Max)</button>
                            </div>
                        @endif

                        <p class="text-danger small mb-0" id="amount-error"></p>
                    </div>

                    <!-- Contact Details -->
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">{{ __('Contact Person Name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control form-control-sm"
                                value="{{ auth()->user()->fullName }}" placeholder="{{ __('Name') }}" required>
                            <span class="text-danger small" id="name-error"></span>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">{{ __('Contact Phone Number') }} <span class="text-danger">*</span></label>
                            <input type="text" name="contact_number" id="contact_number" class="form-control form-control-sm"
                                value="{{ auth()->user()->phone }}" placeholder="10-digit number"
                                oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" required>
                            <span class="text-danger small" id="contact_number-error"></span>
                        </div>
                    </div>

                    <!-- Remarks -->
                    <div class="mb-2">
                        <label class="form-label small fw-semibold">{{ __('Remarks / Note (Optional)') }}</label>
                        <textarea name="message" id="message" rows="2" placeholder="{{ __('Add any transfer notes or payment reference...') }}" class="form-control form-control-sm"></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
                        {{ __('Cancel') }}
                    </button>
                    <button type="submit" id="submitBtn" class="btn btn-primary btn-sm px-4 fw-semibold shadow-sm">
                        <i class="fas fa-paper-plane me-1"></i> {{ __('Submit Request') }}
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
            title: "{{ __('Are you sure?') }}",
            text: "{{ __('If you cancel this request, it will be deleted permanently!') }}",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "{{ __('Yes, Cancel it!') }}",
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

        // Clear previous validation errors
        $('.is-invalid').removeClass('is-invalid');
        $('p.text-danger, span.text-danger').text('');

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
                    title: "{{ __('Request Submitted!') }}",
                    text: response.message,
                    icon: "success",
                    confirmButtonColor: "#3085d6",
                    confirmButtonText: "{{ __('OK') }}"
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.reload();
                    }
                });
            },
            error: function(response) {
                $('#submitBtn').prop('disabled', false);
                const errors = response.responseJSON?.errors;
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
                        title: response.responseJSON?.message || "{{ __('Submission Failed') }}",
                        icon: "warning",
                        confirmButtonColor: "#3085d6",
                        confirmButtonText: "{{ __('OK') }}"
                    });
                }
            }
        });
    });
</script>
@endpush
