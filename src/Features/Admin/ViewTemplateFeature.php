<?php

namespace OZiTAG\Tager\Backend\Pages\Features\Admin;

use Illuminate\Http\Resources\Json\JsonResource;
use OZiTAG\Tager\Backend\Core\Features\Feature;
use OZiTAG\Tager\Backend\Fields\Enums\FieldType;
use OZiTAG\Tager\Backend\Pages\Jobs\GetTemplateByAliasJob;

class ViewTemplateFeature extends Feature
{
    private $alias;

    public function __construct($alias)
    {
        $this->alias = $alias;
    }

    /**
     * @param array $fieldModel
     * @return array|\stdClass
     */
    private function getMetaJson($fieldModel)
    {
        $result = [];

        if (isset($fieldModel['params'])) {
            foreach ($fieldModel['params'] as $key => $value) {

                if($key == 'scenario'){
                    continue;
                }

                $valueProcessed = $value;

                if ($key == 'options') {
                    $valueProcessed = [];
                    foreach ($value as $valueKey => $valueItem) {
                        $valueProcessed[] = [
                            'value' => $valueKey,
                            'label' => $valueItem
                        ];
                    }
                }

                $result[$key] = $valueProcessed;
            }
        }

        if (empty($result)) {
            return new \stdClass;
        }

        return $result;
    }

    /**
     * @param array $fieldModel
     * @return array
     */
    private function getRepeaterFields($fieldModel)
    {
        $result = [];

        if (!isset($fieldModel['fields'])) {
            return $result;
        }

        foreach ($fieldModel['fields'] as $fieldId => $field) {
            $field = [
                'name' => $fieldId,
                'type' => $field['type'],
                'label' => $field['label'],
                'meta' => $this->getMetaJson($field),
            ];

            if ($field['type'] == FieldType::Repeater) {
                $field['fields'] = $this->getRepeaterFields($field);
            }

            $result[] = $field;
        }

        return $result;
    }

    public function handle()
    {
        $model = $this->run(GetTemplateByAliasJob::class, ['alias' => $this->alias]);
        if (!$model) {
            abort(404, 'Template not found');
        }

        $result = [
            'id' => $this->alias,
            'label' => $model['label'] ?? 'Template "' . $this->alias . '"',
            'fields' => []
        ];

        foreach ($model['fields'] as $fieldId => $fieldModel) {

            $field = [
                'name' => $fieldId,
                'type' => $fieldModel['type'],
                'label' => $fieldModel['label'],
                'meta' => $this->getMetaJson($fieldModel),
            ];

            if ($fieldModel['type'] == FieldType::Repeater) {
                $field['fields'] = $this->getRepeaterFields($fieldModel);
            }

            $result['fields'][] = $field;
        }

        return new JsonResource($result);
    }
}
