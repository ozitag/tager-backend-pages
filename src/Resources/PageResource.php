<?php

namespace OZiTAG\Tager\Backend\Pages\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use OZiTAG\Tager\Backend\Pages\Models\TagerPage;

class PageResource extends JsonResource
{
    public function toArray($request)
    {
        /** @var TagerPage $model */
        $model = $this->resource;

        return [
            'id' => $model->id,
            'title' => $model->title,
            'path' => $model->url_path,
            'template' => $model->template,
        ];
    }
}
