@extends('layouts.app')

@section('header-title', __('Customer Reviews Moderation'))

@section('content')
    <!-- App Page Title -->
    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="w-100 page-title-heading d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div>
                    {{ __('Customer Reviews & Ratings') }}
                    <div class="page-title-subheading">
                        {{ __('Review, moderate, approve, and reply to all customer feedback across franchise shops.') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <!-- KPI Metrics Row -->
        <div class="row g-3 mb-4">
            <!-- Total Reviews -->
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card shadow-sm border-0 rounded-12 h-100">
                    <div class="card-body p-3 d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small text-uppercase fw-semibold d-block">{{ __('Total Reviews') }}</span>
                            <h3 class="fw-bold text-dark mb-0 mt-1">{{ number_format($totalReviews) }}</h3>
                        </div>
                        <div class="avatar bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; min-width: 48px;">
                            <i class="bi bi-chat-square-text-fill fs-4 text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pending Approval -->
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card shadow-sm border-0 rounded-12 h-100" style="border-left: 4px solid #f59e0b !important;">
                    <div class="card-body p-3 d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small text-uppercase fw-semibold d-block">{{ __('Pending Approval') }}</span>
                            <h3 class="fw-bold text-warning mb-0 mt-1">{{ number_format($pendingReviews) }}</h3>
                        </div>
                        <div class="avatar bg-warning-subtle text-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; min-width: 48px;">
                            <i class="bi bi-hourglass-split fs-4 text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Approved & Live -->
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card shadow-sm border-0 rounded-12 h-100" style="border-left: 4px solid #10b981 !important;">
                    <div class="card-body p-3 d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small text-uppercase fw-semibold d-block">{{ __('Approved & Live') }}</span>
                            <h3 class="fw-bold text-success mb-0 mt-1">{{ number_format($approvedReviews) }}</h3>
                        </div>
                        <div class="avatar bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; min-width: 48px;">
                            <i class="bi bi-check-circle-fill fs-4 text-success"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Average Rating -->
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card shadow-sm border-0 rounded-12 h-100">
                    <div class="card-body p-3 d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small text-uppercase fw-semibold d-block">{{ __('Average Rating') }}</span>
                            <h3 class="fw-bold text-dark mb-0 mt-1">
                                ⭐ {{ number_format((float)$averageRating, 1) }} <small class="text-muted fs-6 font-normal">/ 5.0</small>
                            </h3>
                        </div>
                        <div class="avatar bg-info-subtle text-info rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; min-width: 48px;">
                            <i class="bi bi-star-half fs-4 text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Card Section -->
        <div class="card shadow-sm rounded-12 border-0">
            <div class="card-body p-3 p-md-4">
                
                <!-- Status Navigation Tabs -->
                <ul class="nav nav-tabs order-tabs mb-3 flex-nowrap overflow-auto">
                    <li class="nav-item">
                        <a href="{{ route('admin.review.index', array_merge(request()->except('status', 'page'), ['status' => 'all'])) }}"
                           class="nav-link {{ $status === 'all' ? 'active' : '' }}">
                            {{ __('All Reviews') }} ({{ $totalReviews }})
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.review.index', array_merge(request()->except('status', 'page'), ['status' => 'pending'])) }}"
                           class="nav-link {{ $status === 'pending' ? 'active' : '' }}">
                            <span>{{ __('Pending Approval') }}</span>
                            @if($pendingReviews > 0)
                                <span class="badge bg-warning text-dark rounded-pill ms-1">{{ $pendingReviews }}</span>
                            @endif
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.review.index', array_merge(request()->except('status', 'page'), ['status' => 'approved'])) }}"
                           class="nav-link {{ $status === 'approved' ? 'active' : '' }}">
                            <span>{{ __('Approved & Live') }}</span>
                            <span class="badge bg-success-subtle text-success rounded-pill ms-1">{{ $approvedReviews }}</span>
                        </a>
                    </li>
                </ul>

                <!-- Filter Strip -->
                <form action="{{ route('admin.review.index') }}" method="GET" class="row g-2 align-items-center mb-3">
                    <input type="hidden" name="status" value="{{ $status }}">

                    <!-- Search Input -->
                    <div class="col-12 col-md-4 col-lg-4">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="{{ __('Search customer, product, order...') }}" value="{{ $search }}">
                        </div>
                    </div>

                    <!-- Shop Dropdown -->
                    <div class="col-6 col-md-3 col-lg-3">
                        <select name="shop_id" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">{{ __('All Franchise Shops') }}</option>
                            @foreach($shops as $s)
                                <option value="{{ $s->id }}" {{ $shopId == $s->id ? 'selected' : '' }}>
                                    {{ $s->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Rating Dropdown -->
                    <div class="col-6 col-md-3 col-lg-3">
                        <select name="rating" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">{{ __('All Star Ratings') }}</option>
                            <option value="5" {{ $rating == '5' ? 'selected' : '' }}>⭐⭐⭐⭐⭐ (5 Stars)</option>
                            <option value="4" {{ $rating == '4' ? 'selected' : '' }}>⭐⭐⭐⭐ (4 Stars)</option>
                            <option value="3" {{ $rating == '3' ? 'selected' : '' }}>⭐⭐⭐ (3 Stars)</option>
                            <option value="2" {{ $rating == '2' ? 'selected' : '' }}>⭐⭐ (2 Stars)</option>
                            <option value="1" {{ $rating == '1' ? 'selected' : '' }}>⭐ (1 Star)</option>
                        </select>
                    </div>

                    <!-- Filter / Reset Buttons -->
                    <div class="col-12 col-md-2 col-lg-2 d-flex gap-1 justify-content-md-end">
                        <button type="submit" class="btn btn-sm btn-primary flex-grow-1 flex-md-grow-0">
                            <i class="bi bi-funnel-fill me-1"></i>{{ __('Filter') }}
                        </button>
                        @if($search || $shopId || $rating || $status !== 'all')
                            <a href="{{ route('admin.review.index') }}" class="btn btn-sm btn-outline-danger" title="{{ __('Reset Filters') }}">
                                <i class="bi bi-x-lg"></i>
                            </a>
                        @endif
                    </div>
                </form>

                <!-- Reviews Table -->
                <div class="table-responsive">
                    <table class="table border-left-right table-responsive-lg align-middle table-hover">
                        <thead class="table-light text-uppercase small text-muted">
                            <tr>
                                <th style="min-width: 180px;">{{ __('Product') }}</th>
                                <th style="min-width: 170px;">{{ __('Customer & Order') }}</th>
                                <th style="min-width: 140px;">{{ __('Shop') }}</th>
                                <th style="min-width: 110px;" class="text-center">{{ __('Rating') }}</th>
                                <th style="min-width: 260px;">{{ __('Customer Review & Response') }}</th>
                                <th style="min-width: 130px;" class="text-center">{{ __('Status') }}</th>
                                <th style="min-width: 140px;" class="text-end pe-3">{{ __('Actions') }}</th>
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
                                                <a href="{{ route('admin.product.show', $review->product_id ?? 0) }}" class="fw-semibold text-dark text-decoration-none d-block small">
                                                    {{ Str::limit($review->product?->name ?? 'Product Deleted', 28) }}
                                                </a>
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
                                                    <i class="bi bi-clock me-1"></i>{{ $review->created_at?->format('d M Y, h:i A') }}
                                                </div>
                                                @if($review->order)
                                                    <a href="{{ route('admin.order.show', $review->order_id) }}" class="badge bg-light text-primary border text-decoration-none mt-1" style="font-size: 10px;">
                                                        <i class="bi bi-receipt me-1"></i>{{ $review->order->prefix ?? 'ORD' }}{{ $review->order->order_code }}
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Shop -->
                                    <td>
                                        <span class="badge bg-light text-dark border fw-medium px-2 py-1">
                                            <i class="bi bi-shop me-1 text-success"></i>{{ $review->shop?->name ?? 'Central Warehouse' }}
                                        </span>
                                    </td>

                                    <!-- Rating -->
                                    <td class="text-center">
                                        <div class="d-inline-flex align-items-center gap-1 bg-warning-subtle text-dark px-2 py-1 rounded-pill fw-bold" style="font-size: 13px;">
                                            <i class="fa fa-star text-warning"></i>
                                            <span>{{ number_format((float)$review->rating, 1) }}</span>
                                        </div>
                                    </td>

                                    <!-- Description, Photos & Reply -->
                                    <td>
                                        <div class="text-dark small leading-normal mb-1">
                                            {{ $review->description }}
                                        </div>

                                        <!-- Photo Attachments -->
                                        @if(!empty($review->photos) && is_array($review->photos))
                                            <div class="d-flex gap-1.5 flex-wrap my-1.5">
                                                @foreach($review->photos as $photoIdx => $photo)
                                                    <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#photoModal{{ $review->id }}_{{ $photoIdx }}">
                                                        <img src="{{ $photo }}" class="rounded border object-fit-cover shadow-xs" width="40" height="40" title="{{ __('Click to enlarge') }}">
                                                    </a>

                                                    <!-- Photo Lightbox Modal -->
                                                    <div class="modal fade" id="photoModal{{ $review->id }}_{{ $photoIdx }}" tabindex="-1" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered modal-lg">
                                                            <div class="modal-content border-0 shadow bg-transparent">
                                                                <div class="modal-body p-0 text-center position-relative">
                                                                    <img src="{{ $photo }}" class="img-fluid rounded-3 shadow-lg max-vh-80" alt="review photo">
                                                                    <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif

                                        <!-- Official Store Response -->
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

                                    <!-- Actions Toolbar -->
                                    <td class="text-end pe-3">
                                        <div class="d-inline-flex gap-1">
                                            @if(!$review->is_active)
                                                <!-- Approve Button -->
                                                <form action="{{ route('admin.review.approve', $review->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success py-1 px-2" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Approve & Publish Review') }}">
                                                        <i class="bi bi-check-lg"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <!-- Reject / Hide Button -->
                                                <form action="{{ route('admin.review.reject', $review->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-warning py-1 px-2" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Hide / Unpublish Review') }}">
                                                        <i class="bi bi-eye-slash-fill"></i>
                                                    </button>
                                                </form>
                                            @endif

                                            <!-- Reply Modal Trigger -->
                                            <button type="button" class="btn btn-sm btn-outline-primary py-1 px-2" data-bs-toggle="modal" data-bs-target="#replyModal{{ $review->id }}" title="{{ __('Post Official Reply') }}">
                                                <i class="bi bi-reply-fill"></i>
                                            </button>

                                            <!-- Delete Button -->
                                            <form action="{{ route('admin.review.destroy', $review->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Are you sure you want to permanently delete this review?') }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger py-1 px-2" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Delete Review') }}">
                                                    <i class="bi bi-trash-fill"></i>
                                                </button>
                                            </form>
                                        </div>

                                        <!-- Reply Modal -->
                                        <div class="modal fade text-start" id="replyModal{{ $review->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content border-0 shadow">
                                                    <form action="{{ route('admin.review.reply', $review->id) }}" method="POST">
                                                        @csrf
                                                        <div class="modal-header border-bottom py-2.5 px-3">
                                                            <h6 class="modal-title fw-bold text-dark mb-0">
                                                                <i class="bi bi-reply-fill text-primary me-1"></i>{{ __('Official Response to Review #') }}{{ $review->id }}
                                                            </h6>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body p-3">
                                                            <div class="mb-3 p-2.5 bg-light rounded-2 border">
                                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                                    <span class="small fw-bold text-dark">{{ $review->customer?->user?->name ?? 'Customer' }}</span>
                                                                    <span class="badge bg-warning text-dark font-normal">⭐ {{ number_format((float)$review->rating, 1) }}</span>
                                                                </div>
                                                                <div class="small text-muted italic">"{{ $review->description }}"</div>
                                                            </div>
                                                            <div class="mb-2">
                                                                <label class="form-label fw-semibold small text-dark mb-1">
                                                                    {{ __('Official Store / Admin Reply Message:') }}
                                                                </label>
                                                                <textarea name="reply" class="form-control" rows="4" placeholder="{{ __('Type your response to the customer (e.g. Thank you for your feedback! We appreciate your support...)...') }}" required>{{ $review->reply }}</textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer border-top py-2 px-3">
                                                            <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                                                            <button type="submit" class="btn btn-sm btn-primary px-3">
                                                                <i class="bi bi-send-fill me-1"></i>{{ __('Save & Publish Reply') }}
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <div class="avatar-lg bg-light text-muted rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                                            <i class="bi bi-chat-square-dots fs-2"></i>
                                        </div>
                                        <h6 class="fw-semibold">{{ __('No customer reviews found') }}</h6>
                                        <p class="small mb-0 text-muted">{{ __('No customer reviews match the selected filter criteria.') }}</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-end mt-3">
                    {{ $reviews->links() }}
                </div>

            </div>
        </div>
    </div>
@endsection
