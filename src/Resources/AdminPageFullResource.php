<?php

namespace OZiTAG\Tager\Backend\Pages\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use OZiTAG\Tager\Backend\Pages\Models\TagerPageField;
use OZiTAG\Tager\Backend\Pages\TagerPagesConfig;
use OZiTAG\Tager\Backend\Fields\Enums\FieldType;

class AdminPageFullResource extends JsonResource
{
    private function getValue(TagerPageField $templateField, $type)
    {
        if ($type == FieldType::File || $type == FieldType::Image) {
            return $templateField->file ? $templateField->file->getShortJson() : null;
        }

        return $templateField->value;
    }

    private function getTemplateValuesJson()
    {
        if (!$this->template) {
            return [];
        }

        $result = [];

        foreach ($this->templateFields as $templateField) {
            $field = TagerPagesConfig::getField($this->template, $templateField->field);
            if (!$field) {
                continue;
            }

            $result[] = [
                'field' => $templateField->field,
                'type' => $field['type'],
                'value' => $this->getValue($templateField, $field['type'])
            ];
        }

        return $result;
    }

    public function toArray($request)
    {
        $parentJson = $this->parent ? [
            'id' => $this->parent->id,
            'title' => $this->parent->title,
        ] : null;


        return [
            'id' => $this->id,
            'template' => $this->template,
            'title' => $this->title,
            'path' => $this->url_path,
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
