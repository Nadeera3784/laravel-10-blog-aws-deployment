@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Back to Blog Link -->
    <div class="mb-6">
        <a href="{{ route('blog.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Blog
        </a>
    </div>

    <!-- Post Content -->
    <article class="bg-white rounded-lg shadow-lg overflow-hidden">
        @if($post->image)
            <div class="w-full h-64 md:h-80">
                <img src="{{ $post->getImageUrl() }}" alt="{{ $post->name }}" class="w-full h-full object-cover">
            </div>
        @endif

        <div class="p-8">
            <!-- Post Meta -->
            <div class="flex flex-wrap items-center gap-4 text-sm text-gray-500 mb-6">
                <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full font-medium">
                    {{ $post->category->name }}
                </span>
                <span class="flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    {{ $post->created_at->format('F j, Y') }}
                </span>
                <span class="flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    By {{ $post->user->name }}
                </span>
            </div>

            <!-- Post Title -->
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6 leading-tight">
                {{ $post->name }}
            </h1>

            <!-- Post Content -->
            <div class="prose prose-lg max-w-none text-gray-700 leading-relaxed">
                {!! nl2br(e($post->description)) !!}
            </div>
        </div>
    </article>

    <!-- Related Posts (Optional - you can implement this later) -->
    <div class="mt-12">
        <h3 class="text-2xl font-bold text-gray-900 mb-6">More Posts</h3>
        <div class="text-center py-8">
            <a href="{{ route('blog.index') }}" 
               class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 transition duration-300">
                Browse All Posts
                <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                </svg>
            </a>
        </div>
    </div>
</div>
@endsection 