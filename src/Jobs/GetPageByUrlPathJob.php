<?php

namespace OZiTAG\Tager\Backend\Pages\Jobs;

use OZiTAG\Tager\Backend\Core\Jobs\Job;
use OZiTAG\Tager\Backend\Pages\Repositories\PagesRepository;

class GetPageByUrlPathJob extends Job
{
    private $path;

    public function __construct($path)
    {
        $this->path = $path;
    }

    public function handle(PagesRepository $repository)
    {
        $model = $repository->findByUrlPath($this->path);

        if (!$model) {
            abort(404, __('tager-pages::errors.page_not_found'));
        }

        return $model;
    }
}
