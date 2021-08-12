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
            'parent' => [
                'nullable', 'integer', 'exists:tager_pages,id,id,!0,deleted_at,NULL',
                'not_in:' . $this->route('id')
            ],
            'image' => ['nullable', new FileRule()],
            'excerpt' => 'nullable|string',
            'body' => 'nullable|string',
            'datetime' => 'nullable|date',
            'pageTitle' => 'nullable|string',
            'pageDescription' => 'nullable|string',
            'pageKeywords' => 'nullable|string',
            'openGraphImage' => ['nullable', new FileRule()],
            'templateValues' => 'nullable|array',
            'templateValues.*.field' => 'required|string',
            'templateValues.*.value' => 'nullable',
        ]);
    }
}
