<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\Repositories\SingleKeyModelRepositoryInterface;
use App\Models\Base;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class SingleKeyModelRepository extends BaseRepository implements SingleKeyModelRepositoryInterface
{
    public function __call($method, $parameters)
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

    public function find(int $id): Base|null|Model
    {
        $query = $this->getBaseQuery();
        if ($this->cacheEnabled) {
            $key = $this->getCacheKey([$id]);

            return cache()->remember($key, $this->cacheLifeTime, function () use ($id, $query) {
                $query = $query->where($this->getPrimaryKey(), $id);
                $query = $this->queryOptions($query);

                return $query->first();
            });
        } else {
            $query = $query->where($this->getPrimaryKey(), $id);
            $query = $this->queryOptions($query);

            return $query->first();
        }
    }

    public function allByIds(array $ids, string $order = null, string $direction = null, bool $reorder = false): Collection|array
    {
        if (0 === \count($ids)) {
            return $this->getEmptyList();
        }
        $modelClass = $this->getModelClassName();
        $primaryKey = $this->getPrimaryKey();

        $query = $modelClass::whereIn($primaryKey, $ids);
        if (!empty($order)) {
            $direction = empty($direction) ? 'asc' : $direction;
            $query = $query->orderBy($order, $direction);
        }

        $query = $this->queryOptions($query);

        $models = $query->get();

        if (!$reorder) {
            return $models;
        }

        $result = $this->getEmptyList();
        $map = [];
        foreach ($models as $model) {
            $map[$model->id] = $model;
        }
        foreach ($ids as $id) {
            $model = $map[$id];
            if (!empty($model)) {
                $result->push($model);
            }
        }

        return $result;
    }

    public function getPrimaryKey(): string
    {
        $model = $this->getBlankModel();

        return $model->getPrimaryKey();
    }

    public function countByIds($ids): int
    {
        if (0 === \count($ids)) {
            return 0;
        }
        $modelClass = $this->getModelClassName();
        $primaryKey = $this->getPrimaryKey();

        return $modelClass::whereIn($primaryKey, $ids)->count();
    }

    public function getByIds(array $ids, string $order = null, string $direction = null, int $offset = null, int $limit = null): Collection|array
    {
        if (0 === \count($ids)) {
            return $this->getEmptyList();
        }
        $modelClass = $this->getModelClassName();
        $primaryKey = $this->getPrimaryKey();

        $query = $modelClass::whereIn($primaryKey, $ids);
        if (!empty($order)) {
            $direction = empty($direction) ? 'asc' : $direction;
            $query = $query->orderBy($order, $direction);
        }
        if (null !== $offset && null !== $limit) {
            $query = $query->offset($offset)->limit($limit);
        }

        $query = $this->queryOptions($query);

        return $query->get();
    }

    public function create(array $input): Base
    {
        $model = $this->getBlankModel();

        return $this->update($model, $input);
    }

    public function dryUpdate(Base $model, array $input): Base
    {
        foreach ($model->getFillable() as $column) {
            if (\array_key_exists($column, $input)) {
                $newData = Arr::get($input, $column);
                if ($model->{$column} !== $newData) {
                    $model->{$column} = Arr::get($input, $column);
                }
            }
        }

        return $model;
    }

    public function update(Base $model, array $input): ?Base
    {
        $model = $this->dryUpdate($model, $input);

        if ($this->cacheEnabled) {
            $primaryKey = $this->getPrimaryKey();
            $key = $this->getCacheKey([$model->{$primaryKey}]);
            cache()->forget($key);
        }

        return $this->save($model);
    }

    public function save(Base $model): ?Base
    {
        if (!$model->save()) {
            return null;
        }

        if ($this->cacheEnabled) {
            $primaryKey = $this->getPrimaryKey();
            $key = $this->getCacheKey([$model->{$primaryKey}]);
            cache()->forget($key);
        }

        return $model;
    }

    public function delete(Base $model): bool
    {
        if ($this->cacheEnabled) {
            $primaryKey = $this->getPrimaryKey();
            $key = $this->getCacheKey([$model->{$primaryKey}]);
            cache()->forget($key);
        }

        return $model->delete();
    }

    public function updateMultipleEntries(int $id, string $parentColumnName, string $targetColumnName, array $list): bool
    {
        return $this->updateMultipleEntriesWithFilter([$parentColumnName => $id], $targetColumnName, $list);
    }

    public function updateMultipleEntriesWithFilter(array $filter, string $targetColumnName, array $list): bool
    {
        $currentList = $this->allByFilter($filter)->pluck($targetColumnName)->toArray();
        $deletes = array_diff($currentList, $list);
        $adds = array_diff($list, $currentList);

        if (\count($deletes) > 0) {
            $query = $this->getBaseQuery();
            foreach ($filter as $column => $value) {
                $query->where($column, $value);
            }
            $query->whereIn($targetColumnName, $deletes)->delete();
        }

        if (\count($adds) > 0) {
            foreach ($adds as $data) {
                $this->create(array_merge($filter, [
                    $targetColumnName => $data,
                ]));
            }
        }

        return true;
    }

    private function dynamicGet(string $method, array $parameters): Collection
    {
        $finder = substr($method, 5);
        $segments = preg_split('/(And|Or)(?=[A-Z])/', $finder, -1);
        $conditionCount = \count($segments);
        $conditionParams = array_splice($parameters, 0, $conditionCount);
        $model = $this->getBaseQuery();
        $whereMethod = 'where'.$finder;
        $query = \call_user_func_array([$model, $whereMethod], $conditionParams);

        $order = Arr::get($parameters, 0, 'id');
        $direction = Arr::get($parameters, 1, 'asc');
        $offset = Arr::get($parameters, 2, 0);
        $limit = Arr::get($parameters, 3, 10);
        $before = Arr::get($parameters, 4, 0);
        $after = Arr::get($parameters, 5, 0);

        $query = $this->setBefore($query, $order, $direction, $before);
        $query = $this->setAfter($query, $order, $direction, $after);

        if (!empty($order)) {
            $direction = empty($direction) ? 'asc' : $direction;
            $query = $query->orderBy($order, $direction);
        }
        if (null !== $offset && null !== $limit) {
            $query = $query->offset($offset)->limit($limit);
        }

        $query = $this->queryOptions($query);

        return $query->get();
    }

    private function dynamicAll(string $method, array $parameters): Collection
    {
        $finder = substr($method, 5);
        $segments = preg_split('/(And|Or)(?=[A-Z])/', $finder, -1);
        $conditionCount = \count($segments);
        $conditionParams = array_splice($parameters, 0, $conditionCount);
        $model = $this->getBaseQuery();
        $model = $this->queryOptions($model);
        $whereMethod = 'where'.$finder;
        $query = \call_user_func_array([$model, $whereMethod], $conditionParams);

        $order = Arr::get($parameters, 0, 'id');
        $direction = Arr::get($parameters, 1, 'asc');

        $query = $this->queryOptions($query);

        return $query->orderBy($order, $direction)->get();
    }

    private function dynamicCount(string $method, array $parameters): int
    {
        $finder = substr($method, 7);
        $segments = preg_split('/(And|Or)(?=[A-Z])/', $finder, -1);
        $conditionCount = \count($segments);
        $conditionParams = array_splice($parameters, 0, $conditionCount);
        $model = $this->getBaseQuery();
        $whereMethod = 'where'.$finder;
        $query = \call_user_func_array([$model, $whereMethod], $conditionParams);

        return $query->count();
    }

    private function dynamicFind(string $method, array $parameters): Builder|null
    {
        $finder = substr($method, 6);
        $segments = preg_split('/(And|Or)(?=[A-Z])/', $finder, -1);
        $conditionCount = \count($segments);
        $conditionParams = array_splice($parameters, 0, $conditionCount);
        $model = $this->getBaseQuery();
        $model = $this->queryOptions($model);
        $whereMethod = 'where'.$finder;
        $query = \call_user_func_array([$model, $whereMethod], $conditionParams);

        $query = $this->queryOptions($query);

        return $query->first();
    }

    private function dynamicDelete(string $method, array $parameters)
    {
        $finder = substr($method, 8);
        $segments = preg_split('/(And|Or)(?=[A-Z])/', $finder, -1);
        $conditionCount = \count($segments);
        $conditionParams = array_splice($parameters, 0, $conditionCount);
        $model = $this->getBaseQuery();
        $whereMethod = 'where'.$finder;
        $query = \call_user_func_array([$model, $whereMethod], $conditionParams);

        return $query->delete();
    }

    private function dynamicUpdate(string $method, array $parameters)
    {
        $finder = substr($method, 8);
        $segments = preg_split('/(And|Or)(?=[A-Z])/', $finder, -1);
        $conditionCount = \count($segments);
        $conditionParams = array_splice($parameters, 0, $conditionCount);
        $model = $this->getBaseQuery();
        $model = $this->queryOptions($model);
        $whereMethod = 'where'.$finder;
        $query = \call_user_func_array([$model, $whereMethod], $conditionParams);
        $updates = Arr::get($parameters, 0);

        if (empty($updates)) {
            return null;
        }

        return $query->update($updates);
    }
}
