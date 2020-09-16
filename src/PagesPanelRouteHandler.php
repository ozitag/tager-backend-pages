<?php

namespace OZiTAG\Tager\Backend\Pages;

use OZiTAG\Tager\Backend\Pages\Repositories\PagesRepository;
use OZiTAG\Tager\Backend\Panel\Contracts\IRouteHandler;
use OZiTAG\Tager\Backend\Panel\Structures\TagerRouteHandlerResult;

class PagesPanelRouteHandler implements IRouteHandler
{
    /** @var PagesRepository */
    private $pagesRepository;

    public function __construct(PagesRepository $pagesRepository)
    {
        $this->pagesRepository = $pagesRepository;
    }

    public function handle($route, $matches)
    {
        $model = $this->pagesRepository->findByUrlPath($route);

        if (!$model) {
            return null;
        }

        $result = new TagerRouteHandlerResult();

        $result->setModel('Page', $model->title);
        $result->addAction('Edit Page', '/pages/' . $model->id);

        return $result;
    }
}
