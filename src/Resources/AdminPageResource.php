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
            'datetime' => 'datetime:date',
            'parent' => [
                'relation' => 'parent',
                'as' => [
                    'id', 'title'
                ]
            ],
            'sitemapPriority' => 'sitemap_priority',
            'sitemapFrequency' => 'sitemap_frequency',
            'hiddenFromSeoIndexation' => 'hidden_from_seo_indexation:boolean',
        ];
    }
}
