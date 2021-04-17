<?php

namespace OZiTAG\Tager\Backend\Pages\Jobs;

use Ozerich\FileStorage\Repositories\FileRepository;
use Ozerich\FileStorage\Storage;
use OZiTAG\Tager\Backend\Core\Jobs\Job;
use OZiTAG\Tager\Backend\Pages\Models\TagerPage;
use OZiTAG\Tager\Backend\Pages\Utils\TagerPagesConfig;

class SetPageSeoParamsJob extends Job
{
    private TagerPage $model;

    private ?string $title;

    private ?string $description;

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
