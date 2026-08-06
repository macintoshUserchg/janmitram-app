{{--
    Sortable table column header with a Material-UI style arrow toggle.

    Usage (inside <th>):
        @include('admin.partials.sortable-header', ['label' => __('ID'), 'column' => 'id', 'route' => 'admin.stock-request.index', 'sort' => $sort, 'direction' => $direction])

    For routes with a model param (e.g. admin.warehouse.show) pass 'routeParam' => $warehouse->id.
    Expects the controller to provide $sort and $direction, order the paginated
    query by the whitelisted column + direction, and paginate with ->withQueryString().
--}}
@php
    $sort = $sort ?? 'id';
    $direction = $direction ?? 'asc';
    $newDirection = ($sort === $column && $direction === 'asc') ? 'desc' : 'asc';
    $active = $sort === $column;
    $arrow = $active
        ? ($direction === 'asc' ? 'fa-sort-up text-primary' : 'fa-sort-down text-primary')
        : 'fa-sort text-muted';

    $perPage = request('per_page') && is_numeric(request('per_page'))
        ? (int) request('per_page')
        : null;

    $params = array_filter([
        'sort' => $column,
        'direction' => $newDirection,
        'per_page' => $perPage,
    ]);

    $url = ! empty($routeParam)
        ? route($route, [$routeParam] + $params)
        : route($route, $params);
@endphp

<a href="{{ $url }}"
   class="text-decoration-none text-dark d-inline-flex align-items-center gap-1"
   title="{{ __('Sort by :column', ['column' => $label]) }}">
    <span>{{ $label }}</span>
    <i class="fas {{ $arrow }}"></i>
</a>