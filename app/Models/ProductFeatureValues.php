<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductFeatureValues extends Model
{
    public $timestamps = false;

    protected $fillable = ['value'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'value' => 'string',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function feature()
    {
        return $this->belongsTo(Feature::class);
    }
}
