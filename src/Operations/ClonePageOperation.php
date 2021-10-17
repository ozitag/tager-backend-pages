<?php

namespace OZiTAG\Tager\Backend\Pages\Operations;

use OZiTAG\Tager\Backend\Core\Jobs\Operation;
use OZiTAG\Tager\Backend\Pages\Jobs\Clone\BasicClonePageJob;
use OZiTAG\Tager\Backend\Pages\Jobs\Clone\CloneTemplateFieldsJob;
use OZiTAG\Tager\Backend\Pages\Jobs\Clone\GetNewPageUrlJob;
use OZiTAG\Tager\Backend\Pages\Models\TagerPage;

class ClonePageOperation extends Operation
{
    protected TagerPage $model;

    public function __construct(TagerPage $model)
    {
        $this->model = $model;
    }

    public function handle()
    {
        $newUrl = $this->run(GetNewPageUrlJob::class, [
            'currentUrl' => $this->model->url_path
        ]);

        $newPage = $this->run(BasicClonePageJob::class, [
            'page' => $this->model,
            'urlPath' => $newUrl
        ]);

        $newPage = $this->run(CloneTemplateFieldsJob::class, [
            'oldPage' => $this->model,
            'newPage' => $newPage
        ]);

        return $newPage;
    }
}
