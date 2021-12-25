<?php

namespace OZiTAG\Tager\Backend\Pages\Operations;

use Ozerich\FileStorage\Storage;
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
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class UpdatePageOperation extends Operation
{
    private TagerPage $model;

    private UpdatePageRequest $request;

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
            'status' => $request->status,
            'urlPath' => $request->getPath(),
            'parentId' => $request->parent,
            'title' => $request->title,
            'datetime' => $request->datetime,
        ]);

        if (!$page) {
            throw new BadRequestHttpException(__('tager-pages::errors.create_page_failed'));
        }

        $page = $this->run(SetPageMainParamsJob::class, [
            'model' => $page,
            'excerpt' => $request->excerpt,
            'body' => $request->body,
            'imageId' => Storage::fromUUIDtoId($request->image)
        ]);

        $page = $this->run(SetPageSeoParamsJob::class, [
            'model' => $page,
            'title' => $request->pageTitle,
            'description' => $request->pageDescription,
            'keywords' => $request->pageKeywords,
            'openGraphImageId' => Storage::fromUUIDtoId($request->openGraphImage)
        ]);

        $page = $this->run(SetPageTemplateJob::class, [
            'model' => $page,
            'template' => $request->template,
            'fields' => $request->templateFields
        ]);

        return $page;
    }
}
