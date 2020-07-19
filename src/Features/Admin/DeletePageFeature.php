<?php

namespace OZiTAG\Tager\Backend\Pages\Features\Admin;

use OZiTAG\Tager\Backend\Core\Feature;
use OZiTAG\Tager\Backend\Core\SuccessResource;
use OZiTAG\Tager\Backend\Pages\Jobs\DeletePageJob;
use OZiTAG\Tager\Backend\Pages\Jobs\GetPageByIdJob;

class DeletePageFeature extends Feature
{
    private $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function handle()
    {
        $model = $this->run(GetPageByIdJob::class, ['id' => $this->id]);

        $this->run(DeletePageJob::class, ['model' => $model]);

        return new SuccessResource();
    }
}
