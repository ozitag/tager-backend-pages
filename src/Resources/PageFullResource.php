<?php

namespace OZiTAG\Tager\Backend\Pages\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use OZiTAG\Tager\Backend\Fields\Base\Field;
use OZiTAG\Tager\Backend\Fields\Fields\RepeaterField;
use OZiTAG\Tager\Backend\Fields\TypeFactory;
use OZiTAG\Tager\Backend\Pages\Models\TagerPageField;
use OZiTAG\Tager\Backend\Pages\Utils\TagerPagesConfig;
use OZiTAG\Tager\Backend\Pages\Utils\TagerPagesTemplates;
use OZiTAG\Tager\Backend\Core\Resources\SeoParamsResource;

class PageFullResource extends JsonResource
{
    /**
     * @param TagerPageField[] $modelFields
     * @param Field[] $templateFields
     * @return array
     */
    private function getValuesByFields($modelFields, $templateFields)
    {
        $result = [];

        foreach ($templateFields as $field => $templateField) {
            $type = $templateField->getType();

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

            if ($templateField instanceof RepeaterField) {
                $repeaterValue = [];

                foreach ($found->children as $child) {
                    $repeaterValue[] = $this->getValuesByFields($child->children, $templateField->getFields());
                }

                $result[$field] = $repeaterValue;
            } else {

                $type = TypeFactory::create($type);
                $type->setValue($type->isFileType() ? $found->files : $found->value);

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

        $template = TagerPagesTemplates::get($this->template);
        if (!$template) {
            return null;
        }

        return $this->getValuesByFields($this->templateFields, $template->getFields());
    }

    private function getSeoParams()
    {
        $seoParams = new SeoParamsResource(
            empty($this->page_title) == false ? $this->page_title : $this->title,
            empty($this->page_description) == false ? $this->page_description : $this->excerpt
        );

        $openGraphUrl = null;
        if ($this->openGraphImage) {
            $openGraphUrl = $this->openGraphImage->getDefaultThumbnailUrl(TagerPagesConfig::getOpenGraphScenario());
        }

        $seoParams->setOpenGraph($openGraphUrl, $this->open_graph_title, $this->open_graph_description);

        return $seoParams;
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
            'title' => $this->title,
            'path' => $this->url_path,
            'parent' => $parentJson,
            'image' => $this->image ? $this->image->getFullJson() : null,
            'excerpt' => $this->excerpt,
            'body' => $this->body,
            'seoParams' => $this->getSeoParams(),
            'template' => $this->template,
            'templateFields' => $this->getTemplateValuesJson()
        ];
    }
}
