<?php

namespace OZiTAG\Tager\Backend\Pages\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use OZiTAG\Tager\Backend\Pages\Models\TagerPageField;
use OZiTAG\Tager\Backend\Pages\TagerPagesConfig;
use OZiTAG\Tager\Backend\Fields\Enums\FieldType;

class AdminPageFullResource extends JsonResource
{
    private function getRepeaterValue($children, $fields)
    {
        $result = [];

        foreach ($children as $child) {
            $row = [];
            foreach ($fields as $field => $fieldData) {
                $type = $fieldData['type'];

                $found = null;
                foreach ($child->children as $item) {
                    if ($item->field == $field) {
                        $found = $item;
                        break;
                    }
                }

                if (!$found) {
                    $row[$field] = null;
                } else {
                    $row[$field] = $this->getValue($found, $fieldData);
                }
            }

            $result[] = $row;
        }

        return $result;
    }

    private function getValue(TagerPageField $templateField, $fieldConfig)
    {
        $type = $fieldConfig['type'];

        if ($type == FieldType::Repeater) {
            return $this->getRepeaterValue($templateField->children, $fieldConfig['fields']);
        } else if ($type == FieldType::File) {
            return $templateField->files ? $templateField->files[0]->getUrl() : null;
        } else if ($type == FieldType::Image) {
            return $templateField->files ? $templateField->files[0]->getFullJson() : null;
        } else if ($type == FieldType::Gallery) {
            $result = [];

            foreach ($templateField->files as $file) {
                $result[] = $file->getFullJson();
            }

            return $result;
        } else {
            return $templateField->value;
        }
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
                'name' => $templateField->field,
                'type' => $field['type'],
                'value' => $this->getValue($templateField, $field)
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
