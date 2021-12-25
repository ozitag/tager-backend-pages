<?php

namespace OZiTAG\Tager\Backend\Pages\Jobs;

use OZiTAG\Tager\Backend\Core\Jobs\Job;
use OZiTAG\Tager\Backend\Pages\Repositories\PagesRepository;

class GetPublishedPostsBuilderJob extends Job
{
    public function handle(PagesRepository $pagesRepository)
    {
        return $pagesRepository->findPublished();
    }
}