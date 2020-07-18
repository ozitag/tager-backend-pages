<?php

namespace OZiTAG\Tager\Backend\Pages\Features\Admin;

use OZiTAG\Tager\Backend\Core\Feature;
use OZiTAG\Tager\Backend\Core\SuccessResource;
use OZiTAG\Tager\Backend\Pages\Jobs\GetPageByIdJob;
use OZiTAG\Tager\Backend\Pages\Requests\PageRequest;

class UpdatePageFeature extends Feature
{
    private $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function handle(PageRequest $pageRequest)
    {
        $model = $this->run(GetPageByIdJob::class, ['id' => $this->id]);

        return new SuccessResource();
    }
}
