@extends('layouts.app')

@section('title', __('Customer Reviews Moderation'))

@section('content')
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-1 text-dark">{{ __('Customer Reviews & Ratings Moderation') }}</h4>
            <p class="text-muted mb-0 small">{{ __('Review, moderate, approve, and officially respond to product and shop reviews submitted by customers.') }}</p>
        </div>
    </div>

    <!-- Alert Notifications -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm mb-4">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- KPI Summary Metrics -->
    <div class="row g-3 mb-4">
        <!-- Total Reviews -->
        <div class="col-md-3">
            <div class="card shadow-sm border-0 rounded-3 h-100">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small text-uppercase fw-semibold">{{ __('Total Reviews') }}</div>
                        <h3 class="fw-bold text-dark mb-0 mt-1">{{ number_format($totalReviews) }}</h3>
                    </div>
                    <div class="avatar bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="fas fa-comments fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Approval -->
        <div class="col-md-3">
            <div class="card shadow-sm border-0 rounded-3 h-100 border-start border-warning border-4">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small text-uppercase fw-semibold">{{ __('Pending Approval') }}</div>
                        <h3 class="fw-bold text-warning mb-0 mt-1">{{ number_format($pendingReviews) }}</h3>
                    </div>
                    <div class="avatar bg-warning-subtle text-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="fas fa-hourglass-half fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Approved & Live -->
        <div class="col-md-3">
            <div class="card shadow-sm border-0 rounded-3 h-100 border-start border-success border-4">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small text-uppercase fw-semibold">{{ __('Approved & Live') }}</div>
                        <h3 class="fw-bold text-success mb-0 mt-1">{{ number_format($approvedReviews) }}</h3>
                    </div>
                    <div class="avatar bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="fas fa-check-circle fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Average Rating -->
        <div class="col-md-3">
            <div class="card shadow-sm border-0 rounded-3 h-100">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small text-uppercase fw-semibold">{{ __('Average Rating') }}</div>
                        <h3 class="fw-bold text-dark mb-0 mt-1">
                            ⭐ {{ number_format((float)$averageRating, 1) }} <span class="fs-6 text-muted font-normal">/ 5.0</span>
                        </h3>
                    </div>
                    <div class="avatar bg-info-subtle text-info rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="fas fa-star fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Bar & Status Pills -->
    <div class="card shadow-sm border-0 rounded-3 mb-4">
        <div class="card-body p-3">
            <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
                <!-- Status Filter Pills -->
                <div class="nav nav-pills gap-1">
                    <a href="{{ route('admin.review.index', array_merge(request()->except('status', 'page'), ['status' => 'all'])) }}"
                       class="nav-link rounded-pill px-3 py-1.5 {{ $status === 'all' ? 'active' : 'bg-light text-dark' }}">
                        {{ __('All Reviews') }} ({{ $totalReviews }})
                    </a>
                    <a href="{{ route('admin.review.index', array_merge(request()->except('status', 'page'), ['status' => 'pending'])) }}"
                       class="nav-link rounded-pill px-3 py-1.5 {{ $status === 'pending' ? 'active bg-warning text-dark fw-bold' : 'bg-light text-dark' }}">
                        <i class="fas fa-hourglass-half me-1"></i> {{ __('Pending Approval') }}
                        @if($pendingReviews > 0)
                            <span class="badge bg-danger ms-1 rounded-pill">{{ $pendingReviews }}</span>
                        @endif
                    </a>
                    <a href="{{ route('admin.review.index', array_merge(request()->except('status', 'page'), ['status' => 'approved'])) }}"
                       class="nav-link rounded-pill px-3 py-1.5 {{ $status === 'approved' ? 'active bg-success text-white' : 'bg-light text-dark' }}">
                        <i class="fas fa-check-circle me-1"></i> {{ __('Approved & Live') }} ({{ $approvedReviews }})
                    </a>
                </div>

                <!-- Search & Filters Form -->
                <form action="{{ route('admin.review.index') }}" method="GET" class="d-flex flex-wrap align-items-center gap-2">
                    <input type="hidden" name="status" value="{{ $status }}">

                    <!-- Shop Filter -->
                    <select name="shop_id" class="form-select form-select-sm" style="max-width: 180px;" onchange="this.form.submit()">
                        <option value="">{{ __('All Franchise Shops') }}</option>
                        @foreach($shops as $s)
                            <option value="{{ $s->id }}" {{ $shopId == $s->id ? 'selected' : '' }}>
                                {{ $s->name }}
                            </option>
                        @endforeach
                    </select>

                    <!-- Rating Filter -->
                    <select name="rating" class="form-select form-select-sm" style="max-width: 140px;" onchange="this.form.submit()">
                        <option value="">{{ __('All Ratings') }}</option>
                        <option value="5" {{ $rating == '5' ? 'selected' : '' }}>⭐⭐⭐⭐⭐ (5)</option>
                        <option value="4" {{ $rating == '4' ? 'selected' : '' }}>⭐⭐⭐⭐ (4)</option>
                        <option value="3" {{ $rating == '3' ? 'selected' : '' }}>⭐⭐⭐ (3)</option>
                        <option value="2" {{ $rating == '2' ? 'selected' : '' }}>⭐⭐ (2)</option>
                        <option value="1" {{ $rating == '1' ? 'selected' : '' }}>⭐ (1)</option>
                    </select>

                    <!-- Search Input -->
                    <div class="input-group input-group-sm" style="max-width: 220px;">
                        <input type="text" name="search" class="form-control" placeholder="{{ __('Search customer/product...') }}" value="{{ $search }}">
                        <button type="submit" class="btn btn-outline-secondary"><i class="fas fa-search"></i></button>
                    </div>

                    @if($search || $shopId || $rating || $status !== 'all')
                        <a href="{{ route('admin.review.index') }}" class="btn btn-sm btn-outline-danger" title="{{ __('Reset Filters') }}">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </form>
            </div>
        </div>
    </div>

    <!-- Reviews Table -->
    <div class="card shadow-sm border-0 rounded-3 mb-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-uppercase small text-muted">
                        <tr>
                            <th class="ps-3" style="width: 200px;">{{ __('Customer') }}</th>
                            <th style="width: 240px;">{{ __('Product & Shop') }}</th>
                            <th style="width: 120px;" class="text-center">{{ __('Rating') }}</th>
                            <th>{{ __('Customer Review & Photos') }}</th>
                            <th style="width: 140px;" class="text-center">{{ __('Status') }}</th>
                            <th class="text-end pe-3" style="width: 160px;">{{ __('Moderation Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reviews as $review)
                            <tr>
                                <!-- Customer -->
                                <td class="ps-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="{{ $review->customer?->user?->thumbnail ?? asset('default/default.jpg') }}" class="rounded-circle object-fit-cover border" width="40" height="40">
                                        <div>
                                            <div class="fw-bold text-dark small">{{ $review->customer?->user?->name ?? 'Guest / Customer' }}</div>
                                            <div class="text-muted" style="font-size: 11px;">
                                                <i class="fas fa-calendar-alt me-1"></i>{{ $review->created_at?->format('d M, Y') }}
                                            </div>
                                            @if($review->order_id)
                                                <span class="badge bg-light text-dark border" style="font-size: 10px;">
                                                    <i class="fas fa-bag-shopping me-1 text-primary"></i>#ORD-{{ $review->order_id }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <!-- Product & Shop -->
                                <td>
                                    <div class="fw-semibold text-dark small">{{ $review->product?->name ?? 'Product Deleted' }}</div>
                                    <div class="small text-muted">
                                        <i class="fas fa-store me-1 text-success"></i>{{ $review->shop?->name ?? 'Central' }}
                                    </div>
                                </td>

                                <!-- Rating -->
                                <td class="text-center">
                                    <div class="badge bg-warning-subtle text-dark px-2.5 py-1 rounded-pill fw-bold fs-6">
                                        ⭐ {{ number_format((float)$review->rating, 1) }}
                                    </div>
                                </td>

                                <!-- Description, Photos & Reply -->
                                <td>
                                    <p class="mb-1 text-dark small">{{ $review->description }}</p>

                                    <!-- Review Photos -->
                                    @if(!empty($review->photos) && is_array($review->photos))
                                        <div class="d-flex gap-2 flex-wrap my-1">
                                            @foreach($review->photos as $photo)
                                                <a href="{{ $photo }}" target="_blank">
                                                    <img src="{{ $photo }}" class="rounded border object-fit-cover shadow-xs" width="45" height="45" title="{{ __('Click to enlarge') }}">
                                                </a>
                                            @endforeach
                                        </div>
                                    @endif

                                    <!-- Admin Official Reply -->
                                    @if($review->reply)
                                        <div class="mt-2 p-2 bg-light border-start border-primary border-3 rounded-2 small">
                                            <div class="fw-bold text-primary" style="font-size: 11px;">
                                                <i class="fas fa-reply me-1"></i>{{ __('Official Response') }} ({{ $review->replied_at?->format('d M, Y') }}):
                                            </div>
                                            <div class="text-muted">{{ $review->reply }}</div>
                                        </div>
                                    @endif
                                </td>

                                <!-- Status Badge -->
                                <td class="text-center">
                                    @if($review->is_active)
                                        <span class="badge bg-success-subtle text-success px-3 py-1.5 rounded-pill fw-semibold">
                                            <i class="fas fa-check-circle me-1"></i>{{ __('Approved & Live') }}
                                        </span>
                                    @else
                                        <span class="badge bg-warning-subtle text-dark px-3 py-1.5 rounded-pill fw-semibold">
                                            <i class="fas fa-hourglass-half me-1"></i>{{ __('Pending Approval') }}
                                        </span>
                                    @endif
                                </td>

                                <!-- Actions -->
                                <td class="text-end pe-3">
                                    <div class="btn-group btn-group-sm">
                                        @if(!$review->is_active)
                                            <!-- Approve Action -->
                                            <form action="{{ route('admin.review.approve', $review->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-success" title="{{ __('Approve & Publish') }}">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                        @else
                                            <!-- Reject Action -->
                                            <form action="{{ route('admin.review.reject', $review->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-warning" title="{{ __('Hide / Reject') }}">
                                                    <i class="fas fa-ban"></i>
                                                </button>
                                            </form>
                                        @endif

                                        <!-- Reply Modal Trigger -->
                                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#replyModal{{ $review->id }}" title="{{ __('Reply to Customer') }}">
                                            <i class="fas fa-reply"></i>
                                        </button>

                                        <!-- Delete Action -->
                                        <form action="{{ route('admin.review.destroy', $review->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Are you sure you want to permanently delete this review?') }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="{{ __('Delete Review') }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>

                                    <!-- Reply Modal -->
                                    <div class="modal fade text-start" id="replyModal{{ $review->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content border-0 shadow">
                                                <form action="{{ route('admin.review.reply', $review->id) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-header">
                                                        <h6 class="modal-title fw-bold text-dark">
                                                            <i class="fas fa-reply text-primary me-2"></i>{{ __('Official Response to Review #') }}{{ $review->id }}
                                                        </h6>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="mb-3 p-2.5 bg-light rounded-2">
                                                            <div class="small fw-semibold text-dark">{{ $review->customer?->user?->name }}:</div>
                                                            <div class="small text-muted italic">"{{ $review->description }}"</div>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold small text-dark">{{ __('Official Response / Reply Message:') }}</label>
                                                            <textarea name="reply" class="form-control" rows="4" placeholder="{{ __('Write your official response to the customer (e.g. Thank you for your feedback! We are glad you enjoyed the product...)...') }}" required>{{ $review->reply }}</textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                                                        <button type="submit" class="btn btn-primary btn-sm px-3">
                                                            <i class="fas fa-paper-plane me-1"></i>{{ __('Save & Publish Reply') }}
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
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <div class="avatar-lg bg-light text-muted rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                        <i class="fas fa-comments fa-2x"></i>
                                    </div>
                                    <h6>{{ __('No customer reviews found') }}</h6>
                                    <p class="small mb-0">{{ __('Customer reviews matching the selected filter criteria will appear here.') }}</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-end mb-4">
        {{ $reviews->links() }}
    </div>
</div>
@endsection
