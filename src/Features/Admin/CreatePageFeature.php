<?php

namespace OZiTAG\Tager\Backend\Pages\Features\Admin;

use OZiTAG\Tager\Backend\Core\Feature;
use OZiTAG\Tager\Backend\Core\SuccessResource;

class CreatePageFeature extends Feature
{
    public function handle()
    {
        return new SuccessResource();
    }
}
