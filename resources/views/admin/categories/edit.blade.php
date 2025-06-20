@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Edit Category</h1>
        <p class="mt-2 text-gray-600">Update the category details</p>
    </div>

    <div class="bg-white shadow rounded-lg">
        <form method="POST" action="{{ route('admin.categories.update', $category->id) }}">
            @csrf
            @method('PUT')
            
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Category Details</h3>
            </div>
            
            <div class="p-6">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Category Name</label>
                    <input type="text" 
                           name="name" 
                           id="name" 
                           value="{{ old('name', $category->name) }}"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-300 @enderror"
                           placeholder="e.g., Technology, Travel, Food"
                           required>
                    @error('name')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-2 text-sm text-gray-500">The category name will be used to organize your blog posts.</p>
                </div>
                
                <!-- Current Slug Display -->
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700">Current Slug</label>
                    <div class="mt-1 p-3 bg-gray-50 border border-gray-200 rounded-md">
                        <span class="text-sm text-gray-600">{{ $category->slug }}</span>
                    </div>
                    <p class="mt-2 text-sm text-gray-500">The slug will be automatically updated based on the category name.</p>
                </div>
            </div>
            
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-between">
                <a href="{{ route('admin.categories.index') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" 
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                    Update Category
                </button>
            </div>
        </form>
    </div>
</div>
@endsection 