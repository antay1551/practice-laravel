<?php

namespace Tests\Feature\Api;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_categories_list()
    {
        $category = Category::factory()->create();
        $response = $this
            ->loginAs(isAdmin: true)
            ->getJson('/api/categories');

        $response->assertJsonStructure([
            'data' => [
                '*' => ['id', 'name']
            ]
        ]);

        $response->assertJsonFragment([
            'id' => $category->id,
            'name' => $category->name,
        ]);
    }

    public function test_category_store_successful()
    {
        $category = [
            'name' => 'Category 1',
        ];
        $response = $this
            ->loginAs(isAdmin: true)
            ->postJson('/api/categories', $category);

        $response->assertCreated();
        $response->assertJsonStructure([
            'data' => [
                'id', 'name'
            ]
        ]);

        $response->assertJsonFragment([
            'name' => $category['name'],
        ]);
    }

    public function test_category_invalid_store_returns_error()
    {
        $category = [
            'name' => '',
        ];

        $response = $this
            ->loginAs(isAdmin: true)
            ->postJson('/api/categories', $category);

        $response->assertUnprocessable();
    }
}
