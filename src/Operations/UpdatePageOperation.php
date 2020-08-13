<?php

namespace OZiTAG\Tager\Backend\Pages\Operations;

use OZiTAG\Tager\Backend\Core\Features\Feature;
use OZiTAG\Tager\Backend\Core\Jobs\Operation;
use OZiTAG\Tager\Backend\Pages\Jobs\GetPageByIdJob;
use OZiTAG\Tager\Backend\Pages\Jobs\SetPageMainParamsJob;
use OZiTAG\Tager\Backend\Pages\Jobs\SetPageSeoParamsJob;
use OZiTAG\Tager\Backend\Pages\Jobs\SetPageTemplateJob;
use OZiTAG\Tager\Backend\Pages\Jobs\UpdatePageJob;
use OZiTAG\Tager\Backend\Pages\Models\TagerPage;
use OZiTAG\Tager\Backend\Pages\Requests\UpdatePageRequest;
use OZiTAG\Tager\Backend\Pages\Resources\AdminPageFullResource;

class UpdatePageOperation extends Operation
{
    /** @var TagerPage */
    private $model;

    /** @var UpdatePageRequest */
    private $request;

    public function __construct(TagerPage $model, UpdatePageRequest $request)
    {
        $this->model = $model;

        $this->request = $request;
    }

    public function handle()
    {
        $request = $this->request;

        $page = $this->run(UpdatePageJob::class, [
            'model' => $this->model,
            'urlPath' => $request->path,
            'parentId' => $request->parent,
            'title' => $request->title
        ]);

        if (!$page) {
            abort('400', 'Create page failed');
        }

        $page = $this->run(SetPageMainParamsJob::class, [
            'model' => $page,
            'excerpt' => $request->excerpt,
            'body' => $request->body,
            'imageId' => $request->image
        ]);

        $page = $this->run(SetPageSeoParamsJob::class, [
            'model' => $page,
            'title' => $request->pageTitle,
            'description' => $request->pageDescription,
            'openGraphTitle' => $request->openGraphTitle,
            'openGraphDescription' => $request->openGraphDescription,
            'openGraphImageId' => $request->openGraphImage
        ]);

        $page = $this->run(SetPageTemplateJob::class, [
            'model' => $page,
            'template' => $request->template,
            'fields' => $request->templateFields
        ]);

        return $page;
    }
}