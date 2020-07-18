<?php

namespace OZiTAG\Tager\Backend\Pages\Repositories;

use OZiTAG\Tager\Backend\Core\Repositories\EloquentRepository;
use OZiTAG\Tager\Backend\Pages\Models\TagerPage;

class PagesRepository extends EloquentRepository
{
    public function __construct(TagerPage $model)
    {
        parent::__construct($model);
    }

    public function findByAlias($alias)
    {
        return TagerPage::whereAlias($alias)->first();
    }
}
