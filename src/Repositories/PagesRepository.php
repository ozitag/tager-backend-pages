<?php

namespace OZiTAG\Tager\Backend\Pages\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
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

    public function findByUrlPath($urlPath): ?TagerPage
    {
        $urlPath = preg_replace('#\/+$#si', '', $urlPath);
        if (empty($urlPath)) {
            $urlPath = '/';
        }

        if (substr($urlPath, 0, 1) !== '/') {
            $urlPath = '/' . $urlPath;
        }

        return $this->model::query()->whereUrlPath($urlPath)->first();
    }

    public function search($searchQuery, $offset = 0, $limit = null)
    {
        $query = $this->model::query();

        if ($offset !== null) {
            $query->skip($offset);
            $query->take(999999999);
        }

        if ($limit !== null) {
            $query->take($limit);
        }

        $query->where('title', 'LIKE', '%' . $searchQuery . '%')
            ->orWhere('excerpt', 'LIKE', '%' . $searchQuery . '%')
            ->orWhere('body', 'LIKE', '%' . $searchQuery . '%');

        return $query->get();
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
            case 'with-children':
                if ($value == 0) {
                    return $builder;
                }
                return $builder->groupBy('tager_pages.id')->select('tager_pages.*')
                    ->join('tager_pages as tp2', 'tager_pages.id','=','tp2.parent_id');
            case 'parent':
                $allChildrenIds = [];
                $childrenIds = explode(',', $value);
                do {
                    $allChildrenIds = array_merge($allChildrenIds, $childrenIds);
                    $childrenIds = DB::table('tager_pages')
                        ->whereIn('parent_id', $childrenIds)
                        ->select('id')->pluck('id')->toArray();
                } while (!empty($childrenIds));

                return $builder->whereIn('id', explode(',', $value))
                    ->orWhereIn('parent_id', $allChildrenIds);
            default:
                return $builder;
        }
    }

    public function findByTemplate(string $template): Builder
    {
        return $this->model::query()->where('template', '=', $template);
    }
}
