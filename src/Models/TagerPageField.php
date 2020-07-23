<?php

namespace OZiTAG\Tager\Backend\Pages\Models;

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
        'page_id',
        'field',
        'value',
        'file_id'
    ];

    public function file()
    {
        return $this->belongsTo(File::class);
    }
}
