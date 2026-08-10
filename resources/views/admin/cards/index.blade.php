@extends('layouts.app')
@section('header-title', __('Cards'))

@section('content')
    <div class="d-flex align-items-center flex-wrap gap-3 justify-content-between px-3">
        <h4>{{ __('Cards') }}</h4>
    </div>

    <div class="mt-4">
        <div class="row">
            <div class="col-lg-12 mb-3">
                <div class="card rounded-12">
                    <div class="card-body">
                        <form action="{{ route('admin.cards.store') }}" method="POST" class="row g-3 align-items-end">
                            @csrf
                            <div class="col-md-5">
                                <label class="form-label">{{ __('Assign to Customer') }} ({{ __('optional') }})</label>
                                <select name="customer_id" class="form-select">
                                    <option value="">— {{ __('Unassigned card') }} —</option>
                                    @foreach ($customers as $customer)
                                        <option value="{{ $customer->id }}">
                                            {{ $customer->user?->name ?? '#' . $customer->id }}
                                            ({{ $customer->user?->phone ?? '' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <div class="text-muted small">
                                    {{ __('A unique card number is generated automatically.') }}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-plus-circle"></i> {{ __('Create Card') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-12">
                <div class="card rounded-12">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table border-left-right">
                                <thead>
                                    <tr>
                                        <th>{{ __('Card Number') }}</th>
                                        <th>{{ __('Customer') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($cards as $card)
                                        <tr>
                                            <td class="fw-bold">{{ $card->card_number }}</td>
                                            <td>
                                                @if ($card->customer)
                                                    {{ $card->customer->user?->name ?? '#' . $card->customer->id }}
                                                    <small class="text-muted">
                                                        ({{ $card->customer->user?->phone ?? '' }})
                                                    </small>
                                                @else
                                                    <span class="text-muted">{{ __('Unassigned') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $card->is_active ? 'success' : 'danger' }}">
                                                    {{ $card->is_active ? __('Active') : __('Inactive') }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.cards.show', $card) }}"
                                                    class="btn btn-sm btn-info">
                                                    {{ __('View') }}
                                                </a>
                                                <a href="{{ route('admin.cards.toggle', $card) }}"
                                                    class="btn btn-sm btn-{{ $card->is_active ? 'danger' : 'success' }}">
                                                    {{ $card->is_active ? __('Deactivate') : __('Activate') }}
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted">{{ __('No cards yet') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{ $cards->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
