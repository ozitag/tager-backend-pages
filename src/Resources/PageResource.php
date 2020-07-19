<?php

namespace OZiTAG\Tager\Backend\Pages\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PageResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'path' => $this->url_path,
            'template' => $this->template,
        ];
    }
}
