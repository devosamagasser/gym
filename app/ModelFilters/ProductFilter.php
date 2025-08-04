<?php 

namespace App\ModelFilters;

use EloquentFilter\ModelFilter;

class ProductFilter extends ModelFilter
{
    /**
    * Related Models that have ModelFilters as well as the method on the ModelFilter
    * As [relationMethod => [input_key1, input_key2]].
    *
    * @var array
    */
    public $relations = [];

    public function name($value)
    {
        return $this->whereHas('translations', function ($query) use ($value) {
            $query->where('name', 'like', "%{$value}%");
        });
    }

    public function isActive($value)
    {
        return $this->where('is_active', (bool) $value);
    }

    public function sale($value)
    {
        if ($value === 'true') {
            return $this->whereNotNull('sale');
        }
    }

    public function category($value)
    {
        return $this->where('category_id',$value);
    }

    public function brand($value)
    {
        return $this->where('brand_id', $value);
    }

}
