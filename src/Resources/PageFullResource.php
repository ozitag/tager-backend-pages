<?php

namespace OZiTAG\Tager\Backend\Pages\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use OZiTAG\Tager\Backend\Seo\Resources\SeoParamsResource;

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

        $seoParams = new SeoParamsResource($this->page_title, $this->page_description, $this->openGraphImage, $this->open_graph_title, $this->open_graph_description);

        return [
            'id' => $this->id,
            'template' => $this->template,
            'title' => $this->title,
            'path' => $this->url_path,
            'parent' => $parentJson,
            'image' => $this->image ? $this->image->getShortJson() : null,
            'excerpt' => $this->excerpt,
            'body' => $this->body,
            'seoParams' => $seoParams,
            'templateValues' => $this->getTemplateValuesJson()
        ];
    }
}
