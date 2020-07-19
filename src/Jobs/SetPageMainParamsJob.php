<?php

namespace OZiTAG\Tager\Backend\Pages\Jobs;

use Illuminate\Queue\Jobs\Job;
use Ozerich\FileStorage\Repositories\FileRepository;
use Ozerich\FileStorage\Storage;
use OZiTAG\Tager\Backend\Pages\Exceptions\InvalidUrlPathException;
use OZiTAG\Tager\Backend\Pages\Models\TagerPage;
use OZiTAG\Tager\Backend\Pages\Repositories\PagesRepository;
use OZiTAG\Tager\Backend\Pages\TagerPagesConfig;

class SetPageMainParamsJob
{
    /** @var TagerPage */
    private $model;

    private $excerpt;

    private $body;

    private $imageId;

    public function __construct(TagerPage $model, $excerpt, $body, $imageId)
    {
        $this->model = $model;
        $this->excerpt = $excerpt;
        $this->body = $body;
        $this->imageId = $imageId;
    }

    public function handle(FileRepository $fileRepository, Storage $fileStorage)
    {
        $this->model->excerpt = $this->excerpt;
        $this->model->body = $this->body;

        if ($this->imageId) {
            $image = $fileRepository->find($this->imageId);
            if ($image) {
                $scenario = TagerPagesConfig::getPageImageScenario();
                if ($scenario) {
                    $fileStorage->setFileScenario($this->imageId, $scenario);
                }
                $this->model->image_id = $this->imageId;
            }
        }

        $this->model->save();

        return $this->model;
    }
}
