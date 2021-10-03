<?php

namespace OZiTAG\Tager\Backend\Pages\Jobs\Clone;

use Ozerich\FileStorage\Storage;
use OZiTAG\Tager\Backend\Core\Jobs\Job;
use OZiTAG\Tager\Backend\Fields\Base\Field;
use OZiTAG\Tager\Backend\Fields\Fields\GroupField;
use OZiTAG\Tager\Backend\Pages\Models\TagerPage;
use OZiTAG\Tager\Backend\Pages\Models\TagerPageField;
use OZiTAG\Tager\Backend\Pages\Models\TagerPageFieldFile;
use OZiTAG\Tager\Backend\Pages\Repositories\PageFieldFilesRepository;
use OZiTAG\Tager\Backend\Pages\Repositories\PageFieldsRepository;
use OZiTAG\Tager\Backend\Pages\Repositories\PagesRepository;
use OZiTAG\Tager\Backend\Pages\Utils\TagerPagesTemplates;

class CloneTemplateFieldsJob extends Job
{
    protected TagerPage $oldPage;

    protected TagerPage $newPage;

    protected Storage $storage;

    protected PageFieldsRepository $pageFieldsRepository;

    protected PageFieldFilesRepository $pageFieldFilesRepository;

    public function __construct(TagerPage $oldPage, TagerPage $newPage)
    {
        $this->oldPage = $oldPage;

        $this->newPage = $newPage;
    }

    private function cloneTemplateField(Field $field)
    {
        if ($field instanceof GroupField) {
            foreach ($field->getFields() as $field) {
                $this->cloneTemplateField($field);
            }
            return;
        }

        print_r($field);
        exit;
        print_r($field->getName() . "\n");
    }

    private function processFields(?int $parentId = null, ?int $newParentId = null)
    {
        /** @var TagerPageField[] $fields */
        $fields = $this->pageFieldsRepository->builder()->where('page_id', $this->oldPage->id)
            ->where('parent_id', $parentId)->get();

        foreach ($fields as $field) {
            $newFieldModel = new TagerPageField();

            $newFieldModel->page_id = $this->newPage->id;
            $newFieldModel->parent_id = $newParentId;
            $newFieldModel->field = $field->field;
            $newFieldModel->value = $field->value;

            $newFieldModel->save();

            /** @var TagerPageFieldFile[] $fileFields */
            $fileFields = $this->pageFieldFilesRepository->builder()->where('field_id', $field->id)->get();
            foreach ($fileFields as $fileField) {
                $newFileId = $this->storage->clone($fileField->file_id);
                if (!$newFileId) continue;

                $this->pageFieldFilesRepository->create([
                    'field_id' => $newFieldModel->id,
                    'file_id' => $newFileId
                ]);
            }

            $this->processFields($field->id, $newFieldModel->id);
        }
    }

    public function handle(PagesRepository $pagesRepository, Storage $storage, PageFieldsRepository $pageFieldsRepository, PageFieldFilesRepository $pageFieldFilesRepository)
    {
        $this->storage = $storage;

        $this->pageFieldsRepository = $pageFieldsRepository;

        $this->pageFieldFilesRepository = $pageFieldFilesRepository;

        if (!$this->oldPage->template) {
            return $this->newPage;
        }

        $template = (new TagerPagesTemplates())->get($this->oldPage->template);
        if (!$template) {
            return $this->newPage;
        }

        $pageFieldsRepository->removeByPageId($this->newPage->id);

        $this->processFields();

        return $this->newPage;
    }
}
