<?php

namespace App\Blog\IO\Http;

use App\Http\Controllers\Controller;
use App\Blog\UseCases\GetPosts;
use App\Blog\UseCases\GetPost;
use App\Blog\Entities\Category;
use App\Blog\UseCases\Exceptions\PostNotFoundException;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    private $getPosts;
    private $getPost;

    public function __construct(GetPosts $getPosts, GetPost $getPost)
    {
        $this->getPosts = $getPosts;
        $this->getPost = $getPost;
    }

    public function index(Request $request)
    {
        $categoryId = $request->query('category');
        $posts = $this->getPosts->execute($categoryId, true);
        $categories = Category::withPublishedPosts()->get();

        return view('blog.index', compact('posts', 'categories'));
    }

    public function show(string $slug)
    {
        try {
            $post = $this->getPost->execute($slug);

            if (!$post->isPublished()) {
                abort(404);
            }

            return view('blog.show', compact('post'));
        } catch (PostNotFoundException $e) {
            abort(404);
        }
    }
}
