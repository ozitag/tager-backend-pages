<?php

namespace OZiTAG\Tager\Backend\Pages\Features\Admin;

use Ozerich\FileStorage\Rules\FileRule;
use OZiTAG\Tager\Backend\Core\Feature;
use OZiTAG\Tager\Backend\Core\SuccessResource;
use OZiTAG\Tager\Backend\Pages\Exceptions\InvalidUrlPathException;
use OZiTAG\Tager\Backend\Pages\Jobs\CreatePageJob;
use OZiTAG\Tager\Backend\Pages\Jobs\GetPageUrlPathJob;
use OZiTAG\Tager\Backend\Pages\Jobs\SetPageMainParams;
use OZiTAG\Tager\Backend\Pages\Jobs\SetPageMainParamsJob;
use OZiTAG\Tager\Backend\Pages\Jobs\SetPageSeoParamsJob;
use OZiTAG\Tager\Backend\Pages\Jobs\SetPageTemplateJob;
use OZiTAG\Tager\Backend\Pages\Requests\CreatePageRequest;
use OZiTAG\Tager\Backend\Pages\Requests\PageRequest;
use OZiTAG\Tager\Backend\Pages\Resources\AdminPageFullResource;

class CreatePageFeature extends Feature
{
    public function handle(CreatePageRequest $request)
    {
        $urlPath = $request->urlPath ? $request->urlPath : $this->run(GetPageUrlPathJob::class, [
            'title' => $request->title,
            'parentId' => $request->parent
        ]);

        $page = $this->run(CreatePageJob::class, [
            'urlPath' => $urlPath,
            'parentId' => $request->parent,
            'title' => $request->title
        ]);

        if (!$page) {
            abort('400', 'Create page failed');
        }

        try {
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

        } catch (\Exception $exception) {
            $page->delete();
            throw $exception;
        }

        return new AdminPageFullResource($page);
    }
}
