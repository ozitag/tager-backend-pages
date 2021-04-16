<?php

namespace OZiTAG\Tager\Backend\Pages\Jobs;

use OZiTAG\Tager\Backend\Core\Jobs\Job;
use OZiTAG\Tager\Backend\Pages\Exceptions\InvalidUrlPathException;
use OZiTAG\Tager\Backend\Pages\Models\TagerPage;
use OZiTAG\Tager\Backend\Pages\Repositories\PagesRepository;

class UpdatePageJob extends Job
{
    protected TagerPage $model;

    private $urlPath;

    private $parentId;

    private $title;

    public function __construct(TagerPage $model, $title, $parentId, $urlPath)
    {
        $this->model = $model;
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
        if ($exists && $exists->id != $this->model->id) {
            throw new InvalidUrlPathException(
                __('tager-pages::errors.url_busy', ['url_path' => $this->urlPath])
            );
        }

        $this->model->title = $this->title;
        $this->model->parent_id = $parentPage ? $parentPage->id : null;
        $this->model->url_path = $this->urlPath;
        $this->model->save();

        return $this->model;
    }
}
