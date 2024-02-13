<?php

namespace Tests\Feature\Api;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function loginAs(?bool $isAdmin = false): self
    {
        $user = $this->getUser(isAdmin: $isAdmin);
        return $this->actingAs($user);
    }

    private function getUser(bool $isAdmin): User
    {
        if ($isAdmin) {
            return User::factory()->admin()->create();
        }

        return User::factory()->create();
    }

    public function test_categories_list()
    {
        $category = Category::factory()->create();
        $response = $this->loginAs(isAdmin: true)->getJson('/api/categories');

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
        $response = $this->loginAs(isAdmin: true)->postJson('/api/categories', $category);

        $response->assertStatus(201);
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

        $response = $this->loginAs(isAdmin: true)->postJson('/api/categories', $category);
        $response->assertStatus(422);
    }
}
