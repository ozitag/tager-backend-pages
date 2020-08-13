<?php

namespace OZiTAG\Tager\Backend\Pages\Models;

use Illuminate\Database\Eloquent\Model;
use Ozerich\FileStorage\Models\File;

class TagerPageFieldFile extends Model
{
    public $timestamps = false;

    protected $table = 'tager_page_field_files';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'field_id',
        'file_id'
    ];

    public function field()
    {
        return $this->belongsTo(TagerPageField::class);
    }

    public function file()
    {
        return $this->belongsTo(File::class);
    }
}
