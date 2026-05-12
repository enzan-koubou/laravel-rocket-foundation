<?php

namespace EnzanRocket\Foundation\Services\Production;

use EnzanRocket\Foundation\Services\ExportServiceInterface;

class ExportService extends BaseService implements ExportServiceInterface
{
    public function __construct() {}

    public function getModel(string $modelName): ?\EnzanRocket\Foundation\Models\Base
    {
        $modelClass = '\\App\\Models\\'.$modelName;
        if (! class_exists($modelClass)) {
            return null;
        }

        /** @var \EnzanRocket\Foundation\Models\Base $modelInstance */
        $modelInstance = new $modelClass;

        return $modelInstance;
    }

    public function getRepository(string $modelName): ?\EnzanRocket\Foundation\Repositories\Eloquent\SingleKeyModelRepository
    {
        $repositoryInterfaceClass = 'App\\Repositories\\'.$modelName.'RepositoryInterface';

        if (! interface_exists($repositoryInterfaceClass)) {
            return null;
        }

        /** @var \EnzanRocket\Foundation\Repositories\Eloquent\SingleKeyModelRepository $repository */
        $repository = app()->make($repositoryInterfaceClass);

        return $repository;
    }

    public function selectColumns(\Illuminate\Database\Eloquent\Model $model): array
    {
        $columns = $model->getFillable();
        $columns = array_merge(['id'], $columns);

        return array_merge($columns, ['created_at', 'updated_at']);
    }

    public function checkModelExportable(string $modelName): bool
    {
        $model = $this->getModel($modelName);
        $repository = $this->getRepository($modelName);

        return ! empty($model) && ! empty($repository);
    }
}
