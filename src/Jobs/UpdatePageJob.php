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

    private $datetime;

    private $status;

    public function __construct(TagerPage $model, $title, $parentId, $urlPath, $datetime, $status)
    {
        $this->model = $model;
        $this->title = $title;
        $this->parentId = $parentId;
        $this->urlPath = $urlPath;
        $this->datetime = $datetime;
        $this->status = $status;
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
        $this->model->status = $this->status;
        $this->model->parent_id = $parentPage ? $parentPage->id : null;
        $this->model->url_path = $this->urlPath;
        $this->model->datetime = $this->datetime;
        $this->model->save();

        return $this->model;
    }
}
