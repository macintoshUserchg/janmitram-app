{{--
    Consistent Material-style pagination footer for admin list tables.

    Usage (in the table's card-footer, replacing a bare ->links() call):
        @include('admin.partials.pagination', ['paginator' => $warehouses])

    Always renders a "Showing X–Y of Z" summary plus a rows-per-page selector so the
    footer is visible even on a single page; page links appear only when they exist.
    Sorting params (sort/direction) are preserved via hidden form inputs.
--}}
@php
    $total = $paginator->total();
    $from = $paginator->firstItem() ?? 0;
    $to = $paginator->lastItem() ?? 0;
    $perPage = (int) $paginator->perPage();
    $options = array_values(array_unique(array_merge([5, 10, 15, 20, 25, 50, 100], [$perPage])));
    sort($options);
    $query = request()->query();
    unset($query['page']);
@endphp

<div class="d-flex flex-wrap align-items-center justify-content-between gap-2 px-3 py-3">
    <div class="small text-muted">
        {{ __('Showing') }} <strong>{{ $from }}</strong>–<strong>{{ $to }}</strong> {{ __('of') }} <strong>{{ $total }}</strong> {{ __('records') }}
    </div>

    <div class="d-flex align-items-center gap-3">
        <form method="GET" action="{{ url()->current() }}" class="d-flex align-items-center gap-1 mb-0">
            @foreach($query as $key => $value)
                @if(is_array($value))
                    @foreach($value as $item)
                        <input type="hidden" name="{{ $key }}[]" value="{{ $item }}">
                    @endforeach
                @else
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endif
            @endforeach
            <label for="per_page_{{ $paginator->currentPage() }}" class="small text-muted mb-0">{{ __('Rows per page') }}</label>
            <select name="per_page" id="per_page_{{ $paginator->currentPage() }}"
                    class="form-select form-select-sm w-auto" onchange="this.form.submit()">
                @foreach($options as $option)
                    <option value="{{ $option }}" {{ $perPage === $option ? 'selected' : '' }}>{{ $option }}</option>
                @endforeach
            </select>
        </form>

        @if($paginator->hasPages())
            {{ $paginator->links() }}
        @endif
    </div>
</div>