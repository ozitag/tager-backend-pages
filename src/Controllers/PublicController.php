<?php

namespace OZiTAG\Tager\Backend\Pages\Controllers;

use Illuminate\Http\Request;
use OZiTAG\Tager\Backend\Core\Controllers\Controller;
use OZiTAG\Tager\Backend\Core\Repositories\EloquentRepository;
use OZiTAG\Tager\Backend\Crud\Actions\IndexAction;
use OZiTAG\Tager\Backend\Crud\Controllers\PublicCrudController;
use OZiTAG\Tager\Backend\Pages\Features\Guest\ListPagesFeature;
use OZiTAG\Tager\Backend\Pages\Features\Guest\ViewByIdPageFeature;
use OZiTAG\Tager\Backend\Pages\Features\Guest\ViewByPathPageFeature;
use OZiTAG\Tager\Backend\Pages\Jobs\GetPublishedPostsBuilderJob;
use OZiTAG\Tager\Backend\Pages\Repositories\PagesRepository;
use OZiTAG\Tager\Backend\Pages\Resources\PageFullResource;
use OZiTAG\Tager\Backend\Pages\Resources\PageResource;

class PublicController extends PublicCrudController
{
    public function __construct(PagesRepository $repository)
    {
        parent::__construct($repository);

        $this->setIndexAction(
            (new IndexAction(GetPublishedPostsBuilderJob::class))->disablePagination()
        );

        $this->setResourceClasses(PageResource::class, PageFullResource::class);
    }

    public function viewByPath(Request $request)
    {
        $path = $request->get('path');

        return $this->serve(ViewByPathPageFeature::class, [
            'path' => $path
        ]);
    }
}
