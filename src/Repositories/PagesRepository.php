<?php

namespace OZiTAG\Tager\Backend\Pages\Repositories;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use OZiTAG\Tager\Backend\Core\Repositories\EloquentRepository;
use OZiTAG\Tager\Backend\Core\Repositories\IFilterable;
use OZiTAG\Tager\Backend\Core\Repositories\ISearchable;
use OZiTAG\Tager\Backend\Core\Repositories\ISortable;
use OZiTAG\Tager\Backend\Crud\Contracts\IRepositoryCrudTreeRepository;
use OZiTAG\Tager\Backend\Pages\Enums\PageStatus;
use OZiTAG\Tager\Backend\Pages\Models\TagerPage;

class PagesRepository extends EloquentRepository implements IRepositoryCrudTreeRepository, ISearchable, IFilterable, ISortable
{
    public function __construct(TagerPage $model)
    {
        parent::__construct($model);
    }

    public function queryByUrlPath(string $urlPath): Builder
    {
        $urlPath = preg_replace('#\/+$#si', '', $urlPath);
        if (empty($urlPath)) {
            $urlPath = '/';
        }

        if (substr($urlPath, 0, 1) !== '/') {
            $urlPath = '/' . $urlPath;
        }

        $urlPath = strtolower($urlPath);

        return $this->model::query()->whereUrlPath($urlPath);
    }

    public function findPublished()
    {
        return $this->builder()->where('status', PageStatus::Published->value);
    }

    public function findPublishedByUrlPath(string $urlPath): ?TagerPage
    {
        return $this->queryByUrlPath($urlPath)
            ->where('status', PageStatus::Published->value)
            ->first();
    }

    public function findByUrlPath($urlPath): ?TagerPage
    {
        return $this->queryByUrlPath($urlPath)->first();
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
        if (empty($query)) {
            return $builder;
        }

        $builder = $builder ? $builder->defaultOrder() : $this->model;

        return $builder->where(function ($builder) use ($query) {
            return $builder
                ->orWhere('title', 'LIKE', '%' . $query . '%')
                ->orWhere('url_path', 'LIKE', '%' . $query . '%');
        });
    }


    public function sort(?string $sort = null, Builder $builder = null): ?Builder
    {
        $builder = $builder ?: $this->builder();

        switch ($sort) {
            case 'date_desc':
                return $builder->reorder('datetime', 'desc');
            case 'date_asc':
                return $builder->reorder('datetime', 'asc');
            default:
                return $builder;
        }
    }

    public function filterByKey(Builder $builder, string $key, mixed $value): Builder
    {
        $builder = $builder ? $builder->defaultOrder() : $this->model;

        switch ($key) {
            case 'template':
                return $builder->whereIn('tager_pages.template', explode(',', $value));
            case 'status':
                return $builder->whereIn('tager_pages.status', explode(',', $value));
            case 'with-children':
                if ($value == 0) {
                    return $builder;
                }

                $columns = array_map(function ($column) {
                    return 'tager_pages.' . $column;
                }, DB::getSchemaBuilder()->getColumnListing('tager_pages'));

                $builder = $builder->groupBy($columns)->select('tager_pages.*')
                    ->whereNull('tp2.deleted_at')
                    ->join('tager_pages as tp2', 'tager_pages.id', '=', 'tp2.parent_id');

                $builder->reorder(
                    'tager_pages._lft',
                    'ASC'
                )->orderBy('tager_pages.id', 'asc');

                return $builder;
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
        return $this->model::query()->defaultOrder()->where('tager_pages.template', '=', $template);
    }
}
