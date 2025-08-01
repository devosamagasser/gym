<?php

namespace App\Models;

use EloquentFilter\Filterable;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;
use Spatie\MediaLibrary\InteractsWithMedia;

class Category extends Model implements HasMedia
{
    use Translatable, InteractsWithMedia, Filterable;

    protected $fillable = ['is_active'];
    public $translatedAttributes = ['name', 'description'];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('cover')->singleFile();
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
