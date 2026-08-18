@extends('layouts.app')

@section('header-title', __('Shop Reviews - ') . $shop->name)

@section('content')
    <div class="container-fluid">
        <div class="card shadow-sm rounded-12 border-0">
            <div class="card-header py-3 bg-white border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div>
                    <h5 class="fw-bold text-dark m-0">{{ $shop->name }} - {{ __('Customer Reviews') }}</h5>
                    <small class="text-muted">{{ __('All customer feedback and product reviews fulfilled by this franchise shop.') }}</small>
                </div>
                <div>
                    <span class="badge bg-warning-subtle text-dark px-3 py-2 rounded-pill fw-bold fs-6">
                        ⭐ {{ number_format((float)$shop->averageRating, 1) }} / 5.0
                    </span>
                </div>
            </div>
            <div class="card-body p-3 p-md-4">
                @include('admin.shop.header-nav')

                <div class="table-responsive mt-4">
                    <table class="table table-responsive-lg border-left-right align-middle table-hover">
                        <thead class="table-light text-uppercase small text-muted">
                            <tr>
                                <th style="min-width: 180px;">{{ __('Product') }}</th>
                                <th style="min-width: 160px;">{{ __('Customer & Order') }}</th>
                                <th style="min-width: 110px;" class="text-center">{{ __('Rating') }}</th>
                                <th style="min-width: 280px;">{{ __('Customer Feedback & Response') }}</th>
                                <th style="min-width: 120px;" class="text-center">{{ __('Status') }}</th>
                                <th style="min-width: 130px;" class="text-end pe-3">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($reviews as $review)
                                <tr>
                                    <!-- Product Info -->
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="rounded overflow-hidden border" style="width: 44px; height: 44px; min-width: 44px;">
                                                <img src="{{ $review->product?->thumbnail ?? asset('default/default.jpg') }}" class="w-100 h-100 object-fit-cover" alt="product">
                                            </div>
                                            <div>
                                                <span class="fw-semibold text-dark d-block small">
                                                    {{ Str::limit($review->product?->name ?? 'Product Deleted', 28) }}
                                                </span>
                                                <span class="text-muted" style="font-size: 11px;">
                                                    ID: #{{ $review->product_id }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Customer & Order -->
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="rounded-circle overflow-hidden border" style="width: 36px; height: 36px; min-width: 36px;">
                                                <img src="{{ $review->customer?->user?->thumbnail ?? asset('default/default.jpg') }}" class="w-100 h-100 object-fit-cover" alt="customer">
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark small">{{ $review->customer?->user?->name ?? 'Customer' }}</div>
                                                <div class="text-muted" style="font-size: 11px;">
                                                    <i class="bi bi-clock me-1"></i>{{ $review->created_at?->format('d M Y') }}
                                                </div>
                                                @if($review->order)
                                                    <span class="badge bg-light text-primary border mt-1" style="font-size: 10px;">
                                                        #{{ $review->order->prefix ?? 'ORD' }}{{ $review->order->order_code }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Rating -->
                                    <td class="text-center">
                                        <div class="d-inline-flex align-items-center gap-1 bg-warning-subtle text-dark px-2 py-1 rounded-pill fw-bold" style="font-size: 13px;">
                                            <i class="fa fa-star text-warning"></i>
                                            <span>{{ number_format((float)$review->rating, 1) }}</span>
                                        </div>
                                    </td>

                                    <!-- Description, Photos & Response -->
                                    <td>
                                        <div class="text-dark small mb-1">{{ $review->description }}</div>

                                        <!-- Photo Attachments -->
                                        @if(!empty($review->photos) && is_array($review->photos))
                                            <div class="d-flex gap-1.5 flex-wrap my-1.5">
                                                @foreach($review->photos as $photo)
                                                    <a href="{{ $photo }}" target="_blank">
                                                        <img src="{{ $photo }}" class="rounded border object-fit-cover shadow-xs" width="40" height="40" title="{{ __('Click to enlarge') }}">
                                                    </a>
                                                @endforeach
                                            </div>
                                        @endif

                                        <!-- Official Reply -->
                                        @if($review->reply)
                                            <div class="mt-2 p-2 bg-light border-start border-primary border-3 rounded-2 small" style="font-size: 11px;">
                                                <div class="fw-bold text-primary mb-0.5">
                                                    <i class="bi bi-reply-fill me-1"></i>{{ __('Official Response') }} ({{ $review->replied_at?->format('d M, Y') }}):
                                                </div>
                                                <div class="text-secondary">{{ $review->reply }}</div>
                                            </div>
                                        @endif
                                    </td>

                                    <!-- Status Badge -->
                                    <td class="text-center">
                                        @if($review->is_active)
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 rounded-pill fw-semibold" style="font-size: 11px;">
                                                <i class="bi bi-check-circle-fill me-1"></i>{{ __('Approved & Live') }}
                                            </span>
                                        @else
                                            <span class="badge bg-warning-subtle text-dark border border-warning-subtle px-2.5 py-1 rounded-pill fw-semibold" style="font-size: 11px;">
                                                <i class="bi bi-hourglass-split me-1 text-warning"></i>{{ __('Pending Approval') }}
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Actions -->
                                    <td class="text-end pe-3">
                                        <div class="d-inline-flex gap-1">
                                            @if(!$review->is_active)
                                                <form action="{{ route('admin.review.approve', $review->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success py-1 px-2" title="{{ __('Approve') }}">
                                                        <i class="bi bi-check-lg"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <form action="{{ route('admin.review.reject', $review->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-warning py-1 px-2" title="{{ __('Hide') }}">
                                                        <i class="bi bi-eye-slash-fill"></i>
                                                    </button>
                                                </form>
                                            @endif

                                            <form action="{{ route('admin.review.destroy', $review->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Are you sure you want to delete this review?') }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger py-1 px-2" title="{{ __('Delete') }}">
                                                    <i class="bi bi-trash-fill"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="bi bi-chat-square-dots fs-2 d-block mb-2 text-muted"></i>
                                        <h6>{{ __('No customer reviews recorded for this shop yet.') }}</h6>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-end mt-3">
                    {{ $reviews->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
