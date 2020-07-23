<?php

namespace OZiTAG\Tager\Backend\Pages\Jobs;

use OZiTAG\Tager\Backend\Core\Jobs\Job;
use OZiTAG\Tager\Backend\Pages\Exceptions\InvalidUrlPathException;
use OZiTAG\Tager\Backend\Pages\Repositories\PagesRepository;

class CreatePageJob extends Job
{
    private $urlPath;

    private $parentId;

    private $title;

    public function __construct($title, $parentId, $urlPath)
    {
        $this->title = $title;
        $this->parentId = $parentId;
        $this->urlPath = $urlPath;
    }

    public function handle(PagesRepository $repository)
    {
        $parentPage = null;

        if ($this->parentId) {
            $parentPage = $repository->find($this->parentId);
            if (!$parentPage) {
                return null;
            }
        }

        $exists = $repository->findByUrlPath($this->urlPath);
        if ($exists) {
            throw new InvalidUrlPathException('The page with URL "' . $this->urlPath . " already exists");
        }

        $model = $repository->createModelInstance();
        $model->title = $this->title;
        $model->parent_id = $parentPage ? $parentPage->id : null;
        $model->url_path = $this->urlPath;
        $model->save();

        return $model;
    }
}
