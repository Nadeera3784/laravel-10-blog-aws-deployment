<?php

namespace App\Blog\IO\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Blog\UseCases\CreateCategory;
use App\Blog\UseCases\UpdateCategory;
use App\Blog\Entities\Category;
use App\Blog\UseCases\Exceptions\CategoryCreationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    private $createCategory;
    private $updateCategory;

    public function __construct(CreateCategory $createCategory, UpdateCategory $updateCategory)
    {
        $this->middleware('auth');
        $this->createCategory = $createCategory;
        $this->updateCategory = $updateCategory;
    }

    public function index()
    {
        $categories = Category::withCount('posts')->orderBy('name')->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:categories,name',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $category = $this->createCategory->execute($request->all());
            return redirect()->route('admin.categories.index')
                ->with('success', 'Category created successfully!');
        } catch (CategoryCreationException $e) {
            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $updatedCategory = $this->updateCategory->execute($category, $request->all());
            return redirect()->route('admin.categories.index')
                ->with('success', 'Category updated successfully!');
        } catch (CategoryCreationException $e) {
            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Category $category)
    {
        if ($category->posts()->count() > 0) {
            return redirect()->route('admin.categories.index')
                ->with('error', 'Cannot delete category with posts. Please reassign or delete posts first.');
        }

        $category->delete();
        return redirect()->route('admin.categories.index')
            ->with('success', 'Category deleted successfully!');
    }
}
