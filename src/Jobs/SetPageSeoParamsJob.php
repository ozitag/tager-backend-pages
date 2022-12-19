<?php

namespace OZiTAG\Tager\Backend\Pages\Jobs;

use Ozerich\FileStorage\Repositories\FileRepository;
use Ozerich\FileStorage\Storage;
use OZiTAG\Tager\Backend\Core\Jobs\Job;
use OZiTAG\Tager\Backend\Pages\Models\TagerPage;
use OZiTAG\Tager\Backend\Pages\Utils\TagerPagesConfig;

class SetPageSeoParamsJob extends Job
{

    public function __construct(protected TagerPage $model,
                                protected ?string   $title = null,
                                protected ?string   $description = null,
                                protected ?string   $keywords = null,
                                protected           $openGraphImageId = null,
                                protected           $hiddenFromSeoIndexation = false,
    )
    {

    }

    public function handle(FileRepository $fileRepository, Storage $fileStorage)
    {
        $this->model->page_title = $this->title;
        $this->model->page_description = $this->description;
        $this->model->page_keywords = $this->keywords;
        $this->model->hidden_from_seo_indexation = $this->hiddenFromSeoIndexation;

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
