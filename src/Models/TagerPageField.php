<?php

namespace OZiTAG\Tager\Backend\Pages\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Ozerich\FileStorage\Models\File;

class TagerPageField extends Model
{
    public $timestamps = false;

    protected $table = 'tager_page_fields';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'parent_id',
        'page_id',
        'field',
        'value'
    ];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function files()
    {
        return $this->belongsToMany(
            File::class,
            'tager_page_field_files',
            'field_id',
            'file_id'
        )->orderBy('tager_page_field_files.id', 'asc');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(self::class);
    }
}
