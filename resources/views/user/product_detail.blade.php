@extends('layouts.user')

@section('title', $product->name . ' - Product Detail')

@section('welcome')
Product Details
@endsection

@section('content')
<!-- Product Hero Section -->
<div class="bg-gradient-to-r from-green-600 via-emerald-600 to-teal-800 rounded-2xl p-8 mb-8 text-white shadow-2xl">
    <div class="max-w-6xl mx-auto">
        <div class="flex flex-col lg:flex-row items-start gap-8">
            <!-- Product Image -->
            <div class="lg:w-1/2">
                @if($product->image)
                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-96 object-cover rounded-2xl shadow-2xl">
                @else
                    <div class="w-full h-96 bg-gradient-to-r from-green-400 to-teal-500 rounded-2xl flex items-center justify-center shadow-2xl">
                        <i class="fas fa-shopping-cart text-white text-6xl"></i>
                    </div>
                @endif
            </div>

            <!-- Product Info -->
            <div class="lg:w-1/2">
                <div class="flex items-center space-x-2 mb-4">
                    <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-white/20 text-white">Product</span>
                    <div class="flex items-center space-x-1">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= floor($product->averageRating()))
                                <i class="fas fa-star text-yellow-300 text-sm"></i>
                            @elseif($i - 0.5 <= $product->averageRating())
                                <i class="fas fa-star-half-alt text-yellow-300 text-sm"></i>
                            @else
                                <i class="far fa-star text-yellow-300 text-sm"></i>
                            @endif
                        @endfor
                        <span class="text-sm ml-1">{{ number_format($product->averageRating(), 1) }}</span>
                        <span class="text-sm text-green-100">({{ $product->reviews->count() }} reviews)</span>
                    </div>
                </div>

                <h1 class="text-4xl font-bold mb-4">{{ $product->name }}</h1>
                <p class="text-green-100 text-lg mb-6">{{ Str::limit($product->description, 200) }}</p>

                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-6 mb-6">
                    <div class="text-3xl font-bold text-white mb-2">Rp {{ number_format($product->price, 0, ',', '.') }}</div>
                    <div class="text-green-100">per unit</div>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 text-center">
                        <div class="text-2xl font-bold text-white">{{ $product->jumlah }}</div>
                        <div class="text-green-100 text-sm">Available Stock</div>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 text-center">
                        <div class="text-2xl font-bold text-white">{{ $product->pax }}</div>
                        <div class="text-green-100 text-sm">Pax</div>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-4">
                    @if(request('from') == 'form_booker')
                        <a href="{{ route('user.form_booker', ['product' => $product->id]) }}" class="flex-1 bg-white text-green-600 px-8 py-4 rounded-xl font-semibold hover:bg-green-50 transition-all duration-200 text-center shadow-lg">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Return to Form Booking
                        </a>
                    @else
                        <a href="{{ route('user.form_booker', ['product' => $product->id]) }}" class="flex-1 bg-white text-green-600 px-8 py-4 rounded-xl font-semibold hover:bg-green-50 transition-all duration-200 text-center shadow-lg">
                            <i class="fas fa-plus-circle mr-2"></i>
                            Book Now
                        </a>
                    @endif
                    <button class="flex-1 bg-white/20 backdrop-blur-sm text-white px-8 py-4 rounded-xl font-semibold hover:bg-white/30 transition-all duration-200 border border-white/30">
                        <i class="fas fa-heart mr-2"></i>
                        Save to Wishlist
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Product Details -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
    <!-- Main Content -->
    <div class="lg:col-span-2 space-y-8">
        <!-- Description -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-info-circle text-green-600"></i>
                </div>
                About This Product
            </h2>
            <p class="text-gray-600 leading-relaxed">{{ $product->description }}</p>
        </div>

        <!-- Product Specifications -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-list text-blue-600"></i>
                </div>
                Product Specifications
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="flex justify-between items-center py-3 border-b border-gray-100">
                    <span class="text-gray-600">Category</span>
                    <span class="font-semibold text-gray-800">{{ $product->category->categories ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between items-center py-3 border-b border-gray-100">
                    <span class="text-gray-600">Stock Available</span>
                    <span class="font-semibold text-gray-800">{{ $product->jumlah }}</span>
                </div>
                <div class="flex justify-between items-center py-3 border-b border-gray-100">
                    <span class="text-gray-600">Max Adults</span>
                    <span class="font-semibold text-gray-800">{{ $product->max_adults }}</span>
                </div>
                <div class="flex justify-between items-center py-3 border-b border-gray-100">
                    <span class="text-gray-600">Max Children</span>
                    <span class="font-semibold text-gray-800">{{ $product->max_children }}</span>
                </div>
                <div class="flex justify-between items-center py-3 border-b border-gray-100">
                    <span class="text-gray-600">Status</span>
                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $product->status == 'available' ? 'bg-green-100 text-green-800' : ($product->status == 'unavailable' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                        {{ ucfirst($product->status) }}
                    </span>
                </div>
                @if($product->pax)
                <div class="flex justify-between items-center py-3 border-b border-gray-100">
                    <span class="text-gray-600">Capacity (Pax)</span>
                    <span class="font-semibold text-gray-800">{{ $product->pax }}</span>
                </div>
                @endif
            </div>
        </div>

        <!-- Reviews -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-star text-yellow-600"></i>
                </div>
                Reviews & Ratings
            </h2>

            @if($product->reviews->count() > 0)
                <!-- Overall Rating -->
                <div class="bg-gray-50 rounded-xl p-6 mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="text-center">
                            <div class="text-3xl font-bold text-gray-800">{{ number_format($product->averageRating(), 1) }}</div>
                            <div class="flex items-center justify-center mb-1">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star {{ $i <= round($product->averageRating()) ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                @endfor
                            </div>
                            <div class="text-sm text-gray-600">{{ $product->reviews->count() }} reviews</div>
                        </div>
                    </div>
                </div>

                <!-- Individual Reviews -->
                <div class="space-y-6">
                    @foreach($product->reviews as $review)
                        <div class="border-b border-gray-100 pb-6 last:border-b-0 last:pb-0">
                            <div class="flex items-start space-x-4">
                                <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-gray-500"></i>
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex items-center space-x-2">
                                            <span class="font-semibold text-gray-800">{{ $review->user->name ?? 'Anonymous' }}</span>
                                            <div class="flex items-center">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="fas fa-star {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                                @endfor
                                            </div>
                                        </div>
                                        <span class="text-sm text-gray-500">{{ $review->created_at->format('M d, Y') }}</span>
                                    </div>
                                    @if($review->comment)
                                        <p class="text-gray-600 leading-relaxed">{{ $review->comment }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-star-half-alt text-gray-300 text-4xl mb-4"></i>
                    <p class="text-gray-500">No reviews yet for this product</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Sidebar -->
    <div class="space-y-8">
        <!-- Vendor Info -->
        @if($product->vendor && $product->vendor->vendorInfo)
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                <div class="w-6 h-6 bg-green-100 rounded-lg flex items-center justify-center mr-2">
                    <i class="fas fa-building text-green-600"></i>
                </div>
                Vendor Information
            </h3>
            <div class="space-y-3">
                <div>
                    <span class="text-sm font-medium text-gray-500">Vendor Name</span>
                    <p class="text-gray-800">{{ $product->vendor->vendorInfo->name_corporate }}</p>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-500">Location</span>
                    <p class="text-gray-800">{{ $product->vendor->vendorInfo->city->name ?? 'N/A' }}, {{ $product->vendor->vendorInfo->province->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-500">Contact</span>
                    <p class="text-gray-800">{{ $product->vendor->vendorInfo->phone ?? 'N/A' }}</p>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-500">Rating</span>
                    <div class="flex items-center space-x-1">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= floor($product->vendor->vendorInfo->averageRating()))
                                <i class="fas fa-star text-yellow-400 text-sm"></i>
                            @elseif($i - 0.5 <= $product->vendor->vendorInfo->averageRating())
                                <i class="fas fa-star-half-alt text-yellow-400 text-sm"></i>
                            @else
                                <i class="far fa-star text-yellow-400 text-sm"></i>
                            @endif
                        @endfor
                        <span class="text-gray-800 ml-1">{{ number_format($product->vendor->vendorInfo->averageRating(), 1) }}</span>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Related Products -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4">Related Products</h3>
            <div class="space-y-4">
                <div class="text-center py-8">
                    <i class="fas fa-shopping-cart text-gray-300 text-4xl mb-4"></i>
                    <p class="text-gray-500">No related products available</p>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4">Quick Actions</h3>
            <div class="space-y-3">
                @if(request('from') == 'form_booker')
                    <a href="{{ route('user.form_booker', ['product' => $product->id]) }}" class="w-full bg-green-600 text-white px-4 py-3 rounded-xl font-semibold hover:bg-green-700 transition-all duration-200 text-center block">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Return to Form Booking
                    </a>
                @else
                    <a href="{{ route('user.form_booker', ['product' => $product->id]) }}" class="w-full bg-green-600 text-white px-4 py-3 rounded-xl font-semibold hover:bg-green-700 transition-all duration-200 text-center block">
                        <i class="fas fa-plus-circle mr-2"></i>
                        Book Now
                    </a>
                @endif
                <button class="w-full bg-gray-100 text-gray-700 px-4 py-3 rounded-xl font-semibold hover:bg-gray-200 transition-all duration-200">
                    <i class="fas fa-share mr-2"></i>
                    Share Product
                </button>
                <button class="w-full bg-gray-100 text-gray-700 px-4 py-3 rounded-xl font-semibold hover:bg-gray-200 transition-all duration-200">
                    <i class="fas fa-question-circle mr-2"></i>
                    Ask Questions
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
