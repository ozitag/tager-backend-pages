<?php

namespace OZiTAG\Tager\Backend\Pages\Controllers;

use OZiTAG\Tager\Backend\Core\Controllers\Controller;
use OZiTAG\Tager\Backend\Core\Repositories\EloquentRepository;
use OZiTAG\Tager\Backend\Crud\Controllers\AdminCrudController;
use OZiTAG\Tager\Backend\Pages\Features\Admin\ModuleInfoFeature;
use OZiTAG\Tager\Backend\Pages\Jobs\CheckIfCanDeletePageJob;
use OZiTAG\Tager\Backend\Pages\Jobs\CreatePageJob;
use OZiTAG\Tager\Backend\Pages\Operations\CreatePageOperation;
use OZiTAG\Tager\Backend\Pages\Operations\UpdatePageOperation;
use OZiTAG\Tager\Backend\Pages\Repositories\PagesRepository;
use OZiTAG\Tager\Backend\Pages\Requests\CreatePageRequest;
use OZiTAG\Tager\Backend\Pages\Requests\UpdatePageRequest;

class AdminController extends Controller
{
    public function moduleInfo()
    {
        return $this->serve(ModuleInfoFeature::class);
    }
}
