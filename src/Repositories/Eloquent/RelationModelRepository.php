<?php

declare(strict_types=1);

namespace EnzanRocket\Foundation\Repositories\Eloquent;

use EnzanRocket\Foundation\Models\Base;
use EnzanRocket\Foundation\Repositories\RelationModelRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class RelationModelRepository extends SingleKeyModelRepository implements RelationModelRepositoryInterface
{
    protected string $parentKey = '';

    protected string $childKey = '';

    public function getRelationKeys(): array
    {
        return [$this->parentKey, $this->childKey];
    }

    public function findByRelationKeys(int|string $parentId, int|string $childId): ?Base
    {
        return $this->getBlankModel()->newQuery()
            ->where($this->getParentKey(), $parentId)
            ->where($this->getChildKey(), $childId)
            ->first();
    }

    public function getParentKey(): string
    {
        return $this->parentKey;
    }

    public function getChildKey(): string
    {
        return $this->childKey;
    }

    public function updateList(int|string $parentId, array $childIds): bool
    {
        $currentChildIds = $this->allByParentKey($parentId)->pluck($this->getChildKey())->toArray();
        $deletes         = array_diff($currentChildIds, $childIds);
        $adds            = array_diff($childIds, $currentChildIds);

        if (count($deletes) > 0) {
            $this->getBlankModel()->newQuery()
                ->where($this->getParentKey(), $parentId)
                ->whereIn($this->getChildKey(), $deletes)
                ->delete();
        }

        if (count($adds) > 0) {
            $parentKey = $this->getParentKey();
            $childKey  = $this->getChildKey();
            foreach ($adds as $childId) {
                $this->create([
                    $parentKey => $parentId,
                    $childKey  => $childId,
                ]);
            }
        }

        return true;
    }

    public function allByParentKey(int|string $parentId): Collection
    {
        return $this->getBlankModel()->newQuery()
            ->where($this->getParentKey(), $parentId)
            ->get();
    }
}
