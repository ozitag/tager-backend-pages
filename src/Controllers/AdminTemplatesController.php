<?php

namespace OZiTAG\Tager\Backend\Pages\Controllers;

use OZiTAG\Tager\Backend\Core\Controllers\Controller;
use OZiTAG\Tager\Backend\Crud\Controllers\CrudController;

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
