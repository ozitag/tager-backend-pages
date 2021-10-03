<?php

namespace OZiTAG\Tager\Backend\Pages\Features\Admin;

use Illuminate\Http\Resources\Json\JsonResource;
use OZiTAG\Tager\Backend\Core\Features\Feature;
use OZiTAG\Tager\Backend\Core\Resources\FailureResource;
use OZiTAG\Tager\Backend\HttpCache\HttpCache;
use OZiTAG\Tager\Backend\Pages\Models\TagerPage;
use OZiTAG\Tager\Backend\Pages\Operations\ClonePageOperation;
use OZiTAG\Tager\Backend\Pages\Repositories\PagesRepository;
use OZiTAG\Tager\Backend\Pages\Resources\AdminPageFullResource;

class ClonePageFeature extends Feature
{
    protected int $id;

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public function handle(PagesRepository $pagesRepository)
    {
        /** @var TagerPage $page */
        $page = $pagesRepository->find($this->id);
        if (!$page) {
            abort('404', __('tager-pages::errors.page_not_found'));
        }

        $newPage = $this->run(ClonePageOperation::class, [
            'page' => $page
        ]);

        if (!$newPage) {
            return new FailureResource('Error clone page');
        }
        
        return new AdminPageFullResource($newPage);
    }
}
