<?php

namespace OZiTAG\Tager\Backend\Pages\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ozerich\FileStorage\Models\File;

class TagerPage extends Model
{
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
        return $this->hasMany(TagerPageField::class);
    }
}
