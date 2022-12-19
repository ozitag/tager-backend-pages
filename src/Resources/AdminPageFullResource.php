<?php

namespace OZiTAG\Tager\Backend\Pages\Resources;

class AdminPageFullResource extends AdminPageResource
{
    public function fields()
    {
        return array_merge(parent::fields(), [
            'template',
            'datetime:datetime',
            'image:file:model',
            'excerpt',
            'body',
            'pageTitle' => 'page_title',
            'pageDescription' => 'page_description',
            'pageKeywords' => 'page_keywords',
            'openGraphImage:file:model',
            'hiddenFromSeoIndexation' => 'hidden_from_seo_indexation:boolean',
            'templateValues' => 'templateValuesJson'
        ]);
    }
}
