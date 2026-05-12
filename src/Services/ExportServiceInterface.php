<?php

namespace EnzanRocket\Foundation\Services;

interface ExportServiceInterface extends BaseServiceInterface
{
    public function getModel(string $modelName): ?\EnzanRocket\Foundation\Models\Base;

    public function getRepository(string $modelName): ?\EnzanRocket\Foundation\Repositories\Eloquent\SingleKeyModelRepository;

    /**
     * @param  \EnzanRocket\Foundation\Models\Base  $model
     */
    public function selectColumns(\Illuminate\Database\Eloquent\Model $model): array;

    public function checkModelExportable(string $modelName): bool;
}
