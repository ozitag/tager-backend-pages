<?php

namespace OZiTAG\Tager\Backend\Pages\Jobs;

use OZiTAG\Tager\Backend\Pages\Repositories\PagesRepository;

class GetPageByUrlPathJob
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
            abort(404, 'Page not found');
        }

        return $model;
    }
}