<?php

namespace App\Blog\IO\Http;

use App\Http\Controllers\Controller;
use App\Blog\UseCases\GetPosts;
use App\Blog\UseCases\GetPost;
use App\Blog\UseCases\CreatePost;
use App\Blog\UseCases\UpdatePost;
use App\Blog\UseCases\DeletePost;
use App\Blog\Entities\Category;
use App\Blog\UseCases\Exceptions\PostNotFoundException;
use App\Blog\UseCases\Exceptions\PostCreationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    private $getPosts;
    private $getPost;
    private $createPost;
    private $updatePost;
    private $deletePost;

    public function __construct(
        GetPosts $getPosts,
        GetPost $getPost,
        CreatePost $createPost,
        UpdatePost $updatePost,
        DeletePost $deletePost
    ) {
        $this->middleware("auth");
        $this->getPosts = $getPosts;
        $this->getPost = $getPost;
        $this->createPost = $createPost;
        $this->updatePost = $updatePost;
        $this->deletePost = $deletePost;
    }

    public function index()
    {
        $posts = $this->getPosts->execute();
        return view("admin.posts.index", compact("posts"));
    }

    public function create()
    {
        $categories = Category::all();
        return view("admin.posts.create", compact("categories"));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "name" => "required|string|max:255",
            "description" => "required|string",
            "category_id" => "required|exists:categories,id",
            "image" => "nullable|image|mimes:jpeg,png,jpg,gif|max:2048",
            "is_published" => "nullable|boolean"
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $data = $request->all();
            $data["is_published"] = $request->has("is_published") ? true : false;

            $post = $this->createPost->execute($data);
            return redirect()->route("admin.posts.show", $post->id)
                ->with("success", "Post created successfully!");
        } catch (PostCreationException $e) {
            return redirect()->back()
                ->with("error", $e->getMessage())
                ->withInput();
        }
    }

    public function show(int $id)
    {
        try {
            $post = $this->getPost->executeById($id);
            return view("admin.posts.show", compact("post"));
        } catch (PostNotFoundException $e) {
            abort(404);
        }
    }

    public function edit(int $id)
    {
        try {
            $post = $this->getPost->executeById($id);
            $categories = Category::all();
            return view("admin.posts.edit", compact("post", "categories"));
        } catch (PostNotFoundException $e) {
            abort(404);
        }
    }

    public function update(Request $request, int $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_published' => 'nullable|boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $data = $request->all();
            $data['is_published'] = $request->has('is_published') ? true : false;

            $post = $this->updatePost->execute($id, $data);
            return redirect()->route('admin.posts.show', $post->id)
                ->with('success', 'Post updated successfully!');
        } catch (PostNotFoundException $e) {
            abort(404);
        }
    }

    public function destroy(int $id)
    {
        try {
            $this->deletePost->execute($id);
            return redirect()->route("admin.posts.index")
                ->with("success", "Post deleted successfully!");
        } catch (PostNotFoundException $e) {
            abort(404);
        }
    }
}
