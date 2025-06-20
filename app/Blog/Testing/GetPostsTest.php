<?php

namespace App\Blog\Testing;

use Tests\TestCase;
use App\Blog\UseCases\GetPosts;
use App\Blog\Entities\Post;
use App\Blog\Entities\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GetPostsTest extends TestCase
{
    use RefreshDatabase;

    private GetPosts $getPosts;
    private User $user;
    private Category $category1;
    private Category $category2;

    protected function setUp(): void
    {
        parent::setUp();
        $this->getPosts = new GetPosts;

        $this->user = User::factory()->create();
        $this->category1 = Category::create(['name' => 'Category 1', 'slug' => 'category-1']);
        $this->category2 = Category::create(['name' => 'Category 2', 'slug' => 'category-2']);
    }

    public function it_can_get_all_posts()
    {
        Post::create([
            'name' => 'Post 1',
            'slug' => 'post-1',
            'description' => 'Description 1',
            'category_id' => $this->category1->id,
            'user_id' => $this->user->id,
            'is_published' => true
        ]);

        Post::create([
            'name' => 'Post 2',
            'slug' => 'post-2',
            'description' => 'Description 2',
            'category_id' => $this->category2->id,
            'user_id' => $this->user->id,
            'is_published' => false
        ]);

        $posts = $this->getPosts->execute();

        $this->assertEquals(2, $posts->total());
    }

    public function it_can_filter_published_posts_only()
    {
        Post::create([
            'name' => 'Published Post',
            'slug' => 'published-post',
            'description' => 'Published description',
            'category_id' => $this->category1->id,
            'user_id' => $this->user->id,
            'is_published' => true
        ]);

        Post::create([
            'name' => 'Draft Post',
            'slug' => 'draft-post',
            'description' => 'Draft description',
            'category_id' => $this->category1->id,
            'user_id' => $this->user->id,
            'is_published' => false
        ]);

        $posts = $this->getPosts->execute(null, true);

        $this->assertEquals(1, $posts->total());
        $this->assertEquals('Published Post', $posts->first()->name);
    }

    public function it_can_filter_posts_by_category()
    {
        Post::create([
            'name' => 'Post in Category 1',
            'slug' => 'post-category-1',
            'description' => 'Description',
            'category_id' => $this->category1->id,
            'user_id' => $this->user->id,
            'is_published' => true
        ]);

        Post::create([
            'name' => 'Post in Category 2',
            'slug' => 'post-category-2',
            'description' => 'Description',
            'category_id' => $this->category2->id,
            'user_id' => $this->user->id,
            'is_published' => true
        ]);

        $posts = $this->getPosts->execute($this->category1->id, true);

        $this->assertEquals(1, $posts->total());
        $this->assertEquals('Post in Category 1', $posts->first()->name);
    }

    public function it_orders_posts_by_created_date_descending()
    {
        $olderPost = Post::create([
            'name' => 'Older Post',
            'slug' => 'older-post',
            'description' => 'Description',
            'category_id' => $this->category1->id,
            'user_id' => $this->user->id,
            'is_published' => true
        ]);

        sleep(1);

        $newerPost = Post::create([
            'name' => 'Newer Post',
            'slug' => 'newer-post',
            'description' => 'Description',
            'category_id' => $this->category1->id,
            'user_id' => $this->user->id,
            'is_published' => true
        ]);

        $posts = $this->getPosts->execute();

        $this->assertEquals('Newer Post', $posts->first()->name);
        $this->assertEquals('Older Post', $posts->last()->name);
    }
}
