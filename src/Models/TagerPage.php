<?php

namespace OZiTAG\Tager\Backend\Pages\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kalnoy\Nestedset\NodeTrait;
use Ozerich\FileStorage\Models\File;
use OZiTAG\Tager\Backend\Fields\Base\Field;
use OZiTAG\Tager\Backend\Fields\Fields\RepeaterField;
use OZiTAG\Tager\Backend\Fields\TypeFactory;
use OZiTAG\Tager\Backend\Pages\Utils\TagerPagesConfig;
use OZiTAG\Tager\Backend\Pages\Utils\TagerPagesTemplates;

class TagerPage extends Model
{
    use NodeTrait;

    use SoftDeletes;

    protected $table = 'tager_pages';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'parent_id',
        'template',
        'url_path',
        'image_id',
        'title',
        'excerpt',
        'body',
        'page_title',
        'page_description',
        'open_graph_title',
        'open_graph_description',
        'open_graph_image_id'
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

    /**
     * @param array $modelFields
     * @param Field[] $templateFields
     * @return array
     * @throws \OZiTAG\Tager\Backend\Fields\Exceptions\InvalidTypeException
     */
    private function getValuesByFields($modelFields, $templateFields)
    {
        $result = [];

        foreach ($templateFields as $field => $templateField) {
            $type = $templateField->getType();

            $found = null;
            foreach ($modelFields as $modelField) {
                if ($modelField->field == $field) {
                    $found = $modelField;
                    break;
                }
            }

            $isRepeater = $templateField instanceof RepeaterField;

            if (!$found) {
                $result[] = [
                    'name' => $field,
                    'value' => $type->isArray() ? [] : null
                ];

                continue;
            }

            if ($isRepeater) {
                $repeaterValue = [];

                foreach ($found->children as $child) {
                    $repeaterValue[] = $this->getValuesByFields($child->children, $templateField->getFields());
                }

                $result[] = ['name' => $field, 'value' => $repeaterValue];
            } else {

                $type = TypeFactory::create($type);
                $type->setValue($type->isFileType() ? $found->files : $found->value);

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
}
