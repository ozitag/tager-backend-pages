<?php

namespace OZiTAG\Tager\Backend\Pages\Controllers;

use OZiTAG\Tager\Backend\Crud\Actions\DeleteAction;
use OZiTAG\Tager\Backend\Crud\Actions\IndexAction;
use OZiTAG\Tager\Backend\Crud\Actions\StoreOrUpdateAction;
use OZiTAG\Tager\Backend\Crud\Controllers\AdminCrudController;
use OZiTAG\Tager\Backend\Pages\Events\PageDeletedEvent;
use OZiTAG\Tager\Backend\Pages\Events\PageUpdatedEvent;
use OZiTAG\Tager\Backend\Pages\Jobs\CheckIfCanDeletePageJob;
use OZiTAG\Tager\Backend\Pages\Operations\CreatePageOperation;
use OZiTAG\Tager\Backend\Pages\Operations\UpdatePageOperation;
use OZiTAG\Tager\Backend\Pages\Repositories\PagesRepository;
use OZiTAG\Tager\Backend\Pages\Requests\CreatePageRequest;
use OZiTAG\Tager\Backend\Pages\Requests\UpdatePageRequest;

class AdminPagesController extends AdminCrudController
{
    public bool $hasCountAction = true;

    public bool $hasMoveAction = true;

    public function __construct(PagesRepository $repository)
    {
        parent::__construct($repository);

        $this->setIndexAction((new IndexAction())->enableTree());

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

        $fields = [
            'id',
            'depth',
            'title',
            'templateName',
            'path' => 'url_path',
            'parent' => [
                'relation' => 'parent',
                'as' => [
                    'id', 'title'
                ]
            ],
        ];

        $this->setResourceFields($fields);

        $this->setFullResourceFields(array_merge($fields, [
            'template',
            'image:file:model',
            'excerpt',
            'body',
            'pageTitle' => 'page_title',
            'pageDescription' => 'page_description',
            'pageKeywords' => 'page_keywords',
            'openGraphImage:file:model',
            'templateValues' => 'templateValuesJson'
        ]));

        $this->setCacheNamespace('tager/pages');
    }
}
