@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <a href="{{ route('admin.posts.index') }}" class="text-blue-600 hover:text-blue-800 mb-2 inline-block">
                    ← Back to Posts
                </a>
                <h1 class="text-3xl font-bold text-gray-900">{{ $post->name }}</h1>
                <div class="flex items-center space-x-4 mt-2">
                    @if($post->is_published)
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                            Published
                        </span>
                    @else
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                            Draft
                        </span>
                    @endif
                    <span class="text-sm text-gray-500">{{ $post->created_at->format('M d, Y') }}</span>
                </div>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('admin.posts.edit', $post->id) }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    Edit Post
                </a>
                @if($post->is_published)
                    <a href="{{ route('blog.show', $post->slug) }}" 
                       target="_blank"
                       class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                        View Live
                    </a>
                @endif
            </div>
        </div>
    </div>

    <div class="bg-white shadow rounded-lg overflow-hidden">
        @if($post->image)
            <div class="w-full h-64 md:h-80">
                <img src="{{ $post->getImageUrl() }}" alt="{{ $post->name }}" class="w-full h-full object-cover">
            </div>
        @endif

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Category</h3>
                    <p class="mt-1 text-sm text-gray-900">{{ $post->category->name }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Author</h3>
                    <p class="mt-1 text-sm text-gray-900">{{ $post->user->name }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Slug</h3>
                    <p class="mt-1 text-sm text-gray-900">{{ $post->slug }}</p>
                </div>
            </div>

            <div class="border-t pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Content</h3>
                <div class="prose max-w-none text-gray-700">
                    {!! nl2br(e($post->description)) !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 