<?php

namespace OZiTAG\Tager\Backend\Pages\Repositories;

use OZiTAG\Tager\Backend\Core\Repositories\EloquentRepository;
use OZiTAG\Tager\Backend\Crud\Contracts\IRepositoryCrudTreeRepository;
use OZiTAG\Tager\Backend\Pages\Models\TagerPage;

class PagesRepository extends EloquentRepository implements IRepositoryCrudTreeRepository
{
    public function __construct(TagerPage $model)
    {
        parent::__construct($model);
    }

    public function toFlatTree()
    {
        return $this->model::query()->withDepth()->defaultOrder()->get()->toFlatTree();
    }

    public function findByAlias($alias)
    {
        return $this->model::query()->whereAlias($alias)->first();
    }

    public function findByUrlPath($urlPath)
    {
        return $this->model::query()->whereUrlPath($urlPath)->first();
    }
}
