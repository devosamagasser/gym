<?php

namespace App\ModelFilters;

use EloquentFilter\ModelFilter;

class BrandFilter extends ModelFilter
{
    public $relations = [];

    public function name($value)
    {
        return $this->whereLike('name', "%$value%");
    }

    public function isActive($value)
    {
        return $this->where('is_active', (bool) $value);
    }
}
