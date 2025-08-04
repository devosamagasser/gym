<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $category = Category::create(['is_active' => true]);
        $category->translateOrNew('en')->name = 'Default Category';
        $category->translateOrNew('en')->description = 'Default category description';
        $category->translateOrNew('ar')->name = 'تصنيف افتراضي';
        $category->translateOrNew('ar')->description = 'وصف التصنيف الافتراضي';
        $category->save();

        $brand = Brand::create(['is_active' => true]);
        $brand->translateOrNew('en')->name = 'Default Brand';
        $brand->translateOrNew('en')->description = 'Default brand description';
        $brand->translateOrNew('ar')->name = 'ماركة افتراضية';
        $brand->translateOrNew('ar')->description = 'وصف الماركة الافتراضية';
        $brand->save();

        Product::factory()->count(10)->for($category)->for($brand)->create();
    }
}
