<?php

declare(strict_types=1);

namespace App\Contracts\Repositories;

use App\Models\Base;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Traversable;

interface BaseRepositoryInterface
{
    /**
     * Get Empty Array or Traversable Object.
     */
    public function getEmptyList(): Collection|iterable;

    /**
     * Get All Model's query.
     */
    public function allByFilterQuery(array $filter, string $order = null, string $direction = null): Builder;

    /**
     * Get All Models.
     */
    public function all(string $order = null, string $direction = null): Collection|iterable;

    /**
     * Get All Enabled Models.
     */
    public function allEnabled(string $order = null, string $direction = null): Collection|iterable;

    /**
     * Get All Models with filter conditions.
     */
    public function allByFilter(array $filter, string $order = null, string $direction = null): Collection|iterable;

    /**
     * Get Models with Order.
     */
    public function get(string $order, string $direction, int $offset, int $limit, mixed $before = 0, mixed $after = 0): Collection|iterable;

    /**
     * Get Models with Order.
     */
    public function getByFilter(array $filter, string $order, string $direction, int $offset, int $limit, mixed $before = 0, mixed $after = 0): Collection|iterable;

    /**
     * Get Models with Order.
     *
     * @return array|Base[]|Collection|\Illuminate\Support\Collection|\Traversable
     */
    public function getEnabled(string $order, string $direction, int $offset, int $limit, mixed $before = 0, mixed $after = 0): \Illuminate\Support\Collection|array;

    public function count(): int;

    public function countByFilter(array $filter): int;

    public function countEnabled(): int;

    public function firstByFilter(array $filter): Model|Base;

    public function updateByFilter(array $filter, array $values): int;

    public function getSQLByFilter(array $filter): string;

    public function deleteByFilter(array $filter): int;

    public function getModelClassName(): string;

    /**
     * Get Empty Array or Traversable Object.
     */
    public function getBlankModel(): Base|Builder;

    /**
     * Get base query for fetching data.
     */
    public function getBaseQuery(): Builder;

    public function firstOrNew(array $attributes, array $values = []): Base;

    public function firstOrCreate(array $attributes, array $values = []): Base;

    public function updateOrCreate(array $attributes, array $values = []): Base;

    /**
     * Get a rule for Validator.
     */
    public function rules(): array;

    /**
     * Get a messages for Validator.
     */
    public function messages(): array;

    public function pluck(\Illuminate\Support\Collection $collection, string $value, string $key = null): \Illuminate\Support\Collection;
}
