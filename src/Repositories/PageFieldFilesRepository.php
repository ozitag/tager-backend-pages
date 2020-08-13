<?php

namespace OZiTAG\Tager\Backend\Pages\Repositories;

use OZiTAG\Tager\Backend\Core\Repositories\EloquentRepository;
use OZiTAG\Tager\Backend\Pages\Models\TagerPageField;
use OZiTAG\Tager\Backend\Pages\Models\TagerPageFieldFile;

class PageFieldFilesRepository extends EloquentRepository
{
    public function __construct(TagerPageFieldFile $model)
    {
        parent::__construct($model);
    }
}
