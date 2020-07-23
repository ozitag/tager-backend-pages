<?php

namespace OZiTAG\Tager\Backend\Pages\Requests;

class UpdatePageRequest extends CreatePageRequest
{
    public function rules()
    {
        return array_merge(parent::rules(), [
            'path' => ['required', 'string', 'unique:tager_pages,url_path,' . $this->route('id', 0) . ',id,deleted_at,NULL'],
        ]);
    }
}
