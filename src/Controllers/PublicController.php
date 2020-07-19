<?php

namespace OZiTAG\Tager\Backend\Pages\Controllers;

use Illuminate\Http\Request;
use OZiTAG\Tager\Backend\Core\Controller;
use OZiTAG\Tager\Backend\Pages\Features\Guest\ListPagesFeature;
use OZiTAG\Tager\Backend\Pages\Features\Guest\ViewByIdPageFeature;
use OZiTAG\Tager\Backend\Pages\Features\Guest\ViewByPathPageFeature;

class PublicController extends Controller
{
    public function index()
    {
        return $this->serve(ListPagesFeature::class);
    }

    public function viewById($id)
    {
        return $this->serve(ViewByIdPageFeature::class, [
            'id' => $id
        ]);
    }

    public function viewByPath(Request $request)
    {
        $path = $request->get('path');

        return $this->serve(ViewByPathPageFeature::class, [
            'path' => $path
        ]);
    }
}
