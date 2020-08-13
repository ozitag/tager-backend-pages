<?php

namespace OZiTAG\Tager\Backend\Pages\Jobs;

use OZiTAG\Tager\Backend\Core\Jobs\Job;
use OZiTAG\Tager\Backend\Pages\Models\TagerPage;
use OZiTAG\Tager\Backend\Shop\Models\TagerShopCategory;

class CheckIfCanDeletePageJob extends Job
{
    private $model;

    public function __construct(TagerPage $model)
    {
        $this->model = $model;
    }

    public function handle()
    {
        if ($this->model->descendants->isEmpty() == false) {
            return 'It is not available to remove page with descendants';
        }

        return true;
    }
}
