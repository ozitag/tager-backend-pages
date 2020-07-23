<?php

namespace OZiTAG\Tager\Backend\Pages\Features\Admin;

use OZiTAG\Tager\Backend\Core\Features\Feature;
use OZiTAG\Tager\Backend\Pages\Models\TagerPage;
use OZiTAG\Tager\Backend\Pages\Resources\AdminPageResource;

class ListPagesFeature extends Feature
{
    public function handle()
    {
        $nodes = TagerPage::get()->toFlatTree();

        return AdminPageResource::collection($nodes);
    }
}
