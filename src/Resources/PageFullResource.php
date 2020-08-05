<?php

namespace OZiTAG\Tager\Backend\Pages\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use OZiTAG\Tager\Backend\Pages\TagerPagesConfig;
use OZiTAG\Tager\Backend\Seo\Resources\SeoParamsResource;

class PageFullResource extends JsonResource
{
    private function getTemplateValuesJson()
    {
        if (!$this->template) {
            return null;
        }

        $result = [];

        $templateConfig = TagerPagesConfig::getTemplateConfig($this->template);
        $templateFiedls = $templateConfig['fields'] ?? [];

        foreach ($templateFiedls as $field => $templateField) {
            $value = null;
            foreach ($this->templateFields as $templateField) {
                if ($templateField->field == $field) {
                    $value = $templateField->file ? $templateField->file->getFullJson() : $templateField->value;;
                }
            }
            $result[$field] = $value;
        }

        if (empty($result)) {
            return new \stdClass;
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

        $seoParams = new SeoParamsResource($this->page_title, $this->page_description);

        $openGraphUrl = null;
        if($this->openGraphImage){
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
