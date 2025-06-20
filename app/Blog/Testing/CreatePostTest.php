<?php

namespace App\Blog\Testing;

use Tests\TestCase;
use App\Blog\UseCases\CreatePost;
use App\Blog\Entities\Post;
use App\Blog\Entities\Category;
use App\Models\User;
use App\Blog\UseCases\Exceptions\PostCreationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;

class CreatePostTest extends TestCase
{
    use RefreshDatabase;

    private CreatePost $createPost;
    private User $user;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->createPost = new CreatePost;

        $this->user = User::factory()->create();
        $this->category = Category::create([
            'name' => 'Test Category',
            'slug' => 'test-category'
        ]);

        Auth::login($this->user);
    }

    public function it_can_create_a_post_with_valid_data()
    {
        $data = [
            'name' => 'Test Post',
            'description' => 'This is a test post description.',
            'category_id' => $this->category->id,
            'is_published' => true
        ];

        $post = $this->createPost->execute($data);

        $this->assertInstanceOf(Post::class, $post);
        $this->assertEquals('Test Post', $post->name);
        $this->assertEquals('test-post', $post->slug);
        $this->assertEquals('This is a test post description.', $post->description);
        $this->assertEquals($this->category->id, $post->category_id);
        $this->assertEquals($this->user->id, $post->user_id);
        $this->assertTrue($post->is_published);
        $this->assertDatabaseHas('posts', [
            'name' => 'Test Post',
            'slug' => 'test-post'
        ]);
    }

    public function it_creates_a_draft_post_by_default()
    {
        $data = [
            'name' => 'Draft Post',
            'description' => 'This is a draft post.',
            'category_id' => $this->category->id
        ];

        $post = $this->createPost->execute($data);

        $this->assertFalse($post->is_published);
    }

    public function it_generates_slug_automatically()
    {
        $data = [
            'name' => 'A Post With Spaces and Special Characters!',
            'description' => 'Test description.',
            'category_id' => $this->category->id
        ];

        $post = $this->createPost->execute($data);

        $this->assertEquals('a-post-with-spaces-and-special-characters', $post->slug);
    }

    public function it_throws_exception_on_creation_failure()
    {
        $this->expectException(PostCreationException::class);

        $data = [
            'name' => 'Test Post',
            'description' => 'Test description.',
            'category_id' => 99999
        ];

        $this->createPost->execute($data);
    }
}
