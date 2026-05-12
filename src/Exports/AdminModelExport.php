<?php

namespace EnzanRocket\Foundation\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;

class AdminModelExport implements FromQuery
{
    use Exportable;

    protected string $modelName;

    protected \EnzanRocket\Foundation\Services\ExportServiceInterface $exportService;

    public function __construct(string $modelName)
    {
        $this->modelName = $modelName;
        $this->exportService = app()->make(\EnzanRocket\Foundation\Services\ExportServiceInterface::class);
    }

    public function query(): mixed
    {
        $modelInstance = $this->exportService->getModel($this->modelName);
        if (empty($modelInstance)) {
            return null;
        }
        $repository = $this->exportService->getRepository($this->modelName);
        if (empty($repository)) {
            return null;
        }

        return $repository->allByFilterQuery([], 'id', 'asc');
    }
}
