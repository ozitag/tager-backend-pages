<?php

namespace OZiTAG\Tager\Backend\Pages\Requests;

use Ozerich\FileStorage\Rules\FileRule;
use OZiTAG\Tager\Backend\Pages\Rules\TemplateRule;

/**
 * @property string $template
 * @property string $title
 * @property int $parent
 * @property string $image
 * @property string $excerpt
 * @property string $body
 * @property string $datetime
 * @property string $pageTitle
 * @property string $pageDescription
 * @property string $pageKeywords
 * @property string $openGraphImage
 * @property float $sitemapPriority
 * @property string $sitemapFrequency
 * @property boolean $hiddenFromSeoIndexation
 * @property array $templateFields
 */
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
            'sitemapPriority' => ['nullable', 'number'],
            'sitemapFrequency' => ['nullable', 'string'],
            'hiddenFromSeoIndexation' => ['required', 'boolean'],
            'templateFields' => 'nullable|array',
            'templateFields.*.name' => 'required|string',
            'templateFields.*.value' => 'nullable',
        ]);
    }
}
