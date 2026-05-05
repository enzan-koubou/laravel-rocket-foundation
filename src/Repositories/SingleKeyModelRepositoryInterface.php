<?php

declare(strict_types=1);

namespace EnzanRocket\Foundation\Repositories;

use EnzanRocket\Foundation\Models\Base;
use Illuminate\Database\Eloquent\Collection;

interface SingleKeyModelRepositoryInterface extends BaseRepositoryInterface
{
    public function getPrimaryKey(): string;

    public function find(int|string $id): ?Base;

    public function allByIds(array $ids, ?string $order = null, ?string $direction = null, bool $reorder = false): Collection;

    public function countByIds(array $ids): int;

    public function getByIds(array $ids, ?string $order = null, ?string $direction = null, ?int $offset = null, ?int $limit = null): Collection;

    public function create(array $input): Base|false;

    public function dryUpdate(Base $model, array $input): Base;

    public function update(Base $model, array $input): Base|false;

    public function save(Base $model): Base|false;

    /**
     * @param Base $model
     *
     * @return bool
     */
    public function delete(Base $model): bool;

    public function updateMultipleEntries(int $id, string $parentColumnName, string $targetColumnName, array $list): bool;
}
