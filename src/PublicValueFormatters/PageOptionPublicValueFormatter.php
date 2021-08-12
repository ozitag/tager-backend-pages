<?php

namespace OZiTAG\Tager\Backend\Pages\PublicValueFormatters;

use OZiTAG\Tager\Backend\Fields\Contracts\IPublicValueFormatter;
use OZiTAG\Tager\Backend\Pages\Models\TagerPage;
use OZiTAG\Tager\Backend\Pages\Repositories\PagesRepository;

class PageOptionPublicValueFormatter implements IPublicValueFormatter
{
    protected PagesRepository $pagesRepository;

    public function __construct(PagesRepository $pagesRepository)
    {
        $this->pagesRepository = $pagesRepository;
    }

    public function format($value)
    {
        /** @var TagerPage $page */
        $page = $this->pagesRepository->find($value);
        if (!$page) {
            return null;
        }

        return [
            'id' => $page->id,
            'url' => $page->getWebPageUrl(),
            'title' => $page->title,
            'excerpt' => $page->excerpt,
            'image' => $page->image ? $page->image->getFullJson() : ,
            'datetime' => $page->datetime,
        ];
    }
}
