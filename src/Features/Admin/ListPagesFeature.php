<?php

namespace OZiTAG\Tager\Backend\Pages\Features\Admin;

use OZiTAG\Tager\Backend\Core\Feature;
use OZiTAG\Tager\Backend\Core\SuccessResource;
use OZiTAG\Tager\Backend\Pages\Models\TagerPage;
use OZiTAG\Tager\Backend\Pages\Repositories\PagesRepository;
use OZiTAG\Tager\Backend\Pages\Resources\AdminPageResource;

class ListPagesFeature extends Feature
{
    public function handle(PagesRepository $repository)
    {
        $nodes = TagerPage::get()->toFlatTree();

        return AdminPageResource::collection($nodes);
    }
}
