<?php

namespace OZiTAG\Tager\Backend\Pages;

use Carbon\Carbon;
use OZiTAG\Tager\Backend\Pages\Models\TagerPage;
use OZiTAG\Tager\Backend\Pages\Repositories\PagesRepository;
use OZiTAG\Tager\Backend\Seo\Contracts\ISitemapHandler;
use OZiTAG\Tager\Backend\Seo\Structures\SitemapItem;

class PagesSitemapHandler implements ISitemapHandler
{
    private PagesRepository $pagesRepository;

    public function __construct(PagesRepository $pagesRepository)
    {
        $this->pagesRepository = $pagesRepository;
    }

    public function handle()
    {
        /** @var TagerPage[] $pages */
        $pages = $this->pagesRepository->findPublished()->get();

        $result = [];
        foreach ($pages as $page) {
            $result[] = new SitemapItem($page->getWebPageUrl(), new Carbon($page->updated_at));
        }

        return $result;
    }
}
