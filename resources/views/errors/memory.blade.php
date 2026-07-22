@extends('layouts.app')

@section('title', 'Memory Error')

@section('content')
<div class="flex items-center justify-center min-h-screen">
    <div class="text-center">
        <div class="text-red-500 text-6xl mb-4">⚠️</div>
        <h1 class="text-2xl font-bold mb-2">Memory Limit Exceeded</h1>
        <p class="text-gray-600 mb-4">The application ran out of memory. Please try again with fewer items.</p>
        <a href="{{ url()->previous() }}" class="bg-blue-500 text-white px-4 py-2 rounded">Go Back</a>
    </div>
</div>
@endsection