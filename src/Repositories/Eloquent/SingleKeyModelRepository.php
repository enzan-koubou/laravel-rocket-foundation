<?php

declare(strict_types=1);

namespace EnzanRocket\Foundation\Repositories\Eloquent;

use EnzanRocket\Foundation\Models\Base;
use EnzanRocket\Foundation\Repositories\SingleKeyModelRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class SingleKeyModelRepository extends BaseRepository implements SingleKeyModelRepositoryInterface
{
    public function find(int|string $id): ?Base
    {
        $modelClass = $this->getModelClassName();
        if ($this->cacheEnabled) {
            $key = $this->getCacheKey([$id]);

            return cache()->remember($key, $this->cacheLifeTime, function () use ($id, $modelClass): ?Base {
                return $modelClass::find($id);
            });
        }

        return $modelClass::find($id);
    }

    public function allByIds(array $ids, ?string $order = null, ?string $direction = null, bool $reorder = false): Collection
    {
        if (count($ids) === 0) {
            return $this->getEmptyList();
        }
        $modelClass = $this->getModelClassName();
        $primaryKey = $this->getPrimaryKey();

        $query = $modelClass::whereIn($primaryKey, $ids);
        if (!empty($order)) {
            $direction = empty($direction) ? 'asc' : $direction;
            $query     = $query->orderBy($order, $direction);
        }

        $models = $query->get();

        if (!$reorder) {
            return $models;
        }

        $result = $this->getEmptyList();
        $map    = [];
        foreach ($models as $model) {
            $map[$model->id] = $model;
        }
        foreach ($ids as $id) {
            $model = $map[$id] ?? null;
            if (!empty($model)) {
                $result->push($model);
            }
        }

        return $result;
    }

    public function getPrimaryKey(): string
    {
        return $this->getBlankModel()->getPrimaryKey();
    }

    public function countByIds(array $ids): int
    {
        if (count($ids) === 0) {
            return 0;
        }
        $modelClass = $this->getModelClassName();
        $primaryKey = $this->getPrimaryKey();

        return $modelClass::whereIn($primaryKey, $ids)->count();
    }

    public function getByIds(array $ids, ?string $order = null, ?string $direction = null, ?int $offset = null, ?int $limit = null): Collection
    {
        if (count($ids) === 0) {
            return $this->getEmptyList();
        }
        $modelClass = $this->getModelClassName();
        $primaryKey = $this->getPrimaryKey();

        $query = $modelClass::whereIn($primaryKey, $ids);
        if (!empty($order)) {
            $direction = empty($direction) ? 'asc' : $direction;
            $query     = $query->orderBy($order, $direction);
        }
        if (!is_null($offset) && !is_null($limit)) {
            $query = $query->offset($offset)->limit($limit);
        }

        return $query->get();
    }

    public function create(array $input): Base|false
    {
        return $this->update($this->getBlankModel(), $input);
    }

    public function dryUpdate(Base $model, array $input): Base
    {
        foreach ($model->getFillable() as $column) {
            if (array_key_exists($column, $input)) {
                $newData = Arr::get($input, $column);
                if ($model->$column !== $newData) {
                    $model->$column = Arr::get($input, $column);
                }
            }
        }

        return $model;
    }

    public function update(Base $model, array $input): Base|false
    {
        $model = $this->dryUpdate($model, $input);

        if ($this->cacheEnabled) {
            $primaryKey = $this->getPrimaryKey();
            $key        = $this->getCacheKey([$model->$primaryKey]);
            cache()->forget($key);
        }

        return $this->save($model);
    }

    public function save(Base $model): Base|false
    {
        if (!$model->save()) {
            return false;
        }

        if ($this->cacheEnabled) {
            $primaryKey = $this->getPrimaryKey();
            $key        = $this->getCacheKey([$model->$primaryKey]);
            cache()->forget($key);
        }

        return $model;
    }

    public function delete(Base $model): bool
    {
        if ($this->cacheEnabled) {
            $primaryKey = $this->getPrimaryKey();
            $key        = $this->getCacheKey([$model->$primaryKey]);
            cache()->forget($key);
        }

        return (bool) $model->delete();
    }

    public function __call(string $method, array $parameters): mixed
    {
        if (Str::startsWith($method, 'getBy')) {
            return $this->dynamicGet($method, $parameters);
        }

        if (Str::startsWith($method, 'allBy')) {
            return $this->dynamicAll($method, $parameters);
        }

        if (Str::startsWith($method, 'countBy')) {
            return $this->dynamicCount($method, $parameters);
        }

        if (Str::startsWith($method, 'findBy')) {
            return $this->dynamicFind($method, $parameters);
        }

        if (Str::startsWith($method, 'deleteBy')) {
            return $this->dynamicDelete($method, $parameters);
        }

        if (Str::startsWith($method, 'updateBy')) {
            return $this->dynamicUpdate($method, $parameters);
        }

        $className = static::class;
        throw new \BadMethodCallException("Call to undefined method {$className}::{$method}()");
    }

    public function updateMultipleEntries(int $id, string $parentColumnName, string $targetColumnName, array $list): bool
    {
        $currentList = $this->allByFilter([$parentColumnName => $id])->pluck($targetColumnName)->toArray();
        $deletes     = array_diff($currentList, $list);
        $adds        = array_diff($list, $currentList);

        if (count($deletes) > 0) {
            $this->getBlankModel()->newQuery()
                ->where($parentColumnName, $id)
                ->whereIn($targetColumnName, $deletes)
                ->delete();
        }

        if (count($adds) > 0) {
            foreach ($adds as $data) {
                $this->create([
                    $parentColumnName => $id,
                    $targetColumnName => $data,
                ]);
            }
        }

        return true;
    }

    private function dynamicGet(string $method, array $parameters): Collection
    {
        $finder          = substr($method, 5);
        $segments        = preg_split('/(And|Or)(?=[A-Z])/', $finder, -1);
        $conditionCount  = count($segments);
        $conditionParams = array_splice($parameters, 0, $conditionCount);
        $model           = $this->getBlankModel();
        $whereMethod     = 'where'.$finder;
        $query           = call_user_func_array([$model, $whereMethod], $conditionParams);

        $order     = Arr::get($parameters, 0, 'id');
        $direction = Arr::get($parameters, 1, 'asc');
        $offset    = Arr::get($parameters, 2, 0);
        $limit     = Arr::get($parameters, 3, 10);

        if (!empty($order)) {
            $direction = empty($direction) ? 'asc' : $direction;
            $query     = $query->orderBy($order, $direction);
        }
        if (!is_null($offset) && !is_null($limit)) {
            $query = $query->offset($offset)->limit($limit);
        }

        return $query->get();
    }

    private function dynamicAll(string $method, array $parameters): Collection
    {
        $finder          = substr($method, 5);
        $segments        = preg_split('/(And|Or)(?=[A-Z])/', $finder, -1);
        $conditionCount  = count($segments);
        $conditionParams = array_splice($parameters, 0, $conditionCount);
        $model           = $this->queryOptions($this->getBlankModel()->newQuery());
        $whereMethod     = 'where'.$finder;
        $query           = call_user_func_array([$model, $whereMethod], $conditionParams);

        $order     = Arr::get($parameters, 0, 'id');
        $direction = Arr::get($parameters, 1, 'asc');

        return $query->orderBy($order, $direction)->get();
    }

    private function dynamicCount(string $method, array $parameters): int
    {
        $finder          = substr($method, 7);
        $segments        = preg_split('/(And|Or)(?=[A-Z])/', $finder, -1);
        $conditionCount  = count($segments);
        $conditionParams = array_splice($parameters, 0, $conditionCount);
        $model           = $this->getBlankModel();
        $whereMethod     = 'where'.$finder;
        $query           = call_user_func_array([$model, $whereMethod], $conditionParams);

        return $query->count();
    }

    private function dynamicFind(string $method, array $parameters): ?Base
    {
        $finder          = substr($method, 6);
        $segments        = preg_split('/(And|Or)(?=[A-Z])/', $finder, -1);
        $conditionCount  = count($segments);
        $conditionParams = array_splice($parameters, 0, $conditionCount);
        $model           = $this->queryOptions($this->getBlankModel()->newQuery());
        $whereMethod     = 'where'.$finder;
        $query           = call_user_func_array([$model, $whereMethod], $conditionParams);

        return $query->first();
    }

    private function dynamicDelete(string $method, array $parameters): int
    {
        $finder          = substr($method, 8);
        $segments        = preg_split('/(And|Or)(?=[A-Z])/', $finder, -1);
        $conditionCount  = count($segments);
        $conditionParams = array_splice($parameters, 0, $conditionCount);
        $model           = $this->getBlankModel();
        $whereMethod     = 'where'.$finder;
        $query           = call_user_func_array([$model, $whereMethod], $conditionParams);

        return $query->delete();
    }

    private function dynamicUpdate(string $method, array $parameters): ?int
    {
        $finder          = substr($method, 8);
        $segments        = preg_split('/(And|Or)(?=[A-Z])/', $finder, -1);
        $conditionCount  = count($segments);
        $conditionParams = array_splice($parameters, 0, $conditionCount);
        $model           = $this->queryOptions($this->getBlankModel()->newQuery());
        $whereMethod     = 'where'.$finder;
        $query           = call_user_func_array([$model, $whereMethod], $conditionParams);
        $updates         = Arr::get($parameters, 0);

        if (empty($updates)) {
            return null;
        }

        return $query->update($updates);
    }
}
