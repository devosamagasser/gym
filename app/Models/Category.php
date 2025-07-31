<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;
use Spatie\MediaLibrary\InteractsWithMedia;

class Category extends Model
{
    use Translatable, InteractsWithMedia;

    protected $fillable = ['name', 'description', 'is_active'];
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
