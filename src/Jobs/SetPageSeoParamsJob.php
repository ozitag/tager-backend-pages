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

    private ?string $keywords;

    private ?int $openGraphImageId;

    public function __construct(TagerPage $model, ?string $title = null, ?string $description = null, ?string $keywords = null, $openGraphImageId = null)
    {
        $this->model = $model;
        $this->title = $title;
        $this->description = $description;
        $this->keywords = $keywords;
        $this->openGraphImageId = $openGraphImageId;
    }

    public function handle(FileRepository $fileRepository, Storage $fileStorage)
    {
        $this->model->page_title = $this->title;
        $this->model->page_description = $this->description;
        $this->model->page_keywords = $this->keywords;

        if ($this->openGraphImageId) {
            $image = $fileRepository->find($this->openGraphImageId);
            if ($image) {
                $scenario = TagerPagesConfig::getOpenGraphScenario();
                if ($scenario) {
                    if ($scenario instanceof \BackedEnum) {
                        $scenario = $scenario->value;
                    }
                    $fileStorage->setFileScenario($this->openGraphImageId, $scenario);
                }
                $this->model->open_graph_image_id = $this->openGraphImageId;
            }
        }

        $this->model->save();

        return $this->model;
    }
}
