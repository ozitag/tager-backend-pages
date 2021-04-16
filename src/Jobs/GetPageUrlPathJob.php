<?php

namespace OZiTAG\Tager\Backend\Pages\Jobs;

use OZiTAG\Tager\Backend\Core\Jobs\Job;
use OZiTAG\Tager\Backend\Pages\Repositories\PagesRepository;
use OZiTAG\Tager\Backend\Utils\Helpers\Translit;

class GetPageUrlPathJob extends Job
{
    private $title;

    private $parentId;

    public function __construct($parentId, $title)
    {
        $this->title = $title;
        $this->parentId = $parentId;
    }

    public function handle(PagesRepository $pagesRepository)
    {
        $parent = $this->parentId ? $pagesRepository->find($this->parentId) : null;
        $basePath = $parent ? $parent->url_path : '';

        $baseAlias = Translit::translit($this->title);

        $ind = 0;
        while (true) {
            $alias = $ind === 0 ? $baseAlias : $baseAlias . '-' . $ind;
            $path = $basePath . '/' . $alias;

            $exists = $pagesRepository->findByUrlPath($path);
            if (!$exists) {
                return $path;
            }

            $ind = $ind + 1;
        }
    }
}
