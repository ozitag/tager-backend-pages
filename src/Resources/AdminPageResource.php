<?php

namespace OZiTAG\Tager\Backend\Pages\Resources;

use OZiTAG\Tager\Backend\Core\Resources\ModelResource;

class AdminPageResource extends ModelResource
{
    public function fields()
    {
        return [
            'id',
            'status',
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
    }
}
