<?php

namespace OZiTAG\Tager\Backend\Pages\Controllers;

use OZiTAG\Tager\Backend\Crud\Actions\CloneAction;
use OZiTAG\Tager\Backend\Crud\Actions\CountAction;
use OZiTAG\Tager\Backend\Crud\Actions\DeleteAction;
use OZiTAG\Tager\Backend\Crud\Actions\IndexAction;
use OZiTAG\Tager\Backend\Crud\Actions\StoreOrUpdateAction;
use OZiTAG\Tager\Backend\Crud\Controllers\AdminCrudController;
use OZiTAG\Tager\Backend\Pages\Events\PageDeletedEvent;
use OZiTAG\Tager\Backend\Pages\Events\PageUpdatedEvent;
use OZiTAG\Tager\Backend\Pages\Jobs\CheckIfCanDeletePageJob;
use OZiTAG\Tager\Backend\Pages\Jobs\GetCountActionBuilderJob;
use OZiTAG\Tager\Backend\Pages\Operations\ClonePageOperation;
use OZiTAG\Tager\Backend\Pages\Operations\CreatePageOperation;
use OZiTAG\Tager\Backend\Pages\Operations\UpdatePageOperation;
use OZiTAG\Tager\Backend\Pages\Repositories\PagesRepository;
use OZiTAG\Tager\Backend\Pages\Requests\CreatePageRequest;
use OZiTAG\Tager\Backend\Pages\Requests\UpdatePageRequest;
use OZiTAG\Tager\Backend\Pages\Resources\AdminPageFullResource;
use OZiTAG\Tager\Backend\Pages\Resources\AdminPageResource;

class AdminPagesController extends AdminCrudController
{
    public bool $hasCountAction = true;

    public bool $hasMoveAction = true;

    public function __construct(PagesRepository $repository)
    {
        parent::__construct($repository);

        $this->setIndexAction((new IndexAction())->enableTree());

        $this->setCountAction((new CountAction(GetCountActionBuilderJob::class)));

        $this->setStoreAction(new StoreOrUpdateAction(
            CreatePageRequest::class,
            CreatePageOperation::class,
            null,
            PageUpdatedEvent::class
        ));

        $this->setUpdateAction(new StoreOrUpdateAction(
            UpdatePageRequest::class,
            UpdatePageOperation::class,
            null,
            PageUpdatedEvent::class
        ));

        $this->setDeleteAction(new DeleteAction(CheckIfCanDeletePageJob::class, PageDeletedEvent::class));

        $this->setCloneAction(new CloneAction(ClonePageOperation::class));

        $this->setResourceClasses(AdminPageResource::class, AdminPageFullResource::class);

        $this->setCacheNamespace('tager/pages');
    }
}
