<?php

declare(strict_types=1);

namespace EnzanRocket\Foundation\Repositories;

use EnzanRocket\Foundation\Models\Base;
use Illuminate\Database\Eloquent\Collection;

interface RelationModelRepositoryInterface extends SingleKeyModelRepositoryInterface
{
    public function getRelationKeys(): array;

    public function getParentKey(): string;

    public function getChildKey(): string;

    public function findByRelationKeys(int|string $parentKey, int|string $childKey): ?Base;

    public function allByParentKey(int|string $parentKey): Collection;

    public function updateList(int|string $parentKey, array $childKeys): bool;
}
