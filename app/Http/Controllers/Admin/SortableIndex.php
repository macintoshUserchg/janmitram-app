<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use Illuminate\Database\Eloquent\Builder;

/**
 * Shared sortable-list behaviour: validates the whitelisted sort column and
 * direction from the query string, applies it to the query, and exposes the
 * values to the view for the sortable-header partial.
 */
trait SortableIndex
{
    /** @var array<int, string> Columns a given controller allows sorting by (override as needed). */
    protected array $sortableColumns = ['id', 'created_at', 'updated_at'];

    public function resolveSort(): array
    {
        $sort = in_array(request('sort'), $this->sortableColumns, true) ? request('sort') : 'id';
        $direction = request('direction') === 'desc' ? 'desc' : 'asc';

        return [$sort, $direction];
    }

    /** Whitelisted page sizes offered by the pagination partial. */
    protected array $perPageOptions = [5, 10, 15, 20, 25, 50, 100];

    /**
     * Resolve a validated per-page value from the query string.
     */
    protected function resolvePerPage(int $default = 15): int
    {
        $perPage = (int) request('per_page', $default);

        return in_array($perPage, $this->perPageOptions, true) ? $perPage : $default;
    }

    protected function applySort(Builder $query, ?string $sort = null, ?string $direction = 'asc'): Builder
    {
        $sort = in_array($sort, $this->sortableColumns, true) ? $sort : 'id';

        return $query->orderBy($sort, $direction === 'desc' ? 'desc' : 'asc');
    }
}
