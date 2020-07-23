<?php

namespace OZiTAG\Tager\Backend\Pages\Features\Admin;

use OZiTAG\Tager\Backend\Core\Features\Feature;
use OZiTAG\Tager\Backend\Pages\Jobs\GetPageByIdJob;
use OZiTAG\Tager\Backend\Pages\Jobs\SetPageMainParamsJob;
use OZiTAG\Tager\Backend\Pages\Jobs\SetPageSeoParamsJob;
use OZiTAG\Tager\Backend\Pages\Jobs\SetPageTemplateJob;
use OZiTAG\Tager\Backend\Pages\Jobs\UpdatePageJob;
use OZiTAG\Tager\Backend\Pages\Requests\UpdatePageRequest;
use OZiTAG\Tager\Backend\Pages\Resources\AdminPageFullResource;

class UpdatePageFeature extends Feature
{
    private $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function handle(UpdatePageRequest $request)
    {
        $model = $this->run(GetPageByIdJob::class, ['id' => $this->id]);

        $page = $this->run(UpdatePageJob::class, [
            'model' => $model,
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

        return new AdminPageFullResource($page);
    }
}
