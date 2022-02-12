<?php

namespace OZiTAG\Tager\Backend\Pages\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kalnoy\Nestedset\NodeTrait;
use Ozerich\FileStorage\Models\File;
use OZiTAG\Tager\Backend\Core\Models\Contracts\IPublicWebModel;
use OZiTAG\Tager\Backend\Core\Models\TModel;
use OZiTAG\Tager\Backend\Fields\Fields\GroupField;
use OZiTAG\Tager\Backend\Fields\Fields\RepeaterField;
use OZiTAG\Tager\Backend\Fields\Types\GalleryType;
use OZiTAG\Tager\Backend\Pages\Utils\TagerPagesConfig;
use OZiTAG\Tager\Backend\Pages\Utils\TagerPagesTemplates;
use OZiTAG\Tager\Backend\Seo\TagerSeo;

/**
 * Class TagerPage
 * @package OZiTAG\Tager\Backend\Pages\Models
 *
 * @property int $id
 * @property string $status
 * @property int $parent_id
 * @property string $url_path
 * @property string $template
 * @property int $image_id
 * @property string $title
 * @property string $excerpt
 * @property string $body
 * @property string $datetime
 * @property string $page_title
 * @property string $page_description
 * @property string $page_keywords
 * @property string $open_graph_image_id
 *
 * @property TagerPage $parent
 * @property File $image
 * @property File $openGraphImage
 * @property TagerPageField[] $templateFields
 */
class TagerPage extends TModel implements IPublicWebModel
{
    use NodeTrait;

    use SoftDeletes;

    static string $defaultOrder = 'id desc';

    protected $table = 'tager_pages';

    protected $fillable = [
        'parent_id', 'status', 'template', 'url_path', 'image_id',
        'title', 'excerpt', 'body', 'datetime',
        'page_title', 'page_description', 'page_keywords', 'open_graph_image_id'
    ];

    public function parent()
    {
        return $this->belongsTo(self::class);
    }

    public function image()
    {
        return $this->belongsTo(File::class);
    }

    public function openGraphImage()
    {
        return $this->belongsTo(File::class);
    }

    public function templateFields()
    {
        return $this->hasMany(TagerPageField::class, 'page_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::deleted(function (self $page) {
            $page->image()->delete();

            $page->openGraphImage()->delete();
        });
    }

    private function getValuesByFields(Collection $modelFields, array $templateFields): array
    {
        $result = [];

        $modelFieldsMap = [];
        foreach ($modelFields as $modelField) {
            $modelFieldsMap[$modelField->field_id] = $modelField;
        }

        foreach ($templateFields as $field => $templateField) {
            $type = $templateField->getTypeInstance();

            $found = null;
            foreach ($modelFields as $modelField) {
                if ($modelField->field == $field) {
                    $found = $modelField;
                    break;
                }
            }

            $isRepeater = $templateField instanceof RepeaterField;
            $isGroup = $templateField instanceof GroupField;

            if (!$found && !$isGroup) {
                $result[] = [
                    'name' => $field,
                    'value' => $templateField->getTypeInstance()->isArray() ? [] : null
                ];

                continue;
            }

            if ($isRepeater) {
                $repeaterValue = [];

                foreach ($found->children as $child) {
                    $repeaterValue[] = $this->getValuesByFields($child->children, $templateField->getFields());
                }

                $result[] = [
                    'name' => $isGroup ? 'group' . ($field + 1) : $field,
                    'value' => $repeaterValue
                ];
            } else if ($isGroup) {
                $result[] = [
                    'name' => 'group' . ((int)$field + 1),
                    'value' => $this->getValuesByFields($modelFields, $templateField->getFields())
                ];
            } else {

                if ($type instanceof GalleryType && $type->hasCaptions()) {
                    $value = [];
                    $jsonData = json_decode($found->value, true);
                    foreach ($found->files as $file) {

                        foreach ($jsonData as $jsonDatum) {
                            if (isset($jsonDatum['id']) && $jsonDatum['id'] == $file->id) {
                                $value[] = [
                                    'id' => $file->id,
                                    'caption' => $jsonDatum['caption'] ?? ''
                                ];
                                break;
                            }
                        }
                    }
                    $type->setValue($value);
                } else {
                    if ($type->isFileType()) {
                        $type->setValue($found->files);
                    } else {
                        $type->loadValueFromDatabase($found->value);
                    }
                }

                $result[] = [
                    'name' => $field,
                    'value' => $type->getAdminFullJson()
                ];
            }
        }

        return $result;
    }

    public function getTemplateValuesJsonAttribute()
    {
        if (!$this->template) {
            return null;
        }

        $tagerTemplates = new TagerPagesTemplates;

        $template = $tagerTemplates->get($this->template);
        if (!$template) {
            return null;
        }

        return $this->getValuesByFields($this->templateFields, $template->getFields());
    }

    public function getTemplateNameAttribute()
    {
        if (!$this->template) {
            return '';
        }

        $template = TagerPagesTemplates::get($this->template);
        return $template ? $template->getLabel() : $this->template . ' (Template not found)';
    }

    public function getWebPageUrl(): string
    {
        return $this->url_path;
    }

    public function getWebPageTitle(): string
    {
        return $this->page_title ?? TagerSeo::getPageTitle('page', [
                'title' => $this->title,
                'excerpt' => $this->excerpt
            ]);
    }

    public function getWebPageDescription(): ?string
    {
        return $this->page_description ?? TagerSeo::getPageTitle('page', [
                'title' => $this->title,
                'excerpt' => $this->excerpt
            ]);
    }

    public function getWebPageKeywords(): ?string
    {
        return $this->page_keywords ?? TagerSeo::getPageKeywords('page', [
                'title' => $this->title,
                'excerpt' => $this->excerpt
            ]);
    }

    public function getWebOpenGraphImageUrl(): ?string
    {
        return $this->openGraphImage ?
            $this->openGraphImage->getDefaultThumbnailUrl(TagerPagesConfig::getOpenGraphScenario()) : null;
    }


    public function getPanelType(): ?string
    {
        return __('tager-pages::panel.type');
    }

    public function getPanelTitle(): ?string
    {
        return $this->title;
    }

    public function getPageFieldValue(string $field): ?string
    {
        return $this->templateFields()->where('field', '=', $field)->pluck('value')->first();
    }
}
