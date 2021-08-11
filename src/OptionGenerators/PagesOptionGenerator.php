<?php

namespace OZiTAG\Tager\Backend\Pages\OptionGenerators;

use OZiTAG\Tager\Backend\Fields\Contracts\ISelectOptionsGenerator;
use OZiTAG\Tager\Backend\Pages\Models\TagerPage;
use OZiTAG\Tager\Backend\Pages\Repositories\PagesRepository;

class PagesOptionGenerator implements ISelectOptionsGenerator
{
    protected PagesRepository $pagesRepository;

    public function __construct(PagesRepository $pagesRepository)
    {
        $this->pagesRepository = $pagesRepository;
    }

    public function generate()
    {
        /** @var TagerPage[] $pages */
        $pages = $this->pagesRepository->all();

        $result = [];
        foreach ($pages as $page) {
            $result[$page->id] = $page->title . ' (' . $page->getWebPageUrl() . ')';
        }

        return $result;
    }
}
