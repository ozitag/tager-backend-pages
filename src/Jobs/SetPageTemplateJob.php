<?php

namespace OZiTAG\Tager\Backend\Pages\Jobs;

use Illuminate\Queue\Jobs\Job;
use Ozerich\FileStorage\Repositories\FileRepository;
use Ozerich\FileStorage\Repositories\IFileRepository;
use Ozerich\FileStorage\Storage;
use OZiTAG\Tager\Backend\Pages\Enums\FieldType;
use OZiTAG\Tager\Backend\Pages\Exceptions\InvalidUrlPathException;
use OZiTAG\Tager\Backend\Pages\Models\TagerPage;
use OZiTAG\Tager\Backend\Pages\Repositories\PageFieldsRepository;
use OZiTAG\Tager\Backend\Pages\Repositories\PagesRepository;
use OZiTAG\Tager\Backend\Pages\TagerPagesConfig;

class SetPageTemplateJob
{
    /** @var TagerPage */
    private $model;

    private $template;

    private $fields;

    public function __construct($model, $template, $fields)
    {
        $this->model = $model;
        $this->template = $template;
        $this->fields = $fields;
    }

    public function handle(PageFieldsRepository $repository, IFileRepository $fileRepository, Storage $storage)
    {
        $template = TagerPagesConfig::getTemplateConfig($this->template);
        if (!$template) {
            return $this->model;
        }

        $this->model->template = $this->template;
        $this->model->save();

        $repository->removeByPageId($this->model->id);

        foreach ($this->fields as $fieldItem) {
            $configField = TagerPagesConfig::getField($this->template, $fieldItem['name']);
            if (!$configField) {
                continue;
            }

            $value = $fieldItem['value'] ?? null;
            $fileId = null;

            if (isset($configField['type']) && ($configField['type'] == FieldType::File || $configField['type'] == FieldType::Image)) {
                $fileModel = $fileRepository->find($value);

                if (!$fileModel) {
                    continue;
                }

                $scenario = $configField['params']['scenario'] ?? null;
                if ($scenario) {
                    $storage->setFileScenario($fieldItem['value'], $scenario);
                }

                $fileId = $fileModel->id;
                $value = null;
            }

            $repository->create([
                'page_id' => $this->model->id,
                'field' => $fieldItem['name'],
                'value' => $value,
                'file_id' => $fileId
            ]);
        }

        return $this->model;
    }
}
