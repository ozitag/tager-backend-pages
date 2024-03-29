<?php

namespace OZiTAG\Tager\Backend\Pages\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;
use Ozerich\FileStorage\Storage;
use OZiTAG\Tager\Backend\Core\Utils\TagerVariables;
use OZiTAG\Tager\Backend\Fields\Base\Field;
use OZiTAG\Tager\Backend\Fields\Fields\GroupField;
use OZiTAG\Tager\Backend\Fields\Fields\RepeaterField;
use OZiTAG\Tager\Backend\Fields\Types\GalleryType;
use OZiTAG\Tager\Backend\Pages\Models\TagerPage;
use OZiTAG\Tager\Backend\Pages\Models\TagerPageField;
use OZiTAG\Tager\Backend\Pages\Utils\TagerPagesTemplates;

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
     * @param int|null $parent
     * @return array
     */
    private function getValuesByFields($modelFields, $templateFields, ?int $parent = null)
    {
        $result = [];

        foreach ($templateFields as $field => $templateField) {

            $found = null;
            foreach ($modelFields as $modelField) {
                if ($modelField->field == $field && $modelField->parent_id == $parent) {
                    $found = $modelField;
                    break;
                }
            }

            if (!$found && $templateField instanceof GroupField == false) {
                $result[$field] = null;
                continue;
            }

            if ($templateField instanceof GroupField) {
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
                        if ($scenario instanceof \BackedEnum) {
                            $scenario = $scenario->value;
                        }

                        $type->setValue($found->files);

                        if ($scenario) {
                            foreach ($found->files as $file) {
                                $this->fileStorage->setFileScenario($file->id, $scenario, false);
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
        /** @var TagerPage $model */
        $model = $this->resource;

        if (!$model->template) {
            return null;
        }

        $template = TagerPagesTemplates::get($model->template);
        if (!$template) {
            return null;
        }

        return $this->getValuesByFields($model->templateFields, $template->getFields(), null);
    }

    public function toArray($request)
    {
        /** @var TagerPage $model */
        $model = $this->resource;

        $parentJson = $model->parent ? [
            'id' => $model->parent->id,
            'title' => $model->parent->title,
            'path' => $model->parent->url_path
        ] : null;

        return [
            'id' => $model->id,
            'title' => $model->title,
            'path' => $model->url_path,
            'parent' => $parentJson,
            'image' => $model->image?->getFullJson(),
            'excerpt' => $model->excerpt,
            'body' => $model->body,
            'datetime' => $model->datetime,
            'template' => $model->template,
            'templateFields' => $this->getTemplateValuesJson(),
            'seoParams' => [
                'title' => $model->getWebPageTitle(),
                'description' => $model->getWebPageDescription(),
                'keywords' => $model->getWebPageKeywords(),
                'openGraphImage' => $model->getWebOpenGraphImageUrl(),
                'hiddenFromSeoIndexation' => boolval($model->hidden_from_seo_indexation)
            ]
        ];
    }
}
