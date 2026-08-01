@extends('layouts.app')

@section('header-title', __('Payout Management'))
@section('header-subtitle', __('Review the month, then confirm the payout run.'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h1 class="h3 mb-0 text-gray-800 fw-bold">{{ __('Run Payout') }}</h1>
        <p class="text-muted small mb-0">{{ __('Select a month, review the preview, then run.') }}</p>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4">{!! nl2br(e(session('error'))) !!}</div>
@endif

{{-- Month / Year + Preview --}}
<div class="card border-0 shadow-sm rounded-12 mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('admin.payout.run.form') }}" class="row g-2 align-items-end">
            <div class="col-auto">
                <label class="form-label small text-muted mb-1">{{ __('Month') }}</label>
                <select name="month" class="form-select form-select-sm">
                    @foreach($months as $m)
                        <option value="{{ $m }}" @selected((string) $month === (string) $m)>{{ $m }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <label class="form-label small text-muted mb-1">{{ __('Year') }}</label>
                <select name="year" class="form-select form-select-sm">
                    @for($y = now()->year; $y >= 2024; $y--)
                        <option value="{{ $y }}" @selected((string) $year === (string) $y)>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-outline-primary btn-sm shadow-sm"><i class="fas fa-filter me-1"></i> {{ __('Preview') }}</button>
            </div>
        </form>
    </div>
</div>

{{-- Confirmation --}}
<div class="card border-0 shadow-sm rounded-12 mb-4">
    <div class="card-body py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <div class="fw-bold">{{ __('Confirm run for') }} {{ sprintf('%04d-%02d', $year, $month) }}</div>
            <div class="text-muted small">{{ __('Credits each active shop. Already-paid shops are skipped.') }}</div>
        </div>
        <form method="POST" action="{{ route('admin.payout.run') }}" class="d-inline" onsubmit="return confirm('{{ __('Run the payout for this month?') }}')">
            @csrf
            <input type="hidden" name="month" value="{{ $month }}">
            <input type="hidden" name="year" value="{{ $year }}">
            <button type="submit" class="btn btn-primary shadow-sm"><i class="fas fa-play me-1"></i> {{ __('Run Payout') }}</button>
        </form>
    </div>
</div>

{{-- Preview tree (read-only) --}}
<div class="card border-0 shadow-sm rounded-12">
    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0 fw-bold">{{ __('Preview for') }} {{ sprintf('%04d-%02d', $year, $month) }}</h5>
        <span class="badge bg-light text-dark border">{{ count($nodes) }} {{ __('roots') }}</span>
    </div>
    <div class="card-body p-3">
        @forelse($nodes as $node)
            <ul class="list-unstyled mb-0">
                @include('admin.payout._node', ['node' => $node, 'year' => $year, 'month' => $month])
            </ul>
        @empty
            <div class="text-center py-5 text-muted">
                <i class="fas fa-sitemap fs-1 mb-3 d-block text-secondary"></i>
                {{ __('No active shops for this month.') }}
            </div>
        @endforelse
    </div>
</div>
@endsection
