<?php

namespace OZiTAG\Tager\Backend\Pages\Requests;

use Ozerich\FileStorage\Rules\FileRule;
use OZiTAG\Tager\Backend\Pages\Rules\TemplateRule;

class UpdatePageRequest extends CreatePageRequest
{
    public function rules()
    {
        return array_merge(parent::rules(), [
            'template' => ['nullable', 'string', new TemplateRule()],
            'title' => 'required|string',
            'path' => ['required', 'string', 'unique:tager_pages,url_path,' . $this->route('id', 0) . ',id,deleted_at,NULL'],
            'parent' => [
                'nullable', 'integer', 'exists:tager_pages,id,id,!0,deleted_at,NULL',
                'not_in:' . $this->route('id')
            ],
            'image' => ['nullable', new FileRule()],
            'excerpt' => 'nullable|string',
            'body' => 'nullable|string',
            'pageTitle' => 'nullable|string',
            'pageDescription' => 'nullable|string',
            'openGraphImage' => ['nullable', new FileRule()],
            'templateValues' => 'nullable|array',
            'templateValues.*.field' => 'required|string',
            'templateValues.*.value' => 'nullable',
        ]);
    }
}
