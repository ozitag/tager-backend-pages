<?php

namespace OZiTAG\Tager\Backend\Pages\Features\Admin;

use Illuminate\Http\Resources\Json\JsonResource;
use OZiTAG\Tager\Backend\Core\Feature;
use OZiTAG\Tager\Backend\Core\SuccessResource;
use OZiTAG\Tager\Backend\Pages\Jobs\GetPageByIdJob;
use OZiTAG\Tager\Backend\Pages\Jobs\GetTemplateByAliasJob;

class ViewTemplateFeature extends Feature
{
    private $alias;

    public function __construct($alias)
    {
        $this->alias = $alias;
    }

    private function getMetaJson($fieldModel)
    {
        $result = [];

        if (isset($fieldModel['params'])) {
            foreach ($fieldModel['params'] as $key => $value) {

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
            $result['fields'][] = [
                'field' => $fieldId,
                'type' => $fieldModel['type'],
                'label' => $fieldModel['label'],
                'meta' => $this->getMetaJson($fieldModel)
            ];
        }

        return new JsonResource($result);
    }
}
