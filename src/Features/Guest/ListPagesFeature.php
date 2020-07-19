<?php

namespace OZiTAG\Tager\Backend\Pages\Features\Guest;

use OZiTAG\Tager\Backend\Core\Feature;
use OZiTAG\Tager\Backend\Pages\Repositories\PagesRepository;
use OZiTAG\Tager\Backend\Pages\Resources\PageResource;

class ListPagesFeature extends Feature
{
    public function handle(PagesRepository $pagesRepository)
    {
        return PageResource::collection($pagesRepository->all());
    }
}
