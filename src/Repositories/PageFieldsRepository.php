<?php

namespace OZiTAG\Tager\Backend\Pages\Repositories;

use OZiTAG\Tager\Backend\Core\Repositories\EloquentRepository;
use OZiTAG\Tager\Backend\Pages\Models\TagerPageField;

class PageFieldsRepository extends EloquentRepository
{
    public function __construct(TagerPageField $model)
    {
        parent::__construct($model);
    }

    public function removeByPageId($pageId)
    {
        return TagerPageField::wherePageId($pageId)->delete();
    }
}
