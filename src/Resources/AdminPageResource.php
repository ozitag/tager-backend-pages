<?php

namespace OZiTAG\Tager\Backend\Pages\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AdminPageResource extends JsonResource
{
    public function toArray($request)
    {
        $level = 1;

        $parent = $this->parent;
        while ($parent) {
            $level++;
            $parent = $parent->parent;
        }

        return [
            'id' => $this->id,
            'level' => $level,
            'title' => $this->title,
            'image' => $this->image ? $this->image->getUrl() : null,
            'path' => $this->url_path
        ];
    }
}
