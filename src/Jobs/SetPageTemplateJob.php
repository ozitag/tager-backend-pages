<?php

namespace OZiTAG\Tager\Backend\Pages\Jobs;

use Ozerich\FileStorage\Storage;
use OZiTAG\Tager\Backend\Core\Jobs\Job;
use OZiTAG\Tager\Backend\Fields\Base\Field;
use OZiTAG\Tager\Backend\Fields\Fields\GroupField;
use OZiTAG\Tager\Backend\Fields\Fields\RepeaterField;
use OZiTAG\Tager\Backend\Pages\Models\TagerPage;
use OZiTAG\Tager\Backend\Pages\Models\TagerPageField;
use OZiTAG\Tager\Backend\Pages\Repositories\PageFieldFilesRepository;
use OZiTAG\Tager\Backend\Pages\Repositories\PageFieldsRepository;
use OZiTAG\Tager\Backend\Pages\Utils\TagerPagesTemplates;

class SetPageTemplateJob extends Job
{
    /** @var TagerPage */
    private $model;

    private $template;

    private $fields;

    /** @var PageFieldsRepository */
    private $pageFieldsRepository;

    /** @var PageFieldFilesRepository */
    private $pageFieldFilesRepository;

    public function __construct($model, $template, $fields = [])
    {
        $this->model = $model;
        $this->template = $template;
        $this->fields = $fields;
    }


    /**
     * @param TagerPageField $baseField
     * @param Field[] $fields
     * @param array $fieldValues
     */
    private function saveRepeaterFields(TagerPageField $baseField, $fields, $values)
    {
        foreach ($values as $ind => $value) {

            /** @var TagerPageField $childrenFieldModel */
            $childrenFieldModel = $this->pageFieldsRepository->create([
                'page_id' => $this->model->id,
                'field' => $baseField->field . '[' . $ind . ']',
                'value' => null,
                'parent_id' => $baseField->id
            ]);

            foreach ($value as $fieldValue) {
                $param = $fieldValue['name'] ?? null;
                if (!$param) {
                    continue;
                }

                $field = $fields[$param] ?? null;
                if (!$field) {
                    continue;
                }

                $this->saveValue($param, $fieldValue['value'] ?? null, $field, $childrenFieldModel);
            }
        }
    }

    private function saveValue($param, $value, Field $field, ?TagerPageField $parent = null)
    {
        $type = $field->getTypeInstance();
        $type->setValue($value);

        if ($type->hasFiles()) {
            $scenario = $field->getMetaParamValue('scenario');
            if ($scenario) {
                $type->applyFileScenario($scenario);
            }
        }

        /** @var TagerPageField $item */
        $item = $this->pageFieldsRepository->create([
            'page_id' => $this->model->id,
            'field' => $param,
            'value' => $type->getDatabaseValue(),
            'parent_id' => $parent ? $parent->id : null
        ]);

        if ($field instanceof RepeaterField) {
            $this->saveRepeaterFields($item, $field->getFields(), $value);
        } else {
            foreach ($type->getFileIds() as $fileId) {
                $this->pageFieldFilesRepository->create([
                    'field_id' => $item->id,
                    'file_id' => Storage::fromUUIDtoId($fileId)
                ]);
            }
        }
    }

    public function handle(PageFieldsRepository $repository, PageFieldFilesRepository $pageFieldFilesRepository)
    {
        $this->pageFieldsRepository = $repository;
        $this->pageFieldFilesRepository = $pageFieldFilesRepository;

        if (!$this->template) {
            $this->model->template = $this->template;
            $this->model->save();

            $this->pageFieldsRepository->removeByPageId($this->model->id);

            return $this->model;
        }

        $template = TagerPagesTemplates::get($this->template);
        if (!$template) {
            return $this->model;
        }

        $this->model->template = $this->template;
        $this->model->save();

        $this->pageFieldsRepository->removeByPageId($this->model->id);

        foreach ($this->fields as $fieldItem) {

            $field = $template->getField($fieldItem['name']);
            if (!$field) {
                continue;
            }


            if ($field instanceof GroupField) {
                foreach ($fieldItem['value'] as $groupFieldItem) {
                    $groupField = $template->getField($groupFieldItem['name']);
                    if (!$groupField) {
                        continue;
                    }

                    if($groupField instanceof GroupField){
                        foreach($groupFieldItem['value'] as $groupInnerFieldItem){
                            $groupInnerField = $template->getField($groupInnerFieldItem['name']);
                            
                            if (!$groupInnerField) {
                                continue;
                            }

                            $this->saveValue($groupInnerFieldItem['name'], $groupInnerFieldItem['value'] ?? null, $groupInnerField);
                        }
                    } else {
                        $this->saveValue($groupFieldItem['name'], $groupFieldItem['value'] ?? null, $groupField);
                    }
                }
            } else {
                $this->saveValue($fieldItem['name'], $fieldItem['value'] ?? null, $field);
            }
        }

        return $this->model;
    }
}
