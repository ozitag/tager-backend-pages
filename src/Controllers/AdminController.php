<?php

namespace OZiTAG\Tager\Backend\Pages\Controllers;

use OZiTAG\Tager\Backend\Core\Controllers\Controller;
use OZiTAG\Tager\Backend\Pages\Features\Admin\TemplatesFeature;
use OZiTAG\Tager\Backend\Pages\Features\Admin\ViewTemplateFeature;
use OZiTAG\Tager\Backend\Pages\Features\Admin\CreatePageFeature;
use OZiTAG\Tager\Backend\Pages\Features\Admin\DeletePageFeature;
use OZiTAG\Tager\Backend\Pages\Features\Admin\ListPagesFeature;
use OZiTAG\Tager\Backend\Pages\Features\Admin\UpdatePageFeature;
use OZiTAG\Tager\Backend\Pages\Features\Admin\ViewPageFeature;

class AdminController extends Controller
{
    public function templates()
    {
        return $this->serve(TemplatesFeature::class);
    }

    public function viewTemplate($alias)
    {
        return $this->serve(ViewTemplateFeature::class, [
            'alias' => $alias
        ]);
    }

    public function index()
    {
        return $this->serve(ListPagesFeature::class);
    }

    public function create()
    {
        return $this->serve(CreatePageFeature::class);
    }

    public function view($id)
    {
        return $this->serve(ViewPageFeature::class, [
            'id' => $id
        ]);
    }

    public function update($id)
    {
        return $this->serve(UpdatePageFeature::class, [
            'id' => $id
        ]);
    }

    public function delete($id)
    {
        return $this->serve(DeletePageFeature::class, [
            'id' => $id
        ]);
    }
}
