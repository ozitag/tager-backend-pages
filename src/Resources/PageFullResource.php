<?php

namespace OZiTAG\Tager\Backend\Pages\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;
use Ozerich\FileStorage\Storage;
use OZiTAG\Tager\Backend\Core\Utils\TagerVariables;
use OZiTAG\Tager\Backend\Fields\Base\Field;
use OZiTAG\Tager\Backend\Fields\Fields\GalleryField;
use OZiTAG\Tager\Backend\Fields\Fields\GroupField;
use OZiTAG\Tager\Backend\Fields\Fields\RepeaterField;
use OZiTAG\Tager\Backend\Fields\TypeFactory;
use OZiTAG\Tager\Backend\Fields\Types\GalleryType;
use OZiTAG\Tager\Backend\Pages\Models\TagerPageField;
use OZiTAG\Tager\Backend\Pages\Utils\TagerPagesConfig;
use OZiTAG\Tager\Backend\Pages\Utils\TagerPagesTemplates;
use OZiTAG\Tager\Backend\Core\Resources\SeoParamsResource;

class PageFullResource extends JsonResource
{
    private Storage $fileStorage;

    protected TagerVariables $tagerVariables;

    public function __construct($resource)
    {
        $this->fileStorage = App::make(Storage::class);

        $this->tagerVariables = App::make(TagerVariables::class);

        parent::__construct($resource);
    }

    /**
     * @param TagerPageField[] $modelFields
     * @param Field[] $templateFields
     * @return array
     */
    private function getValuesByFields($modelFields, $templateFields)
    {
        $result = [];

        $modelFieldsMap = [];
        foreach ($modelFields as $modelField) {
            $modelFieldsMap[$modelField->field_id] = $modelField;
        }

        foreach ($templateFields as $field => $templateField) {

            $found = null;
            foreach ($modelFields as $modelField) {
                if ($modelField->field == $field) {
                    $found = $modelField;
                    break;
                }
            }

            if (!$found && $templateField instanceof GroupField == false) {
                $result[$field] = null;
                continue;
            }

            if ($templateField instanceof RepeaterField) {
                $repeaterValue = [];

                foreach ($found->children as $child) {
                    $repeaterValue[] = $this->getValuesByFields($child->children, $templateField->getFields());
                }

                $result[$field] = $repeaterValue;
            } else if ($templateField instanceof GroupField) {
                $groupValue = $this->getValuesByFields($modelFields, $templateField->getFields());
                if ($groupValue) {
                    $result = array_merge($result, $groupValue);
                }
            } else {
                $type = $templateField->getTypeInstance();

                if ($type instanceof GalleryType && $type->hasCaptions()) {
                    $value = [];
                    $jsonData = json_decode($found->value, true);
                    foreach ($found->files as $file) {

                        foreach ($jsonData as $jsonDatum) {
                            if (isset($jsonDatum['id']) && $jsonDatum['id'] == $file->id) {
                                $value[] = [
                                    'id' => $file->id,
                                    'caption' => $jsonDatum['caption'] ?? ''
                                ];
                                break;
                            }
                        }
                    }
                    $type->setValue($value);
                } else {
                    if ($type->isFileType()) {
                        $scenario = $templateField->getMetaParamValue('scenario');
                        $type->setValue($found->files);

                        if ($scenario) {
                            foreach ($found->files as $file) {
                                $this->fileStorage->setFileScenario($file->id, $scenario);
                            }
                        }
                    } else {
                        $type->loadValueFromDatabase($found->value);
                    }
                }

                $result[$field] = $type->getPublicValue();

                if (is_string($result[$field])) {
                    $result[$field] = $this->tagerVariables->processText($result[$field]);
                }
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
