<?php

namespace OZiTAG\Tager\Backend\Pages\Jobs;

use Illuminate\Queue\Jobs\Job;
use Ozerich\FileStorage\Repositories\FileRepository;
use Ozerich\FileStorage\Storage;
use OZiTAG\Tager\Backend\Pages\Exceptions\InvalidUrlPathException;
use OZiTAG\Tager\Backend\Pages\Models\TagerPage;
use OZiTAG\Tager\Backend\Pages\Repositories\PagesRepository;
use OZiTAG\Tager\Backend\Pages\TagerPagesConfig;

class SetPageSeoParamsJob
{
    /** @var TagerPage */
    private $model;

    private $title;

    private $description;

    private $openGraphTitle;

    private $openGraphDescription;

    private $openGraphImageId;

    public function __construct(TagerPage $model, $title, $description, $openGraphTitle, $openGraphDescription, $openGraphImageId)
    {
        $this->model = $model;
        $this->title = $title;
        $this->description = $description;
        $this->openGraphTitle = $openGraphTitle;
        $this->openGraphDescription = $openGraphDescription;
        $this->openGraphImageId = $openGraphImageId;
    }

    public function handle(FileRepository $fileRepository, Storage $fileStorage)
    {
        $this->model->page_title = $this->title;
        $this->model->page_description = $this->description;
        $this->model->open_graph_title = $this->openGraphTitle;
        $this->model->open_graph_description = $this->openGraphDescription;

        if ($this->openGraphImageId) {
            $image = $fileRepository->find($this->openGraphImageId);
            if ($image) {
                $scenario = TagerPagesConfig::getOpenGraphScenario();
                if ($scenario) {
                    $fileStorage->setFileScenario($this->openGraphImageId, $scenario);
                }
                $this->model->open_graph_image_id = $this->openGraphImageId;
            }
        }

        $this->model->save();

        return $this->model;
    }
}
