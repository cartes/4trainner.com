<?php

namespace Tests\Unit\Models;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_category_can_be_created(): void
    {
        $category = Category::create([
            'name' => 'Fitness',
            'slug' => 'fitness',
            'order' => 1,
        ]);

        $this->assertDatabaseHas('categories', [
            'slug' => 'fitness',
        ]);
        $this->assertEquals('Fitness', $category->name);
    }

    public function test_category_hierarchy(): void
    {
        $parent = Category::create([
            'name' => 'Workouts',
            'slug' => 'workouts',
            'order' => 1,
        ]);

        $child = Category::create([
            'name' => 'Cardio',
            'slug' => 'cardio',
            'order' => 1,
            'parent_id' => $parent->id,
        ]);

        // Refresh models
        $parent->refresh();
        $child->refresh();

        // Assert parent relationship
        $this->assertEquals($parent->id, $child->parent->id);
        $this->assertEquals('Workouts', $child->parent->name);

        // Assert children relationship
        $this->assertTrue($parent->children->contains($child));
        $this->assertEquals(1, $parent->children->count());

        // Assert subcategories alias
        $this->assertTrue($parent->subcategories->contains($child));
    }
}
