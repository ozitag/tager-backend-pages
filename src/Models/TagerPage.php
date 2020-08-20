<?php

namespace OZiTAG\Tager\Backend\Pages\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kalnoy\Nestedset\NodeTrait;
use Ozerich\FileStorage\Models\File;
use OZiTAG\Tager\Backend\Fields\Enums\FieldType;
use OZiTAG\Tager\Backend\Fields\TypeFactory;
use OZiTAG\Tager\Backend\Pages\TagerPagesConfig;

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

    private function getValuesByFields($modelFields, $templateFields)
    {
        $result = [];

        foreach ($templateFields as $field => $templateField) {
            $type = $templateField['type'];

            $found = null;
            foreach ($modelFields as $modelField) {
                if ($modelField->field == $field) {
                    $found = $modelField;
                    break;
                }
            }

            if (!$found) {
                $result[] = [
                    'field' => $field,
                    'value' => $type == FieldType::Repeater ? [] : null
                ];

                continue;
            }

            if ($type == FieldType::Repeater) {
                $repeaterValue = [];

                foreach ($found->children as $child) {
                    $repeaterValue[] = $this->getValuesByFields($child->children, $templateField['fields']);
                }

                $result[] = ['field' => $field, 'value' => $repeaterValue];
            } else {

                if ($type == FieldType::File || $type == FieldType::Image) {
                    $value = $found->files ? $found->files[0] : null;
                } else if ($type == FieldType::Gallery) {
                    $value = $found->files;
                } else {
                    $value = $found->value;
                }

                $type = TypeFactory::create($type);
                $type->setValue($value);

                $result[] = [
                    'field' => $field,
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

        $templateConfig = TagerPagesConfig::getTemplateConfig($this->template);
        $templateFields = $templateConfig['fields'] ?? [];

        return $this->getValuesByFields($this->templateFields, $templateFields);
    }
}
