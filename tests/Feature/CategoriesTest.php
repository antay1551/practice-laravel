<?php

namespace Tests\Feature;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoriesTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_category_successful()
    {
        $category = [
            'name' => 'Category 123',
        ];
        $response = $this
            ->loginAs(isAdmin: true)
            ->post('/categories', $category);

        $response->assertStatus(302);
        $response->assertRedirect('categories');

        $this->assertDatabaseHas('categories', $category);

        $lastProduct = Category::latest()->first();
        $this->assertEquals($category['name'], $lastProduct->name);
    }

    public function test_category_edit_contains_correct_values()
    {
        $category = Category::factory()->create();

        $response = $this
            ->loginAs(isAdmin: true)
            ->get('categories/' . $category->id . '/edit');

        $response->assertOk();
        $response->assertSee('value="' . $category->name . '"', false);
        $response->assertViewHas('category', $category);
    }

    public function test_category_edit_validation_redirect_back_with_error()
    {
        $category = Category::factory()->create();

        $response = $this
            ->loginAs(isAdmin: true)
            ->put('categories/' . $category->id, [
            'name' => ''
        ]);

        $response->assertStatus(302);
        $response->assertInvalid(['name']);
    }

    public function test_category_show_in_correct_order()
    {
        [$category1, $category2] = Category::factory(2)->create();

        $response = $this
            ->loginAs(isAdmin: true)
            ->get('/categories');

        $response->assertOk();
        $response->assertSeeInOrder([$category1->name, $category2->name]);
    }

    public function test_category_delete_successful()
    {
        $category = Category::factory()->create();

        $response = $this
            ->loginAs(isAdmin: true)
            ->delete('categories/' . $category->id);

        $response->assertStatus(302);
        $response->assertRedirect('categories');

        $this->assertDatabaseMissing('categories', $category->toArray());
        $this->assertDatabaseCount('categories', 0);
    }
}
