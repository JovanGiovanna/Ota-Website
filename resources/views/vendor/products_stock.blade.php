@extends('layouts.vendor')

@section('title', 'Stock Management (Moved)')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto text-center">
        <h1 class="text-2xl font-semibold text-gray-900 mb-4">Stock Management Moved</h1>
        <p class="text-gray-600 mb-4">Stock management was merged into the main <strong>My Products</strong> page.</p>
        <a href="{{ route('vendor.products') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white rounded-md hover:bg-emerald-700">Go to My Products</a>
    </div>
</div>
@endsection
