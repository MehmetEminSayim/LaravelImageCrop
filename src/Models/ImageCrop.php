<?php

namespace Imagecrop\Mehmeteminsayim\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImageCrop extends Model
{
    use HasFactory;
    protected $table = "imagecrop";
    protected $fillable = [
        "filename",
        "ext",
        "folder",
        "size"
    ];

}
