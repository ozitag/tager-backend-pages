<?php

namespace OZiTAG\Tager\Backend\Pages\Controllers;

use OZiTAG\Tager\Backend\Core\Controller;
use OZiTAG\Tager\Backend\Pages\Features\Admin\CreatePageFeature;
use OZiTAG\Tager\Backend\Pages\Features\Admin\DeletePageFeature;
use OZiTAG\Tager\Backend\Pages\Features\Admin\ListPagesFeature;
use OZiTAG\Tager\Backend\Pages\Features\Admin\UpdatePageFeature;
use OZiTAG\Tager\Backend\Pages\Features\Admin\ViewPageFeature;

class AdminController extends Controller
{
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
