<?php

namespace OZiTAG\Tager\Backend\Pages\Features\Admin;

use OZiTAG\Tager\Backend\Core\Feature;
use OZiTAG\Tager\Backend\Core\SuccessResource;
use OZiTAG\Tager\Backend\Pages\Requests\PageRequest;

class CreatePageFeature extends Feature
{
    public function handle(PageRequest $request)
    {
        return new SuccessResource();
    }
}
