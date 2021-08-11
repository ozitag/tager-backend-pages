<?php

namespace OZiTAG\Tager\Backend\Pages\OptionGenerators;

use OZiTAG\Tager\Backend\Fields\Contracts\ISelectOptionsGenerator;
use OZiTAG\Tager\Backend\Pages\Repositories\PagesRepository;

class PagesOptionGenerator implements ISelectOptionsGenerator
{
    protected PagesRepository $pagesRepository;

    protected ?string $template;

    public function __construct(?string $template = null, PagesRepository $pagesRepository)
    {
        $this->template = $template;

        $this->pagesRepository = $pagesRepository;
    }

    public function generate()
    {
        if ($this->template) {
            $pages = $this->pagesRepository->findByTemplate($this->template)->get();
        } else {
            $pages = $this->pagesRepository->all();
        }

        $result = [];
        foreach ($pages as $page) {
            $result[$page->id] = $page->title . ' (' . $page->getWebPageUrl() . ')';
        }

        return $result;
    }
}
