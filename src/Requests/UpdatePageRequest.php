<?php

namespace OZiTAG\Tager\Backend\Pages\Requests;

use Ozerich\FileStorage\Rules\FileRule;
use OZiTAG\Tager\Backend\Blog\Models\BlogCategory;
use OZiTAG\Tager\Backend\Core\FormRequest;

class UpdatePageRequest extends CreatePageRequest
{
    public function rules()
    {
        return array_merge(parent::rules(), [
            'urlPath' => ['required', 'string', 'unique:tager_pages,url_path,' . $this->route('id', 0) . ',id,deleted_at,NULL'],
        ]);
    }
}
