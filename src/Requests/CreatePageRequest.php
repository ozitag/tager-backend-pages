<?php

namespace OZiTAG\Tager\Backend\Pages\Requests;

use Ozerich\FileStorage\Rules\FileRule;
use OZiTAG\Tager\Backend\Crud\Requests\CrudFormRequest;
use OZiTAG\Tager\Backend\Pages\Rules\TemplateRule;
use OZiTAG\Tager\Backend\Pages\Rules\UrlPathRule;

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
            'path' => ['required', 'string', new UrlPathRule($this->route('id'))],
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

        $path = preg_replace('#\/+$#si', '', $path);
        if(empty($path)){
            $path = '/';
        }

        return $path;
    }
}
