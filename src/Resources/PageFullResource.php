<?php

namespace OZiTAG\Tager\Backend\Pages\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PageFullResource extends JsonResource
{
    private function getTemplateValuesJson()
    {
        if (!$this->template) {
            return null;
        }

        $result = [];

        foreach ($this->templateFields as $templateField) {
            $result[] = [
                'field' => $templateField->field,
                'valuePlain' => $templateField->value,
                'valueFile' => $templateField->file ? $templateField->file->getShortJson() : null
            ];
        }

        return $result;
    }

    public function toArray($request)
    {
        $parentJson = $this->parent ? [
            'id' => $this->parent->id,
            'title' => $this->parent->title,
            'path' => $this->parent->url_path
        ] : null;

        return [
            'id' => $this->id,
            'template' => $this->template,
            'title' => $this->title,
            'urlPath' => $this->url_path,
            'parent' => $parentJson,
            'image' => $this->image ? $this->image->getShortJson() : null,
            'excerpt' => $this->excerpt,
            'body' => $this->body,
            'pageTitle' => $this->page_title,
            'pageDescription' => $this->page_description,
            'openGraphTitle' => $this->open_graph_title,
            'openGraphDescription' => $this->open_graph_description,
            'openGraphImage' => $this->openGraphImage ? $this->openGraphImage->getShortJson() : null,
            'templateValues' => $this->getTemplateValuesJson()
        ];
    }
}
