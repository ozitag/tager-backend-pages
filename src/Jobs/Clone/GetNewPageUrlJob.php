<?php

namespace OZiTAG\Tager\Backend\Pages\Jobs\Clone;

use OZiTAG\Tager\Backend\Core\Jobs\Job;
use OZiTAG\Tager\Backend\Pages\Repositories\PagesRepository;

class GetNewPageUrlJob extends Job
{
    protected string $currentUrl;

    public function __construct(string $currentUrl)
    {
        $this->currentUrl = $currentUrl;
    }

    public function handle(PagesRepository $pagesRepository)
    {
        $ind = 0;
        while (true) {
            $url = $this->currentUrl . '-copy';
            if ($ind > 0) {
                $url .= '-' . $ind;
            }

            if ($pagesRepository->findByUrlPath($url) === null) {
                return $url;
            }

            $ind = $ind + 1;
        }
    }
}
