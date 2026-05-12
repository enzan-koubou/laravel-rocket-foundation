<?php

declare(strict_types=1);

namespace EnzanRocket\Foundation\Repositories;

use EnzanRocket\Foundation\Models\Base;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

interface BaseRepositoryInterface
{
    public function getEmptyList(): Collection;

    public function all(?string $order = null, ?string $direction = null): Collection;

    public function allEnabled(?string $order = null, ?string $direction = null): Collection;

    public function allByFilter(array $filter, ?string $order = null, ?string $direction = null): Collection;

    public function allByFilterWithTrashed(array $filter, ?string $order = null, ?string $direction = null): Collection;

    public function get(string $order = 'id', string $direction = 'asc', int $offset = 0, int $limit = 20): Collection;

    public function getByFilter(array $filter, string $order = 'id', string $direction = 'asc', int $offset = 0, int $limit = 20): Collection;

    public function getByFilterWithTrashed(array $filter, string $order = 'id', string $direction = 'asc', int $offset = 0, int $limit = 20): Collection;

    /**
     * Get Models with Order.
     *
     * @param string $order
     * @param string $direction
     * @param int    $offset
     * @param int    $limit
     */
    public function getEnabled(string $order = 'id', string $direction = 'asc', int $offset = 0, int $limit = 20): Collection;

    public function count(): int;

    public function countByFilter(array $filter): int;

    public function countEnabled(): int;

    public function firstByFilter(array $filter): ?Base;

    public function firstByFilterWithTrashed(array $filter): ?Base;

    public function updateByFilter(array $filter, array $values): int;

    public function getSQLByFilter(array $filter): string;

    public function allByFilterQuery(array $filter, ?string $order = null, ?string $direction = null): Builder;

    public function deleteByFilter(array $filter): int;

    public function getModelClassName(): string;

    public function getBlankModel(): Base;

    public function firstOrNew(array $attributes, array $values = []): Base;

    public function firstOrCreate(array $attributes, array $values = []): Base;

    public function updateOrCreate(array $attributes, array $values = []): Base;

    public function rules(): array;

    public function messages(): array;

    public function pluck(Collection $collection, string $value, ?string $key = null): Collection;
}
