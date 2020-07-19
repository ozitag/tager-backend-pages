<?php

namespace OZiTAG\Tager\Backend\Pages\Jobs;

use Illuminate\Queue\Jobs\Job;
use OZiTAG\Tager\Backend\Pages\Models\TagerPage;
use OZiTAG\Tager\Backend\Pages\Repositories\PagesRepository;

class DeletePageJob
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
