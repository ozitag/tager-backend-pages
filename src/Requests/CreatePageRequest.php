<?php

namespace OZiTAG\Tager\Backend\Pages\Requests;

use Ozerich\FileStorage\Rules\FileRule;
use OZiTAG\Tager\Backend\Blog\Models\BlogCategory;
use OZiTAG\Tager\Backend\Core\FormRequest;
use OZiTAG\Tager\Backend\Pages\Rules\TemplateRule;

class CreatePageRequest extends FormRequest
{
    public function rules()
    {
        return [
            'template' => ['nullable', 'string', new TemplateRule()],
            'title' => 'required|string',
            'urlPath' => ['nullable', 'string', 'unique:tager_pages,url_path,0,id,deleted_at,NULL'],
            'parent' => ['nullable', 'integer', 'exists:tager_pages,id'],
            'image' => ['nullable', 'numeric', new FileRule()],
            'excerpt' => 'nullable|string',
            'body' => 'nullable|string',
            'pageTitle' => 'nullable|string',
            'pageDescription' => 'nullable|string',
            'openGraphTitle' => 'nullable|string',
            'openGraphDescription' => 'nullable|string',
            'openGraphImage' => ['nullable', 'numeric', new FileRule()],
            'templateValues' => 'nullable|array',
            'templateValues.*.field' => 'required|string',
            'templateValues.*.value' => 'nullable',
        ];
    }
}
