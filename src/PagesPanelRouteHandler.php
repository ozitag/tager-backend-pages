<?php

namespace OZiTAG\Tager\Backend\Pages;

use OZiTAG\Tager\Backend\Pages\Repositories\PagesRepository;
use OZiTAG\Tager\Backend\Panel\Contracts\IRouteHandler;
use OZiTAG\Tager\Backend\Panel\Structures\TagerRouteHandlerResult;

class PagesPanelRouteHandler implements IRouteHandler
{
    protected PagesRepository $pagesRepository;

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

        $result->setModel($model->getPanelType(), $model->getPanelTitle());
        $result->addAction(__('tager-pages::panel.edit'), '/pages/' . $model->id);

        return $result;
    }
}
