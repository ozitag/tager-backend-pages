<?php

namespace OZiTAG\Tager\Backend\Pages\Requests;

use Ozerich\FileStorage\Rules\FileRule;
use OZiTAG\Tager\Backend\Crud\Requests\CrudFormRequest;
use OZiTAG\Tager\Backend\Pages\Rules\TemplateRule;

class CreatePageRequest extends CrudFormRequest
{
    public function rules()
    {
        return [
            'title' => 'required|string',
            'template' => ['nullable', 'string', new TemplateRule()],
            'parent' => ['nullable', 'integer', 'exists:tager_pages,id']
        ];
    }
}
