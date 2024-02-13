<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class ProductsTest extends TestCase
{
    use RefreshDatabase;

    public function loginAs($isAdmin = false): ProductsTest
    {
        $user = $this->getUser(isAdmin: $isAdmin);
        return $this->actingAs($user);
    }

    private function getUser($isAdmin): User
    {
        if ($isAdmin) {
            return User::factory()->admin()->create();
        }

        return User::factory()->create();
    }

    public function test_homepage_contains_empty_table(): void
    {
        $response = $this->loginAs()->get('/products');

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

        $response = $this->loginAs()->get('/products');
        $response->assertStatus(200);

        $response->assertViewHas('products', function (LengthAwarePaginator $collection) use ($product) {
            return $collection->contains($product);
        });
    }

    public function test_paginated_products_table_doesnt_contain_11th_record()
    {
        Category::factory(3)->create();
        $products = Product::factory(11)->create();

        $lastProduct = $products->last();

        $response = $this->loginAs()->get('/products');

        $response->assertStatus(200);
        $response->assertViewHas('products', function (LengthAwarePaginator $collection) use ($lastProduct) {
            return $collection->doesntContain($lastProduct);
        });
    }

    public function test_admin_can_see_products_create_button()
    {
        $response = $this->loginAs(isAdmin: true)->get('/products');

        $response->assertStatus(200);
        $response->assertSee('Add new product');
    }

    public function test_non_admin_cannot_see_products_create_button()
    {
        $response = $this->loginAs()->get('/products');

        $response->assertStatus(200);
        $response->assertDontSee('Add new product');
    }

    public function test_admin_can_access_product_create_page()
    {
        $response = $this->loginAs(isAdmin: true)->get('/products/create');

        $response->assertStatus(200);
    }

    public function test_non_admin_cannot_access_product_create_page()
    {
        $response = $this->loginAs()->get('/products/create');

        $response->assertStatus(403);
    }
}
