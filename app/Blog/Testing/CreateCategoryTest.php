<?php

namespace App\Blog\Testing;

use Tests\TestCase;
use App\Blog\UseCases\CreateCategory;
use App\Blog\Entities\Category;
use App\Blog\UseCases\Exceptions\CategoryCreationException;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateCategoryTest extends TestCase
{
    use RefreshDatabase;

    private CreateCategory $createCategory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->createCategory = new CreateCategory;
    }

    public function it_can_create_a_category_with_valid_data()
    {
        $data = [
            'name' => 'Technology'
        ];

        $category = $this->createCategory->execute($data);

        $this->assertInstanceOf(Category::class, $category);
        $this->assertEquals('Technology', $category->name);
        $this->assertEquals('technology', $category->slug);
        $this->assertDatabaseHas('categories', [
            'name' => 'Technology',
            'slug' => 'technology'
        ]);
    }

    public function it_generates_slug_automatically_from_name()
    {
        $data = [
            'name' => 'Web Development & Design'
        ];

        $category = $this->createCategory->execute($data);

        $this->assertEquals('web-development-design', $category->slug);
    }

    public function it_handles_special_characters_in_name()
    {
        $data = [
            'name' => 'Sports & Entertainment!'
        ];

        $category = $this->createCategory->execute($data);

        $this->assertEquals('Sports & Entertainment!', $category->name);
        $this->assertEquals('sports-entertainment', $category->slug);
    }

    public function it_handles_long_category_names()
    {
        $data = [
            'name' => 'This is a very long category name that should still work properly'
        ];

        $category = $this->createCategory->execute($data);

        $this->assertEquals('This is a very long category name that should still work properly', $category->name);
        $this->assertEquals('this-is-a-very-long-category-name-that-should-still-work-properly', $category->slug);
    }

    public function it_throws_exception_when_category_creation_fails()
    {
        $this->expectException(CategoryCreationException::class);
        $this->expectExceptionMessage('Failed to create category:');

        $data = [
            'name' => null
        ];

        $this->createCategory->execute($data);
    }
}
