@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-4">Blog Posts</h1>
        
        <!-- Search Form -->
        <div class="mb-6">
            <form method="GET" action="{{ route('blog.index') }}" class="flex gap-2">
                <div class="flex-1">
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="Search posts..." 
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>
                <input type="hidden" name="category" value="{{ request('category') }}">
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    Search
                </button>
                @if(request('search') || request('category'))
                    <a href="{{ route('blog.index') }}" 
                       class="px-6 py-2 bg-gray-500 text-white font-medium rounded-md hover:bg-gray-600 focus:outline-none">
                        Clear
                    </a>
                @endif
            </form>
        </div>
        
        <!-- Category Filter -->
        <div class="flex flex-wrap gap-2 mb-6">
            <a href="{{ route('blog.index') }}" 
               class="px-4 py-2 text-sm font-medium rounded-md {{ !request('category') ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                All Categories
            </a>
            @foreach($categories as $category)
                <a href="{{ route('blog.index', ['category' => $category->id]) }}" 
                   class="px-4 py-2 text-sm font-medium rounded-md {{ request('category') == $category->id ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                    {{ $category->name }}
                </a>
            @endforeach
        </div>
    </div>

    <!-- Posts Grid -->
    @if($posts->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            @foreach($posts as $post)
                <article class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300">
                    @if($post->image_url)
                        <img src="{{ $post->image_url }}" alt="{{ $post->name }}" class="w-full h-48 object-cover">
                    @else
                        <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                            <span class="text-gray-400">No Image</span>
                        </div>
                    @endif
                    
                    <div class="p-6">
                        <div class="flex items-center text-sm text-gray-500 mb-2">
                            <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs font-medium">
                                {{ $post->category->name }}
                            </span>
                            <span class="ml-2">{{ $post->created_at->format('M d, Y') }}</span>
                        </div>
                        
                        <h2 class="text-xl font-semibold text-gray-900 mb-2">
                            <a href="{{ route('blog.show', $post->slug) }}" class="hover:text-blue-600">
                                {{ $post->name }}
                            </a>
                        </h2>
                        
                        <p class="text-gray-600 mb-4 line-clamp-3">
                            {{ Str::limit(strip_tags($post->description), 120) }}
                        </p>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">By {{ $post->user->name }}</span>
                            <a href="{{ route('blog.show', $post->slug) }}" 
                               class="text-blue-600 hover:text-blue-800 font-medium text-sm">
                                Read More →
                            </a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="flex justify-center">
            {{ $posts->appends(request()->query())->links() }}
        </div>
    @else
        <div class="text-center py-12">
            <div class="max-w-md mx-auto">
                <div class="text-gray-400 mb-4">
                    <svg class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No posts found</h3>
                <p class="text-gray-500">There are no published blog posts yet.</p>
            </div>
        </div>
    @endif
</div>
@endsection 