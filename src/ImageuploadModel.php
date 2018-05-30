<?php

namespace Pelima\Imageupload;

use Illuminate\Database\Eloquent\Model;

class ImageuploadModel extends Model
{
    public $table = "file_uploads";

    public $primaryKey = "id";

    public $timestamps = true;

    public $fillable = [
        'id',
        'file_path',
    ];

    //
}
