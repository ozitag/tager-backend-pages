<?php

namespace OZiTAG\Tager\Backend\Pages\Jobs;

use OZiTAG\Tager\Backend\Core\Jobs\Job;
use OZiTAG\Tager\Backend\Pages\Models\TagerPage;

class DeletePageJob extends Job
{
    private $model;

    public function __construct(TagerPage $model)
    {
        $this->model = $model;
    }

    public function handle()
    {
        $this->model->delete();
    }
}
