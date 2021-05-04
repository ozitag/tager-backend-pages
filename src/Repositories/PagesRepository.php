<?php

namespace OZiTAG\Tager\Backend\Pages\Repositories;

use Illuminate\Database\Eloquent\Builder;
use OZiTAG\Tager\Backend\Core\Repositories\EloquentRepository;
use OZiTAG\Tager\Backend\Core\Repositories\IFilterable;
use OZiTAG\Tager\Backend\Core\Repositories\ISearchable;
use OZiTAG\Tager\Backend\Crud\Contracts\IRepositoryCrudTreeRepository;
use OZiTAG\Tager\Backend\Pages\Models\TagerPage;

class PagesRepository extends EloquentRepository implements IRepositoryCrudTreeRepository, ISearchable, IFilterable
{
    public function __construct(TagerPage $model)
    {
        parent::__construct($model);
    }

    public function findByAlias($alias): ?TagerPage
    {
        return $this->model::query()->whereAlias($alias)->first();
    }

    public function findByUrlPath($urlPath): ?TagerPage
    {
        return $this->model::query()->whereUrlPath($urlPath)->first();
    }

    public function searchByQuery(?string $query, Builder $builder = null): ?Builder
    {
        $builder = $builder ? $builder : $this->model;

        return $builder
            ->orWhere('title', 'LIKE', '%' . $query . '%')
            ->orWhere('url_path', 'LIKE', '%' . $query . '%');
    }

    public function filterByKey(Builder $builder, string $key, mixed $value): Builder
    {
        switch ($key) {
            case 'template':
                return $builder->whereIn('template', explode(',', $value));
            default:
                return $builder;
        }
    }

}
