<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class ProductsTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_contains_empty_table(): void
    {
        $response = $this
            ->loginAs()
            ->get('/products');

        $response->assertOk();
        $response->assertSeetext(__('No products found'));
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

        $response = $this
            ->loginAs()
            ->get('/products');

        $response->assertOk();
        $response->assertViewHas('products', function (LengthAwarePaginator $collection) use ($product) {
            return $collection->contains($product);
        });
    }

    public function test_paginated_products_table_doesnt_contain_11th_record()
    {
        Category::factory(3)->create();
        $products = Product::factory(11)->create();

        $lastProduct = $products->last();

        $response = $this
            ->loginAs()
            ->get('/products');

        $response->assertOk();
        $response->assertViewHas('products', function (LengthAwarePaginator $collection) use ($lastProduct) {
            return $collection->doesntContain($lastProduct);
        });
    }

    public function test_admin_can_see_products_create_button()
    {
        $response = $this->loginAs(isAdmin: true)->get('/products');

        $response->assertOk();
        $response->assertSeeText('Add new product');
    }

    public function test_non_admin_cannot_see_products_create_button()
    {
        $response = $this->loginAs()->get('/products');

        $response->assertOk();
        $response->assertDontSee('Add new product');
    }

    public function test_admin_can_access_product_create_page()
    {
        $response = $this
            ->loginAs(isAdmin: true)
            ->get('/products/create');

        $response->assertOk();
    }

    public function test_non_admin_cannot_access_product_create_page()
    {
        $response = $this
            ->loginAs()
            ->get('/products/create');

        $response->assertForbidden();
    }
}
