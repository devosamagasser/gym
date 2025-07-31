<?php

namespace App\Services;

use App\Models\Category;

class CategoryService
{
    public function create(array $data): Category
    {
        $translations = [
            'en' => [
                'name' => $data['name']['en'],
                'description' => $data['description']['en'] ?? null,
            ],
            'ar' => [
                'name' => $data['name']['ar'],
                'description' => $data['description']['ar'] ?? null,
            ],
        ];

        return Category::create(array_merge($data, $translations));
    }

    public function update(Category $category, array $data): Category
    {
        $translations = [
            'en' => [
                'name' => $data['name']['en'],
                'description' => $data['description']['en'] ?? null,
            ],
            'ar' => [
                'name' => $data['name']['ar'],
                'description' => $data['description']['ar'] ?? null,
            ],
        ];

        $category->update(array_merge($data, $translations));

        return $category;
    }
}
