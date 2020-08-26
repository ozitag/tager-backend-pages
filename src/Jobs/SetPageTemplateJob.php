<?php

namespace OZiTAG\Tager\Backend\Pages\Jobs;

use Ozerich\FileStorage\Repositories\FileRepository;
use Ozerich\FileStorage\Repositories\IFileRepository;
use Ozerich\FileStorage\Storage;
use OZiTAG\Tager\Backend\Core\Jobs\Job;
use OZiTAG\Tager\Backend\Fields\Base\Field;
use OZiTAG\Tager\Backend\Fields\Enums\FieldType;
use OZiTAG\Tager\Backend\Fields\Fields\RepeaterField;
use OZiTAG\Tager\Backend\Fields\TypeFactory;
use OZiTAG\Tager\Backend\Pages\Models\TagerPage;
use OZiTAG\Tager\Backend\Pages\Models\TagerPageField;
use OZiTAG\Tager\Backend\Pages\Repositories\PageFieldFilesRepository;
use OZiTAG\Tager\Backend\Pages\Repositories\PageFieldsRepository;
use OZiTAG\Tager\Backend\Pages\Utils\TagerPagesConfig;
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

    /** @var Storage */
    private $fileStorage;

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
            'value' => $type->hasFiles() ? null : $type->getDatabaseValue(),
            'parent_id' => $parent ? $parent->id : null
        ]);

        if ($field instanceof RepeaterField) {
            $this->saveRepeaterFields($item, $field->getFields(), $value);
        } else {
            foreach ($type->getFileIds() as $fileId) {
                $this->pageFieldFilesRepository->create([
                    'field_id' => $item->id,
                    'file_id' => $fileId
                ]);
            }
        }
    }

    public function handle(PageFieldsRepository $repository, PageFieldFilesRepository $pageFieldFilesRepository, Storage $storage)
    {
        $this->pageFieldsRepository = $repository;
        $this->pageFieldFilesRepository = $pageFieldFilesRepository;
        $this->fileStorage = $storage;

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

            $this->saveValue($fieldItem['name'], $fieldItem['value'] ?? null, $field);
        }

        return $this->model;
    }
}
