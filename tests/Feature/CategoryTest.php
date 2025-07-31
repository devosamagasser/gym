<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_category_with_translations()
    {
        $payload = [
            'name' => ['en' => 'Yoga', 'ar' => 'يوغا'],
            'description' => ['en' => 'Yoga desc', 'ar' => 'وصف يوغا'],
        ];

        $response = $this->postJson('/api/categories', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.name', 'Yoga')
            ->assertJsonPath('data.description', 'Yoga desc')
            ->assertJsonPath('data.translations.en.name', 'Yoga')
            ->assertJsonPath('data.translations.ar.name', 'يوغا');

        $this->assertDatabaseHas('category_translations', [
            'name' => 'Yoga',
            'locale' => 'en',
        ]);
        $this->assertDatabaseHas('category_translations', [
            'name' => 'يوغا',
            'locale' => 'ar',
        ]);
    }

    public function test_show_category_returns_translation_based_on_locale()
    {
        $category = Category::create([
            'en' => ['name' => 'Gym', 'description' => 'English Desc'],
            'ar' => ['name' => 'نادي', 'description' => 'وصف بالعربية'],
        ]);

        $this->getJson('/api/categories/'.$category->id)
            ->assertJsonPath('data.name', 'Gym');

        app()->setLocale('ar');

        $this->getJson('/api/categories/'.$category->id)
            ->assertJsonPath('data.name', 'نادي');
    }
}
