<?php

namespace OZiTAG\Tager\Backend\Pages\Jobs;

use Illuminate\Queue\Jobs\Job;
use OZiTAG\Tager\Backend\Pages\Repositories\PagesRepository;

class GetPageByIdJob
{
    private $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function handle(PagesRepository $repository)
    {
        $model = $repository->find($this->id);

        if (!$model) {
            abort(404, 'Page not found');
        }

        return $model;
    }
}
