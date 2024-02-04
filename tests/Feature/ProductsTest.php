<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Tests\TestCase;

class ProductsTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_contains_empty_table(): void
    {
        $response = $this->get('/products');

        $response->assertStatus(200);
        $response->assertSee(__('No products found'));
    }

    public function test_homepage_contains_non_empty_table(): void
    {
        Category::create([
            'name'  => 'Category 1',
        ]);

        $product = Product::create([
            'name'  => 'Product 1',
            'category_id'  => 1,
            'description' => 'desc',
            'price' => 123,
        ]);

        $response = $this->get('/products');
        $response->assertStatus(200);

        $response->assertViewHas('products', function (Collection $collection) use ($product) {
            return $collection->contains($product);
        });
    }

    public function test_paginated_products_table_doesnt_contain_11th_record()
    {
        Category::factory(3)->create();
        $products = Product::factory(11)->create();

        $lastProduct = $products->last();

        $response = $this->get('/products');

        $response->assertStatus(200);
        $response->assertViewHas('products', function (LengthAwarePaginator $collection) use ($lastProduct) {
            return $collection->doesntContain($lastProduct);
        });
    }
}
