<?php

namespace OZiTAG\Tager\Backend\Pages\Operations;

use Carbon\Carbon;
use OZiTAG\Tager\Backend\Core\Features\Feature;
use OZiTAG\Tager\Backend\Core\Jobs\Operation;
use OZiTAG\Tager\Backend\Pages\Jobs\CreatePageJob;
use OZiTAG\Tager\Backend\Pages\Jobs\GetPageUrlPathJob;
use OZiTAG\Tager\Backend\Pages\Jobs\SetPageMainParamsJob;
use OZiTAG\Tager\Backend\Pages\Jobs\SetPageSeoParamsJob;
use OZiTAG\Tager\Backend\Pages\Jobs\SetPageTemplateJob;
use OZiTAG\Tager\Backend\Pages\Models\TagerPage;
use OZiTAG\Tager\Backend\Pages\Repositories\PagesRepository;
use OZiTAG\Tager\Backend\Pages\Requests\CreatePageRequest;
use OZiTAG\Tager\Backend\Pages\Resources\AdminPageFullResource;

class CreatePageOperation extends Operation
{
    /** @var CreatePageRequest */
    private $request;

    public function __construct(CreatePageRequest $request)
    {
        $this->request = $request;
    }

    public function handle(PagesRepository $repository)
    {
        /** @var TagerPage $model */
        $model = $repository->createModelInstance();
        $model->title = $this->request->title;
        $model->url_path = $this->request->getPath();
        $model->datetime = Carbon::now();
        $model->save();

        if ($this->request->parent) {
            $parent = $repository->find($this->request->parent);
            if ($parent) {
                $parent->prependNode($model);
            }
        }

        return $this->run(SetPageTemplateJob::class, [
            'model' => $model,
            'template' => $this->request->template
        ]);
    }
}
