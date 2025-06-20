<?php

namespace App\Blog\Testing;

use Tests\TestCase;
use App\Blog\Entities\Category;
use App\Blog\Entities\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->category = Category::create([
            'name' => 'Technology',
            'slug' => 'technology'
        ]);
    }

    public function it_can_get_post_count()
    {
        Post::create([
            'name' => 'Post 1',
            'slug' => 'post-1',
            'description' => 'Description 1',
            'category_id' => $this->category->id,
            'user_id' => $this->user->id,
            'is_published' => true
        ]);

        Post::create([
            'name' => 'Post 2',
            'slug' => 'post-2',
            'description' => 'Description 2',
            'category_id' => $this->category->id,
            'user_id' => $this->user->id,
            'is_published' => false
        ]);

        $this->assertEquals(2, $this->category->getPostCount());
    }

    public function it_returns_zero_when_no_posts()
    {
        $this->assertEquals(0, $this->category->getPostCount());
    }

    public function it_can_check_if_has_published_posts()
    {
        Post::create([
            'name' => 'Published Post',
            'slug' => 'published-post',
            'description' => 'Description',
            'category_id' => $this->category->id,
            'user_id' => $this->user->id,
            'is_published' => true
        ]);

        $this->assertTrue($this->category->hasPublishedPosts());
    }

    public function it_returns_false_when_no_published_posts()
    {
        Post::create([
            'name' => 'Draft Post',
            'slug' => 'draft-post',
            'description' => 'Description',
            'category_id' => $this->category->id,
            'user_id' => $this->user->id,
            'is_published' => false
        ]);

        $this->assertFalse($this->category->hasPublishedPosts());
    }

    public function it_returns_false_when_no_posts_at_all()
    {
        $this->assertFalse($this->category->hasPublishedPosts());
    }

    public function it_has_posts_relationship()
    {
        Post::create([
            'name' => 'Related Post',
            'slug' => 'related-post',
            'description' => 'Description',
            'category_id' => $this->category->id,
            'user_id' => $this->user->id,
            'is_published' => true
        ]);

        $this->assertCount(1, $this->category->posts);
        $this->assertEquals('Related Post', $this->category->posts->first()->name);
    }

    public function it_can_scope_categories_with_posts()
    {
        $emptyCategory = Category::create([
            'name' => 'Empty Category',
            'slug' => 'empty-category'
        ]);

        Post::create([
            'name' => 'Post with Category',
            'slug' => 'post-with-category',
            'description' => 'Description',
            'category_id' => $this->category->id,
            'user_id' => $this->user->id,
            'is_published' => true
        ]);

        $categoriesWithPosts = Category::withPosts()->get();

        $this->assertCount(1, $categoriesWithPosts);
        $this->assertEquals($this->category->id, $categoriesWithPosts->first()->id);
    }

    public function it_can_scope_categories_with_published_posts()
    {
        $categoryWithDraft = Category::create([
            'name' => 'Draft Category',
            'slug' => 'draft-category'
        ]);

        Post::create([
            'name' => 'Published Post',
            'slug' => 'published-post',
            'description' => 'Description',
            'category_id' => $this->category->id,
            'user_id' => $this->user->id,
            'is_published' => true
        ]);

        Post::create([
            'name' => 'Draft Post',
            'slug' => 'draft-post',
            'description' => 'Description',
            'category_id' => $categoryWithDraft->id,
            'user_id' => $this->user->id,
            'is_published' => false
        ]);

        $categoriesWithPublishedPosts = Category::withPublishedPosts()->get();

        $this->assertCount(1, $categoriesWithPublishedPosts);
        $this->assertEquals($this->category->id, $categoriesWithPublishedPosts->first()->id);
    }

    public function it_has_correct_fillable_attributes()
    {
        $category = new Category([
            'name' => 'Test Category',
            'slug' => 'test-category'
        ]);

        $this->assertEquals('Test Category', $category->name);
        $this->assertEquals('test-category', $category->slug);
    }

    public function it_can_be_created_with_factory()
    {
        $category = Category::create([
            'name' => 'Factory Category',
            'slug' => 'factory-category'
        ]);

        $this->assertInstanceOf(Category::class, $category);
        $this->assertDatabaseHas('categories', [
            'name' => 'Factory Category',
            'slug' => 'factory-category'
        ]);
    }
}
