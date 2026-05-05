<?php

declare(strict_types=1);

namespace EnzanRocket\Foundation\Repositories\Eloquent;

use EnzanRocket\Foundation\Models\Base;
use EnzanRocket\Foundation\Repositories\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;

class BaseRepository implements BaseRepositoryInterface
{
    protected bool $cacheEnabled = false;

    protected string $cachePrefix = 'model';

    protected int $cacheLifeTime = 60; // Minutes

    protected array $querySearchTargets = [];

    public function getEmptyList(): Collection
    {
        return new Collection();
    }

    public function rules(): array
    {
        return [];
    }

    public function messages(): array
    {
        return [];
    }

    public function all(?string $order = null, ?string $direction = null): Collection
    {
        $model = $this->getBlankModel();
        if (!empty($order)) {
            $direction = empty($direction) ? 'asc' : $direction;
            $model     = $model->orderBy($order, $direction);
        }

        return $model->get();
    }

    public function allByFilter(array $filter, ?string $order = null, ?string $direction = null): Collection
    {
        $query = $this->buildQueryByFilter($this->getBlankModel()->newQuery(), $filter);
        $query = $this->buildOrder($query, $filter, $order, $direction);

        return $query->get();
    }

    public function allByFilterWithTrashed(array $filter, ?string $order = null, ?string $direction = null): Collection
    {
        $query = $this->buildQueryByFilter($this->getBlankModel()->newQuery(), $filter);
        $query = $this->buildOrder($query, $filter, $order, $direction);

        return $query->withTrashed()->get();
    }

    public function getModelClassName(): string
    {
        return get_class($this->getBlankModel());
    }

    public function getBlankModel(): Base
    {
        return new Base();
    }

    public function allEnabled(?string $order = null, ?string $direction = null): Collection
    {
        $model = $this->getBlankModel();
        $query = $model->where('is_enabled', '=', true);
        if (!empty($order)) {
            $direction = empty($direction) ? 'asc' : $direction;
            $query     = $query->orderBy($order, $direction);
        }

        return $query->get();
    }

    public function get(string $order = 'id', string $direction = 'asc', int $offset = 0, int $limit = 20): Collection
    {
        $model = $this->getBlankModel();

        return $model->orderBy($order, $direction)->skip($offset)->take($limit)->get();
    }

    public function getByFilter(array $filter, ?string $order = 'id', ?string $direction = 'asc', ?int $offset = 0, ?int $limit = 20): Collection
    {
        $query = $this->buildQueryByFilter($this->getBlankModel()->newQuery(), $filter);
        $query = $this->buildOrder($query, $filter, $order ?? 'id', $direction ?? 'asc');

        return $query->skip($offset ?? 0)->take($limit ?? 20)->get();
    }

    public function getByFilterWithTrashed(array $filter, ?string $order = 'id', ?string $direction = 'asc', ?int $offset = 0, ?int $limit = 20): Collection
    {
        $query = $this->buildQueryByFilter($this->getBlankModel()->newQuery(), $filter);
        $query = $this->buildOrder($query, $filter, $order ?? 'id', $direction ?? 'asc');

        return $query->withTrashed()->skip($offset ?? 0)->take($limit ?? 20)->get();
    }

    public function getEnabled(string $order = 'id', string $direction = 'asc', int $offset = 0, int $limit = 20): Collection
    {
        $model = $this->getBlankModel();

        return $model->where('is_enabled', '=', true)->orderBy($order, $direction)->skip($offset)->take($limit)->get();
    }

    public function count(): int
    {
        return $this->getBlankModel()->count();
    }

    public function countByFilter(array $filter): int
    {
        return $this->buildQueryByFilter($this->getBlankModel()->newQuery(), $filter)->count();
    }

    public function countEnabled(): int
    {
        return $this->getBlankModel()->where('is_enabled', '=', true)->count();
    }

    public function firstByFilter(array $filter): ?Base
    {
        return $this->buildQueryByFilter($this->getBlankModel()->newQuery(), $filter)->first();
    }

    public function firstByFilterWithTrashed(array $filter): ?Base
    {
        return $this->buildQueryByFilter($this->getBlankModel()->newQuery(), $filter)->withTrashed()->first();
    }

    public function updateByFilter(array $filter, array $values): int
    {
        return $this->buildQueryByFilter($this->getBlankModel()->newQuery(), $filter)->update($values);
    }

    public function getSQLByFilter(array $filter): string
    {
        return $this->buildQueryByFilter($this->getBlankModel()->newQuery(), $filter)->toSql();
    }

    public function deleteByFilter(array $filter): int
    {
        return $this->buildQueryByFilter($this->getBlankModel()->newQuery(), $filter)->delete();
    }

    public function pluck(Collection $collection, string $value, ?string $key = null): Collection
    {
        $items = [];
        foreach ($collection as $model) {
            if (empty($key)) {
                $items[] = $model->$value;
            } else {
                $items[$model->$key] = $model->$value;
            }
        }

        return Collection::make($items);
    }

    public function firstOrNew(array $attributes, array $values = []): Base
    {
        return $this->getBlankModel()->firstOrNew($attributes, $values);
    }

    public function firstOrCreate(array $attributes, array $values = []): Base
    {
        return $this->getBlankModel()->firstOrCreate($attributes, $values);
    }

    public function updateOrCreate(array $attributes, array $values = []): Base
    {
        return $this->getBlankModel()->updateOrCreate($attributes, $values);
    }

    protected function getCacheKey(array $ids): string
    {
        $key = $this->cachePrefix;
        foreach ($ids as $id) {
            $key .= '-'.$id;
        }

        return $key;
    }

    /**
     * Build a filtered query from an already-created Builder instance.
     *
     * Callers MUST pass $this->getBlankModel()->newQuery() (a fresh Builder).
     * Child overrides apply custom filter conditions then call
     * parent::buildQueryByFilter($query, $filter) — the Builder state is
     * preserved across the override chain, preventing query pollution.
     */
    protected function buildQueryByFilter(Builder $query, array $filter): Builder
    {
        $tableName = $query->getModel()->getTable();

        $query = $this->queryOptions($query);

        if (count($this->querySearchTargets) > 0 && array_key_exists('query', $filter)) {
            $searchWord = Arr::get($filter, 'query');
            if (!empty($searchWord)) {
                $query = $query->where(function (Builder $q) use ($searchWord): void {
                    foreach ($this->querySearchTargets as $index => $target) {
                        if ($index === 0) {
                            $q->where($target, 'LIKE', '%'.$searchWord.'%');
                        } else {
                            $q->orWhere($target, 'LIKE', '%'.$searchWord.'%');
                        }
                    }
                });
                unset($filter['query']);
            }
        }

        foreach ($filter as $column => $value) {
            if (is_array($value)) {
                $query = $query->whereIn($tableName.'.'.$column, $value);
            } else {
                $query = $query->where($tableName.'.'.$column, $value);
            }
        }

        return $query;
    }

    protected function buildOrder(Builder $query, array $filter, ?string $order, ?string $direction): Builder
    {
        if (!empty($order)) {
            $direction = empty($direction) ? 'asc' : $direction;
            $query     = $query->orderBy($order, $direction);
        }

        return $query;
    }

    protected function queryOptions(Builder $query): Builder
    {
        return $query;
    }
}
