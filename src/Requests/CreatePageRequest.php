<?php

namespace OZiTAG\Tager\Backend\Pages\Requests;

use Ozerich\FileStorage\Rules\FileRule;
use OZiTAG\Tager\Backend\Crud\Requests\CrudFormRequest;
use OZiTAG\Tager\Backend\Pages\Rules\TemplateRule;

/**
 * Class CreatePageRequest
 * @package OZiTAG\Tager\Backend\Pages\Requests
 *
 * @property string $title
 * @property string $path
 * @property string $template
 * @property integer $parent
 */
class CreatePageRequest extends CrudFormRequest
{
    public function rules()
    {
        return [
            'title' => 'required|string',
            'path' => ['required', 'string', 'unique:tager_pages,url_path,' . $this->route('id', 0) . ',id,deleted_at,NULL'],
            'template' => ['nullable', 'string', new TemplateRule()],
            'parent' => ['nullable', 'integer', 'exists:tager_pages,id,id,!0,deleted_at,NULL']
        ];
    }

    public function getPath(): ?string
    {
        $path = $this->path;
        if (empty($path)) {
            return null;
        }

        return preg_replace('#\/+$#si', '', $path);
    }
}
