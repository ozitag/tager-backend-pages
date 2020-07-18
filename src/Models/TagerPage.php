<?php

namespace OZiTAG\Tager\Backend\Pages\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
        'name',
        'url_alias',
        'url_path',
        'image_id',
        'excerpt',
        'content',
        'page_title',
        'page_description',
        'open_graph_title',
        'open_graph_description',
        'open_graph_image_id'
    ];
}
