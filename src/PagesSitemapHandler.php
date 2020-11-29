<?php

namespace OZiTAG\Tager\Backend\Pages;

use OZiTAG\Tager\Backend\Pages\Repositories\PagesRepository;
use OZiTAG\Tager\Backend\Sitemap\Contracts\ISitemapHandler;
use OZiTAG\Tager\Backend\Sitemap\Structures\SitemapItem;

class PagesSitemapHandler implements ISitemapHandler
{
    /** @var PagesRepository */
    private $pagesRepository;

    public function __construct(PagesRepository $pagesRepository)
    {
        $this->pagesRepository = $pagesRepository;
    }

    public function handle()
    {
        $pages = $this->pagesRepository->all();

        $result = [];
        foreach ($pages as $page) {
            $result[] = new SitemapItem('/' . $page->url_path);
        }

        return $result;
    }
}
