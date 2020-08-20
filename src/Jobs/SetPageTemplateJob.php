<?php

namespace OZiTAG\Tager\Backend\Pages\Jobs;

use Ozerich\FileStorage\Repositories\FileRepository;
use Ozerich\FileStorage\Repositories\IFileRepository;
use Ozerich\FileStorage\Storage;
use OZiTAG\Tager\Backend\Core\Jobs\Job;
use OZiTAG\Tager\Backend\Fields\Enums\FieldType;
use OZiTAG\Tager\Backend\Fields\TypeFactory;
use OZiTAG\Tager\Backend\Pages\Models\TagerPage;
use OZiTAG\Tager\Backend\Pages\Models\TagerPageField;
use OZiTAG\Tager\Backend\Pages\Repositories\PageFieldFilesRepository;
use OZiTAG\Tager\Backend\Pages\Repositories\PageFieldsRepository;
use OZiTAG\Tager\Backend\Pages\TagerPagesConfig;

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
     * @param TagerPageField $field
     * @param array $childrenFields
     */
    private function saveRepeaterFields(TagerPageField $baseField, $childrenFields, $fields)
    {
        foreach ($childrenFields as $ind => $childrenField) {

            /** @var TagerPageField $childrenFieldModel */
            $childrenFieldModel = $this->pageFieldsRepository->create([
                'page_id' => $this->model->id,
                'field' => $baseField->field . '[' . $ind . ']',
                'value' => null,
                'parent_id' => $baseField->id
            ]);

            foreach ($childrenField as $field => $fieldModel) {

                $fieldConfig = $fields[$fieldModel['name']];
                $fieldConfig['field'] = $fieldModel['name'];

                $this->saveValue($fieldModel['value'] ?? null, $fieldConfig, $childrenFieldModel);
            }
        }
    }

    private function saveValue($value, $fieldConfig, ?TagerPageField $parent)
    {
        $type = $fieldConfig['type'];

        $databaseValue = null;

        if ($type != FieldType::Repeater) {
            $typeModel = TypeFactory::create($fieldConfig['type']);
            $typeModel->setValue($value);

            $scenario = $fieldConfig['params']['scenario'] ?? null;
            if ($scenario) {
                $typeModel->applyFileScenario($scenario);
            }

            $databaseValue = $typeModel->getDatabaseValue();
        }

        /** @var TagerPageField $item */
        $item = $this->pageFieldsRepository->create([
            'page_id' => $this->model->id,
            'field' => $fieldConfig['field'],
            'value' => $databaseValue,
            'parent_id' => $parent ? $parent->id : null
        ]);

        if ($type != FieldType::Repeater) {
            foreach ($typeModel->getFileIds() as $fileId) {
                $this->pageFieldFilesRepository->create([
                    'field_id' => $item->id,
                    'file_id' => $fileId
                ]);
            }
        } else {
            $this->saveRepeaterFields($item, $value, $fieldConfig['fields']);
        }
    }

    public function handle(PageFieldsRepository $repository, PageFieldFilesRepository $pageFieldFilesRepository, Storage $storage)
    {
        $this->pageFieldsRepository = $repository;
        $this->pageFieldFilesRepository = $pageFieldFilesRepository;
        $this->fileStorage = $storage;

        $template = TagerPagesConfig::getTemplateConfig($this->template);
        if (!$template) {
            return $this->model;
        }

        $this->model->template = $this->template;
        $this->model->save();

        $this->pageFieldsRepository->removeByPageId($this->model->id);

        foreach ($this->fields as $fieldItem) {
            $configField = TagerPagesConfig::getField($this->template, $fieldItem['name']);
            if (!$configField || !isset($configField['type'])) {
                continue;
            }

            $this->saveValue($fieldItem['value'] ?? null, $configField, null);
        }

        return $this->model;
    }
}
