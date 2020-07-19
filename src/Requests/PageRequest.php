<?php

namespace OZiTAG\Tager\Backend\Pages\Requests;

use Ozerich\FileStorage\Rules\FileRule;
use OZiTAG\Tager\Backend\Core\FormRequest;

class PageRequest extends FormRequest
{
    public function rules()
    {
        return [
            'template' => 'nullable|string',
            'title' => 'required|string',
            'urlPath' => ['required', 'string', 'unique:tager_pages,url_path,' . $this->route('id', 0) . ',id,deleted_at,NULL'],
            'parent' => 'nullable|number',
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
