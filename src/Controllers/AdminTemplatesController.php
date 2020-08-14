<?php

namespace OZiTAG\Tager\Backend\Pages\Controllers;

use OZiTAG\Tager\Backend\Core\Controllers\Controller;
use OZiTAG\Tager\Backend\Crud\Controllers\CrudController;
use OZiTAG\Tager\Backend\Pages\Features\Admin\TemplatesFeature;
use OZiTAG\Tager\Backend\Pages\Features\Admin\ViewTemplateFeature;

class AdminTemplatesController extends Controller
{
    public function index()
    {
        return $this->serve(TemplatesFeature::class);
    }

    public function view($alias)
    {
        return $this->serve(ViewTemplateFeature::class, [
            'alias' => $alias
        ]);
    }
}
