<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoriesTest extends TestCase
{
    use RefreshDatabase;

    public function loginAs($isAdmin = false): CategoriesTest
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

    public function test_create_category_successful()
    {
        $category = [
            'name' => 'Category 123',
        ];
        $response = $this->loginAs(isAdmin: true)->post('/categories', $category);

        $response->assertStatus(302);
        $response->assertRedirect('categories');

        $this->assertDatabaseHas('categories', $category);

        $lastProduct = Category::latest()->first();
        $this->assertEquals($category['name'], $lastProduct->name);
    }

    public function test_category_edit_contains_correct_values()
    {
        $category = Category::factory()->create();

        $response = $this->loginAs(isAdmin: true)->get('categories/' . $category->id . '/edit');

        $response->assertStatus(200);
        $response->assertSee('value="' . $category->name . '"', false);
        $response->assertViewHas('category', $category);
    }

    public function test_category_edit_validation_redirect_back_with_error()
    {
        $category = Category::factory()->create();

        $response = $this->loginAs(isAdmin: true)->put('categories/' . $category->id, [
            'name' => ''
        ]);

        $response->assertStatus(302);
        $response->assertInvalid(['name']);
    }

    public function test_category_delete_successful()
    {
        $category = Category::factory()->create();

        $response = $this->loginAs(isAdmin: true)->delete('categories/' . $category->id);

        $response->assertStatus(302);
        $response->assertRedirect('categories');

        $this->assertDatabaseMissing('categories', $category->toArray());
        $this->assertDatabaseCount('categories', 0);
    }
}
