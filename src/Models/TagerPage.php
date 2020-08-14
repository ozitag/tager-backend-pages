<?php

namespace OZiTAG\Tager\Backend\Pages\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kalnoy\Nestedset\NodeTrait;
use Ozerich\FileStorage\Models\File;
use OZiTAG\Tager\Backend\Fields\Enums\FieldType;
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

    private function getRepeaterValue($children, $fields)
    {
        $result = [];

        foreach ($children as $child) {
            $row = [];
            foreach ($fields as $field => $fieldData) {
                $type = $fieldData['type'];

                $found = null;
                foreach ($child->children as $item) {
                    if ($item->field == $field) {
                        $found = $item;
                        break;
                    }
                }

                $fieldModel = [
                    'name' => $field,
                    'value' => null
                ];

                if ($found) {
                    $fieldModel['value'] = $this->getValue($found, $fieldData);
                }

                $row[] = $fieldModel;
            }

            $result[] = $row;
        }

        return $result;
    }

    private function getValue(TagerPageField $templateField, $fieldConfig)
    {
        $type = $fieldConfig['type'];

        if ($type == FieldType::Repeater) {
            return $this->getRepeaterValue($templateField->children, $fieldConfig['fields']);
        } else if ($type == FieldType::File) {
            return $templateField->files ? $templateField->files[0]->getUrl() : null;
        } else if ($type == FieldType::Image) {
            return $templateField->files ? $templateField->files[0]->getShortJson() : null;
        } else if ($type == FieldType::Gallery) {
            $result = [];

            foreach ($templateField->files as $file) {
                $result[] = $file->getShortJson();
            }

            return $result;
        } else {
            return $templateField->value;
        }
    }

    public function getTemplateValuesJsonAttribute()
    {
        if (!$this->template) {
            return [];
        }

        $result = [];

        foreach ($this->templateFields as $templateField) {
            $field = TagerPagesConfig::getField($this->template, $templateField->field);
            if (!$field) {
                continue;
            }

            $result[] = [
                'name' => $templateField->field,
                'value' => $this->getValue($templateField, $field)
            ];
        }

        return $result;
    }
}
