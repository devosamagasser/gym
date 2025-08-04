<?php

namespace App\Models;

use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Feature;
use App\Models\ProductFeatureValues;

class Product extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory, Translatable, InteractsWithMedia;

    protected $fillable = [
        'price',
        'sale',
        'stock',
        'category_id',
        'brand_id',
        'is_active'
    ];

    public $translatedAttributes = [
        'name',
        'description'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'sale' => 'decimal:2',
        'stock' => 'integer',
        'is_active' => 'boolean'
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('cover')->singleFile(); 
        $this->addMediaCollection('gallery');
    }


    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function features()
    {
        return $this->belongsToMany(Feature::class, 'product_feature_values')
                    ->withPivot('value');
    }

    public function featureValues()
    {
        return $this->hasMany(ProductFeatureValues::class);
    }
}
