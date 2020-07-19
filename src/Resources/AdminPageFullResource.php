<?php

namespace OZiTAG\Tager\Backend\Pages\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Ozerich\FileStorage\Models\File;
use OZiTAG\Tager\Backend\Mail\Models\TagerMailTemplate;
use OZiTAG\Tager\Backend\Mail\Utils\TagerMailConfig;
use OZiTAG\Tager\Backend\Seo\Models\SeoPage;

class AdminPageFullResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'template' => $this->template,
            'title' => $this->title,
            'urlAlias' => $this->url_alias,
            'urlPath' => $this->url_path,
            'parent' => $this->parent_id,
            'image' => $this->image ? $this->image->getShortJson() : null,
            'excerpt' => $this->excerpt,
            'body' => $this->body,
            'pageTitle' => $this->page_title,
            'pageDescription' => $this->page_description,
            'openGraphTitle' => $this->open_graph_title,
            'openGraphDescription' => $this->open_graph_description,
            'openGraphImage' => $this->openGraphImage ? $this->openGraphImage->getShortJson() : null,
            'templateValues' => []
        ];
    }
}
