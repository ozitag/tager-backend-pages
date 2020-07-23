<?php

namespace OZiTAG\Tager\Backend\Pages\Features\Guest;

use OZiTAG\Tager\Backend\Core\Features\Feature;
use OZiTAG\Tager\Backend\Pages\Jobs\GetPageByIdJob;
use OZiTAG\Tager\Backend\Pages\Resources\PageFullResource;

class ViewByIdPageFeature extends Feature
{
    private $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function handle()
    {
        $model = $this->run(GetPageByIdJob::class, ['id' => $this->id]);

        return new PageFullResource($model);
    }
}
