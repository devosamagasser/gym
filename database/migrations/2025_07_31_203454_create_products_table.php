<?php

use App\Models\Brand;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->decimal('price', 10, 2)->default(0.00);
            $table->unsignedInteger('stock')->default(0);
            $table->foreignIdFor(Category::class)->nullable()->constrained('categories')->cascadeOnDelete();
            $table->foreignIdFor(Brand::class)->nullable()->constrained('brands')->cascadeOnDelete();
            $table->decimal('sale', 10, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('product_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Product::class)->constrained()->cascadeOnDelete();
            $table->char('locale',5)->index();
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->unique(['product_id', 'locale']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_translations');
        Schema::dropIfExists('products');

    }
};
