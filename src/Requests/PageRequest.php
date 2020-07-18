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
            'title' => 'string|required',
            'urlAlias' => 'string|required',
            'parent' => 'nullable|number',
            'image' => ['nullable', 'numeric', new FileRule()],
            'excerpt' => 'nullable|string',
            'content' => 'nullable|string',
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
