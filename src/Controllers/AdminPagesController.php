<?php

namespace OZiTAG\Tager\Backend\Pages\Controllers;

use OZiTAG\Tager\Backend\Core\Controllers\Controller;
use OZiTAG\Tager\Backend\Core\Repositories\EloquentRepository;
use OZiTAG\Tager\Backend\Crud\Controllers\AdminCrudController;
use OZiTAG\Tager\Backend\Pages\Jobs\CheckIfCanDeletePageJob;
use OZiTAG\Tager\Backend\Pages\Jobs\CreatePageJob;
use OZiTAG\Tager\Backend\Pages\Operations\CreatePageOperation;
use OZiTAG\Tager\Backend\Pages\Operations\UpdatePageOperation;
use OZiTAG\Tager\Backend\Pages\Repositories\PagesRepository;
use OZiTAG\Tager\Backend\Pages\Requests\CreatePageRequest;
use OZiTAG\Tager\Backend\Pages\Requests\UpdatePageRequest;

class AdminPagesController extends AdminCrudController
{
    public function __construct(PagesRepository $repository)
    {
        parent::__construct($repository);

        $this->setIndexAction(true);

        $this->setStoreAction(CreatePageRequest::class, CreatePageOperation::class);

        $this->setUpdateAction(UpdatePageRequest::class, UpdatePageOperation::class);

        $this->setDeleteAction(CheckIfCanDeletePageJob::class);

        $this->setResourceFields([
            'id',
            'depth',
            'title',
            'path' => 'url_path'
        ]);

        $this->setFullResourceFields([
            'id',
            'path' => 'url_path',
            'title',
            'template',
            'parent' => [
                'relation' => 'parent',
                'as' => [
                    'id', 'title'
                ]
            ],
            'image:file:json',
            'excerpt',
            'body',
            'pageTitle' => 'page_title',
            'pageDescription' => 'page_description',
            'openGraphTitle' => 'open_graph_title',
            'openGraphDescription' => 'open_graph_description',
            'openGraphImage:file:json',
            'templateValues' => 'templateValuesJson'
        ]);

        $this->setCacheNamespace('tager/pages');
    }
}