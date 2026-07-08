<?php

namespace App\Support\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Clean local base repository.
 *
 * SECURITY / PROVENANCE NOTICE
 * ----------------------------
 * This class REPLACES the closed-source base `Abedin\Maker\Repositories\Repository`
 * that shipped inside the malicious `joynala/maker` Composer package.
 *
 * It is an AUDITED EXTRACTION of that package's `src/Repositories/Repository.php`
 * at the exact pinned commit the product used
 * (joynal-a/maker @ feab31317146bc947982d3ac2e854bbcca5b17a8). That file was
 * reviewed in full and contains ONLY standard Eloquent calls — no network,
 * shell, filesystem, or encryption logic. The malicious code lived in sibling
 * files (PushManager, DestroyTrait, ManagerTrait, SetPurchaseKey), which are
 * intentionally NOT carried over.
 *
 * The method surface below is identical to the original so the 53 child
 * repositories behave exactly as built. `all()`, `paginate()` and `store()`
 * are additive conveniences (not in the original) and are unused by the
 * current data layer; they are safe no-conflict helpers.
 *
 * @see app/Repositories
 */
abstract class Repository
{
    /**
     * The fully-qualified Eloquent model class this repository wraps.
     */
    abstract public static function model();

    /**
     * A fresh query builder for the underlying model.
     */
    public static function query(): Builder
    {
        return static::model()::query();
    }

    /**
     * All records, newest first.
     */
    public static function getAll(): Collection
    {
        return static::model()::latest()->get();
    }

    /**
     * Count of all records.
     */
    public static function countAll(): int
    {
        return static::query()->count();
    }

    /**
     * The first record.
     */
    public static function first(): ?Model
    {
        return static::query()->first();
    }

    /**
     * Find a single record by primary key.
     *
     * @param  mixed  $primaryKey
     */
    public static function find($primaryKey): ?Model
    {
        return static::query()->find($primaryKey);
    }

    /**
     * Find a single record by primary key or abort 404.
     *
     * @param  mixed  $primaryKey
     */
    public static function findOrFail($primaryKey)
    {
        return static::query()->findOrFail($primaryKey);
    }

    /**
     * Delete a record (or records) by primary key.
     *
     * @param  mixed  $primaryKey
     */
    public static function delete($primaryKey): bool
    {
        return (bool) static::model()::destroy($primaryKey);
    }

    /**
     * Create and persist a new record.
     *
     * @param  array<string, mixed>  $data
     */
    public static function create(array $data): Model
    {
        return static::query()->create($data);
    }

    /**
     * Update an existing model instance.
     *
     * @param  array<string, mixed>  $data
     */
    public static function update(Model $model, array $data): bool
    {
        return $model->update($data);
    }

    // ----- additive conveniences (not in the original; unused by current repos) -----

    /**
     * All records (unordered).
     */
    public static function all(): Collection
    {
        return static::query()->get();
    }

    /**
     * Paginate the records.
     */
    public static function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return static::query()->paginate($perPage);
    }

    /**
     * Alias of create().
     *
     * @param  array<string, mixed>  $data
     */
    public static function store(array $data): Model
    {
        return static::create($data);
    }
}
