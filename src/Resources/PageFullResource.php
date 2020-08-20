<?php

namespace OZiTAG\Tager\Backend\Pages\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use OZiTAG\Tager\Backend\Fields\Enums\FieldType;
use OZiTAG\Tager\Backend\Fields\TypeFactory;
use OZiTAG\Tager\Backend\Pages\TagerPagesConfig;
use OZiTAG\Tager\Backend\Seo\Resources\SeoParamsResource;

class PageFullResource extends JsonResource
{
    private function getValuesByFields($modelFields, $templateFields)
    {
        $result = [];

        foreach ($templateFields as $field => $templateField) {
            $type = $templateField['type'];

            $found = null;
            foreach ($modelFields as $modelField) {
                if ($modelField->field == $field) {
                    $found = $modelField;
                    break;
                }
            }

            if (!$found) {
                $result[$field] = null;
                continue;
            }

            if ($type == FieldType::Repeater) {
                $repeaterValue = [];

                foreach ($found->children as $child) {
                    $repeaterValue[] = $this->getValuesByFields($child->children, $templateField['fields']);
                }

                $result[$field] = $repeaterValue;
            } else {

                if ($type == FieldType::File || $type == FieldType::Image) {
                    $value = $found->files ? $found->files[0] : null;
                } else if ($type == FieldType::Gallery) {
                    $value = $found->files;
                } else {
                    $value = $found->value;
                }

                $type = TypeFactory::create($type);
                $type->setValue($value);

                $result[$field] = $type->getPublicValue();
            }
        }

        return $result;
    }

    private function getTemplateValuesJson()
    {
        if (!$this->template) {
            return null;
        }

        $templateConfig = TagerPagesConfig::getTemplateConfig($this->template);
        $templateFields = $templateConfig['fields'] ?? [];

        return $this->getValuesByFields($this->templateFields, $templateFields);
    }

    public function toArray($request)
    {
        $parentJson = $this->parent ? [
            'id' => $this->parent->id,
            'title' => $this->parent->title,
            'path' => $this->parent->url_path
        ] : null;

        $seoParams = new SeoParamsResource($this->page_title, $this->page_description);

        $openGraphUrl = null;
        if ($this->openGraphImage) {
            $openGraphUrl = $this->openGraphImage->getDefaultThumbnailUrl(TagerPagesConfig::getOpenGraphScenario());
        }

        $seoParams->setOpenGraph($openGraphUrl, $this->open_graph_title, $this->open_graph_description);

        return [
            'id' => $this->id,
            'title' => $this->title,
            'path' => $this->url_path,
            'parent' => $parentJson,
            'image' => $this->image ? $this->image->getFullJson() : null,
            'excerpt' => $this->excerpt,
            'body' => $this->body,
            'seoParams' => $seoParams,
            'template' => $this->template,
            'templateFields' => $this->getTemplateValuesJson()
        ];
    }
}
