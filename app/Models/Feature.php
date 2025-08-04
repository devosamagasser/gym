<?php

namespace App\Models;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class Feature extends Model
{
    use Translatable;

    public $translatedAttributes = [
        'name',
    ];
}
