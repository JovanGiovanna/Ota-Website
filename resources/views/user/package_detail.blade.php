@extends('layouts.user')

@section('title', $package->name_package . ' - Package Detail')

@section('welcome')
Package Details
@endsection

@section('content')
<!-- Package Hero Section -->
<div class="bg-gradient-to-r from-blue-600 via-purple-600 to-indigo-800 rounded-2xl p-8 mb-8 text-white shadow-2xl">
    <div class="max-w-6xl mx-auto">
        <div class="flex flex-col lg:flex-row items-start gap-8">
            <!-- Package Image Carousel -->
            <div class="lg:w-1/2">
                @php
                    $validImages = array_filter((array) ($package->images ?? []), function($img) {
                        return is_string($img) && !empty($img);
                    });
                @endphp
                @if($validImages && count($validImages) > 0)
                    <div class="relative w-full h-96 rounded-2xl overflow-hidden shadow-2xl cursor-pointer" onclick="openGallery('package', 0)">
                        <!-- Main Image Container -->
                        <div id="package-carousel" class="relative w-full h-full">
                            @foreach($validImages as $index => $image)
                                <div class="carousel-slide absolute inset-0 transition-opacity duration-500 {{ $index === 0 ? 'opacity-100' : 'opacity-0' }}">
                                    <img src="{{ asset('storage/' . $image) }}" alt="{{ $package->name_package }} - Image {{ $index + 1 }}" class="w-full h-full object-cover">
                                </div>
                            @endforeach
                        </div>
                        <!-- Gallery Icon Overlay -->
                        <div class="absolute inset-0 bg-black/20 opacity-0 hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                            <div class="bg-white/90 backdrop-blur-sm rounded-full p-4">
                                <i class="fas fa-images text-gray-800 text-2xl"></i>
                            </div>
                        </div>

                        <!-- Navigation Buttons -->
                        @if(count($validImages) > 1)
                            <button id="package-prev" class="absolute left-4 top-1/2 transform -translate-y-1/2 bg-white/90 backdrop-blur-sm hover:bg-white text-gray-800 p-3 rounded-full shadow-lg transition-all duration-200">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <button id="package-next" class="absolute right-4 top-1/2 transform -translate-y-1/2 bg-white/90 backdrop-blur-sm hover:bg-white text-gray-800 p-3 rounded-full shadow-lg transition-all duration-200">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        @endif

                        <!-- Image Indicators -->
                        @if(count($validImages) > 1)
                            <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex space-x-2">
                                @foreach($validImages as $index => $image)
                                    <button class="package-indicator w-3 h-3 rounded-full transition-all duration-200 {{ $index === 0 ? 'bg-white' : 'bg-white/50' }}" data-slide="{{ $index }}"></button>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @else
                    <div class="w-full h-96 bg-gradient-to-r from-blue-400 to-purple-500 rounded-2xl flex items-center justify-center shadow-2xl">
                        <i class="fas fa-box text-white text-6xl"></i>
                    </div>
                @endif
            </div>

            <!-- Package Info -->
            <div class="lg:w-1/2">
                <div class="flex items-center space-x-2 mb-4">
                    <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-white/20 text-white">Package</span>
                    <div class="flex items-center space-x-1">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= floor($package->averageRating()))
                                <i class="fas fa-star text-yellow-300 text-sm"></i>
                            @elseif($i - 0.5 <= $package->averageRating())
                                <i class="fas fa-star-half-alt text-yellow-300 text-sm"></i>
                            @else
                                <i class="far fa-star text-yellow-300 text-sm"></i>
                            @endif
                        @endfor
                        <span class="text-sm ml-1">{{ number_format($package->averageRating(), 1) }}</span>
                        <span class="text-sm text-blue-100">({{ $package->reviews->count() }} reviews)</span>
                    </div>
                </div>

                <h1 class="text-4xl font-bold mb-4">{{ $package->name_package }}</h1>
                <p class="text-blue-100 text-lg mb-6">{{ Str::limit($package->description, 200) }}</p>

                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-6 mb-6">
                    <div class="text-blue-100">Price</div>
                    <div class="text-3xl font-bold text-white mb-2">Rp {{ number_format($package->nta, 0, ',', '.') }}</div>
                </div>

                <div class="flex flex-col sm:flex-row gap-4">
                    @if(request('from') == 'form_booker')
                        <a href="{{ route('user.form_booker', ['package' => $package->id]) }}" class="flex-1 bg-white text-blue-600 px-8 py-4 rounded-xl font-semibold hover:bg-blue-50 transition-all duration-200 text-center shadow-lg">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Return to Form Booking
                        </a>
                    @else
                        <a href="{{ route('user.form_booker', ['package' => $package->id]) }}" class="flex-1 bg-white text-blue-600 px-8 py-4 rounded-xl font-semibold hover:bg-blue-50 transition-all duration-200 text-center shadow-lg">
                            <i class="fas fa-plus-circle mr-2"></i>
                            Book Now
                        </a>
                    @endif
                    <button id="wishlist-btn" class="flex-1 bg-white/20 backdrop-blur-sm text-white px-8 py-4 rounded-xl font-semibold hover:bg-white/30 transition-all duration-200 border border-white/30" onclick="toggleWishlist('package', '{{ $package->id }}')">
                        <i class="far fa-heart mr-2" id="wishlist-icon"></i>
                        <span id="wishlist-text">Save to Wishlist</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Package Details -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
    <!-- Main Content -->
    <div class="lg:col-span-2">
        <!-- Tab Panel -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            <!-- Tab Buttons -->
            <div class="border-b border-gray-200">
                <nav class="flex">
                    <button class="tab-button active px-6 py-4 text-sm font-medium text-blue-600 border-b-2 border-blue-600 flex items-center" data-tab="description">
                        <i class="fas fa-info-circle mr-2"></i>
                        Description
                    </button>
                    <button class="tab-button px-6 py-4 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent hover:border-gray-300 flex items-center" data-tab="specifications">
                        <i class="fas fa-list mr-2"></i>
                        Specifications
                    </button>
                    <button class="tab-button px-6 py-4 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent hover:border-gray-300 flex items-center" data-tab="refund-policy">
                        <i class="fas fa-undo-alt mr-2"></i>
                        Refund Policy
                    </button>
                    <button class="tab-button px-6 py-4 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent hover:border-gray-300 flex items-center" data-tab="reviews">
                        <i class="fas fa-star mr-2"></i>
                        Reviews
                    </button>
                </nav>
            </div>

            <!-- Tab Content -->
            <div class="p-8">
                <!-- Description Tab -->
                <div id="description-tab" class="tab-content">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-info-circle text-blue-600"></i>
                        </div>
                        About This Package
                    </h2>
                    <p class="text-gray-600 leading-relaxed">{{ $package->description }}</p>
                </div>

                <!-- Specifications Tab -->
                <div id="specifications-tab" class="tab-content hidden">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                        <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-info-circle text-green-600"></i>
                        </div>
                        Package Specifications
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <span class="text-sm font-medium text-gray-500">Slug</span>
                            <p class="text-gray-800">{{ $package->slug }}</p>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-500">Status</span>
                            <p class="text-gray-800">{{ $package->is_active ? 'Active' : 'Inactive' }}</p>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-500">Publish Start</span>
                            <p class="text-gray-800">{{ $package->start_publish ? $package->start_publish->format('d M Y H:i') : 'N/A' }}</p>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-500">Publish End</span>
                            <p class="text-gray-800">{{ $package->end_publish ? $package->end_publish->format('d M Y H:i') : 'N/A' }}</p>
                        </div>
                        @if($package->products_data && count($package->products_data) > 0)
                        <div class="md:col-span-2">
                            <span class="text-sm font-medium text-gray-500">Products Included</span>
                            <div class="mt-2 space-y-2">
                                @foreach($package->products_data as $product)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                        <div class="flex items-center space-x-3">
                                            @php
                                                $validImages = array_filter((array) ($product['images'] ?? []), function($img) {
                                                    return is_string($img) && !empty($img);
                                                });
                                            @endphp
                                            @if($validImages && count($validImages) > 0)
                                                <img src="{{ asset('storage/' . $validImages[0]) }}" alt="{{ $product['name'] ?? 'Product' }}" class="w-10 h-10 object-cover rounded-lg">
                                            @else
                                                <div class="w-10 h-10 bg-gray-200 rounded-lg flex items-center justify-center">
                                                    <i class="fas fa-box text-gray-500 text-sm"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <p class="font-medium text-gray-800">{{ $product['name'] ?? 'Unnamed Product' }}</p>
                                                <p class="text-sm text-gray-500">{{ isset($product['description']) && $product['description'] ? Str::limit($product['description'], 100) : 'No description' }}</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-semibold text-gray-800">Rp {{ number_format($product['nta'] ?? 0, 0, ',', '.') }}</p>
                                            <p class="text-sm text-gray-500">{{ $product['pax'] ?? 1 }} pax</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                        @if($package->addons_data && count($package->addons_data) > 0)
                        <div class="md:col-span-2">
                            <span class="text-sm font-medium text-gray-500">Add-ons Included</span>
                            <div class="mt-2 space-y-2">
                                @foreach($package->addons_data as $addon)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                        <div class="flex items-center space-x-3">
                                            @php
                                                $validImages = array_filter((array) ($addon['images'] ?? []), function($img) {
                                                    return is_string($img) && !empty($img);
                                                });
                                            @endphp
                                            @if($validImages && count($validImages) > 0)
                                                <img src="{{ asset('storage/' . $validImages[0]) }}" alt="{{ $addon['addons'] ?? 'Addon' }}" class="w-10 h-10 object-cover rounded-lg">
                                            @else
                                                <div class="w-10 h-10 bg-gray-200 rounded-lg flex items-center justify-center">
                                                    <i class="fas fa-plus-circle text-gray-500 text-sm"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <p class="font-medium text-gray-800">{{ $addon['name'] ?? 'Unnamed Addon' }}</p>
                                                <p class="text-sm text-gray-500">{{ isset($addon['desc']) && $addon['desc'] ? Str::limit($addon['desc'], 50) : 'No description' }}</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-semibold text-gray-800">Rp {{ number_format($addon['nta'] ?? 0, 0, ',', '.') }}</p>
                                            <p class="text-sm text-gray-500">{{ $addon['pax'] ?? 1 }} pax</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Refund Policy Tab -->
                <div id="refund-policy-tab" class="tab-content hidden">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                        <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-undo-alt text-red-600"></i>
                        </div>
                        Refund Policy
                    </h2>
                    @if($package->refund_policy)
                        <div class="prose prose-gray max-w-none">
                            {!! nl2br(e($package->refund_policy)) !!}
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-info-circle text-gray-300 text-4xl mb-4"></i>
                            <p class="text-gray-500">No refund policy information available for this package.</p>
                        </div>
                    @endif
                </div>

                <!-- Reviews Tab -->
                <div id="reviews-tab" class="tab-content hidden">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                        <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-star text-yellow-600"></i>
                        </div>
                        Reviews & Ratings
                    </h2>

                    @if($package->reviews->count() > 0)
                        <div class="space-y-6">
                            @foreach($package->reviews->take(5) as $review)
                                <div class="border-b border-gray-100 pb-6 last:border-b-0 last:pb-0">
                                    <div class="flex items-start space-x-4">
                                        <div class="w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center">
                                            <span class="text-gray-600 font-semibold">{{ substr($review->user->name, 0, 1) }}</span>
                                        </div>
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-2 mb-2">
                                                <span class="font-semibold text-gray-800">{{ $review->user->name }}</span>
                                                <div class="flex items-center space-x-1 star-rating">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <input type="radio" id="rating-review-{{ $review->id }}-{{ $i }}" name="rating-review-{{ $review->id }}" value="{{ $i }}" class="sr-only" {{ $i <= $review->rating ? 'checked' : '' }}>
                                                        <label for="rating-review-{{ $review->id }}-{{ $i }}" class="cursor-pointer star text-sm" data-rating="{{ $i }}">
                                                            <i class="fas fa-star {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                                        </label>
                                                    @endfor
                                                </div>
                                                <span class="text-sm text-gray-500">{{ $review->created_at->diffForHumans() }}</span>
                                            </div>
                                            <p class="text-gray-600">{{ $review->comment }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-star-half-alt text-gray-300 text-4xl mb-4"></i>
                            <p class="text-gray-500">No reviews yet</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="space-y-8">
        <!-- Vendor Info -->
        @if($package->vendorInfo)
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                <div class="w-6 h-6 bg-blue-100 rounded-lg flex items-center justify-center mr-2">
                    <i class="fas fa-building text-blue-600"></i>
                </div>
                Vendor Information
            </h3>
            <div class="space-y-3">
                <div>
                    <span class="text-sm font-medium text-gray-500">Vendor Name</span>
                    <p class="text-gray-800">{{ $package->vendorInfo->vendor_name }}</p>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-500">Location</span>
                    <p class="text-gray-800">{{ $package->vendorInfo->city->name ?? 'N/A' }}, {{ $package->vendorInfo->province->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-500">Contact</span>
                    <p class="text-gray-800">{{ $package->vendorInfo->phone ?? 'N/A' }}</p>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-500">Rating</span>
                    <div class="flex items-center space-x-1">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= floor($package->vendorInfo->averageRating()))
                                <i class="fas fa-star text-yellow-400 text-sm"></i>
                            @elseif($i - 0.5 <= $package->vendorInfo->averageRating())
                                <i class="fas fa-star-half-alt text-yellow-400 text-sm"></i>
                            @else
                                <i class="far fa-star text-yellow-400 text-sm"></i>
                            @endif
                        @endfor
                        <span class="text-gray-800 ml-1">{{ number_format($package->vendorInfo->averageRating(), 1) }}</span>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Facilities -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                <div class="w-6 h-6 bg-green-100 rounded-lg flex items-center justify-center mr-2">
                    <i class="fas fa-concierge-bell text-green-600"></i>
                </div>
                Facilities & Amenities
            </h3>
            <div class="grid grid-cols-2 gap-3">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-wifi text-green-500"></i>
                    <span class="text-sm text-gray-700">Free WiFi</span>
                </div>
                <div class="flex items-center space-x-2">
                    <i class="fas fa-swimming-pool text-green-500"></i>
                    <span class="text-sm text-gray-700">Swimming Pool</span>
                </div>
                <div class="flex items-center space-x-2">
                    <i class="fas fa-parking text-green-500"></i>
                    <span class="text-sm text-gray-700">Parking</span>
                </div>
                <div class="flex items-center space-x-2">
                    <i class="fas fa-spa text-green-500"></i>
                    <span class="text-sm text-gray-700">Spa</span>
                </div>
                <div class="flex items-center space-x-2">
                    <i class="fas fa-dumbbell text-green-500"></i>
                    <span class="text-sm text-gray-700">Gym</span>
                </div>
                <div class="flex items-center space-x-2">
                    <i class="fas fa-utensils text-green-500"></i>
                    <span class="text-sm text-gray-700">Restaurant</span>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4">Quick Actions</h3>
            <div class="space-y-3">
                @if(request('from') == 'form_booker')
                    <a href="{{ route('user.form_booker', ['package' => $package->id]) }}" class="w-full bg-blue-600 text-white px-4 py-3 rounded-xl font-semibold hover:bg-blue-700 transition-all duration-200 text-center block">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Return to Form Booking
                    </a>
                @else
                    <a href="{{ route('user.form_booker', ['package' => $package->id]) }}" class="w-full bg-blue-600 text-white px-4 py-3 rounded-xl font-semibold hover:bg-blue-700 transition-all duration-200 text-center block">
                        <i class="fas fa-plus-circle mr-2"></i>
                        Book Now
                    </a>
                @endif
                <button class="w-full bg-gray-100 text-gray-700 px-4 py-3 rounded-xl font-semibold hover:bg-gray-200 transition-all duration-200">
                    <i class="fas fa-share mr-2"></i>
                    Share Package
                </button>
                <button class="w-full bg-gray-100 text-gray-700 px-4 py-3 rounded-xl font-semibold hover:bg-gray-200 transition-all duration-200">
                    <i class="fas fa-question-circle mr-2"></i>
                    Ask Questions
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize package carousel
    @php
        $validImages = array_filter((array) ($package->images ?? []), function($img) {
            return is_string($img) && !empty($img);
        });
    @endphp
    @if($validImages && count($validImages) > 1)
    initializeCarousel('package');
    @endif

    // Initialize wishlist button state
    @if(Auth::check())
        @if($isInWishlist)
            document.getElementById('wishlist-icon').classList.remove('far');
            document.getElementById('wishlist-icon').classList.add('fas');
            document.getElementById('wishlist-text').textContent = 'Saved to Wishlist';
        @else
            document.getElementById('wishlist-icon').classList.remove('fas');
            document.getElementById('wishlist-icon').classList.add('far');
            document.getElementById('wishlist-text').textContent = 'Save to Wishlist';
        @endif
    @else
        document.getElementById('wishlist-icon').classList.remove('fas');
        document.getElementById('wishlist-icon').classList.add('far');
        document.getElementById('wishlist-text').textContent = 'Save to Wishlist';
    @endif

    // Get all star rating containers
    const starRatingContainers = document.querySelectorAll('.star-rating');

    starRatingContainers.forEach(container => {
        const stars = container.querySelectorAll('.star');
        const radioButtons = container.querySelectorAll('input[type="radio"]');

        function updateStars(rating) {
            stars.forEach((star, index) => {
                if (index < rating) {
                    star.classList.remove('text-gray-300');
                    star.classList.add('text-yellow-400');
                } else {
                    star.classList.remove('text-yellow-400');
                    star.classList.add('text-gray-300');
                }
            });
        }

        // Initialize with the checked radio button
        const checkedRadio = container.querySelector('input[type="radio"]:checked');
        if (checkedRadio) {
            updateStars(parseInt(checkedRadio.value));
        }
    });
});

function toggleWishlist(type, id) {
    @if(!Auth::check())
        // Redirect to login if not authenticated
        window.location.href = '{{ route("login") }}';
        return;
    @endif

    fetch('{{ url("/wishlist/toggle") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            type: type,
            id: id
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const icon = document.getElementById('wishlist-icon');
            const text = document.getElementById('wishlist-text');

            if (data.action === 'added') {
                icon.classList.remove('far');
                icon.classList.add('fas');
                text.textContent = 'Saved to Wishlist';
                // Show success message
                showMessage('Added to wishlist!', 'success');
            } else {
                icon.classList.remove('fas');
                icon.classList.add('far');
                text.textContent = 'Save to Wishlist';
                // Show success message
                showMessage('Removed from wishlist!', 'success');
            }
        } else {
            showMessage(data.message || 'An error occurred', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('An error occurred', 'error');
    });
}

function showMessage(message, type) {
    // Create message element
    const messageEl = document.createElement('div');
    messageEl.className = `fixed top-4 right-4 px-6 py-3 rounded-lg font-semibold z-50 ${
        type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
    }`;
    messageEl.textContent = message;

    // Add to page
    document.body.appendChild(messageEl);

    // Remove after 3 seconds
    setTimeout(() => {
        messageEl.remove();
    }, 3000);
}

function initializeCarousel(type) {
    const carousel = document.getElementById(`${type}-carousel`);
    const slides = carousel.querySelectorAll('.carousel-slide');
    const prevBtn = document.getElementById(`${type}-prev`);
    const nextBtn = document.getElementById(`${type}-next`);
    const indicators = document.querySelectorAll(`.${type}-indicator`);

    let currentSlide = 0;
    const totalSlides = slides.length;

    function showSlide(index) {
        slides.forEach((slide, i) => {
            slide.classList.toggle('opacity-100', i === index);
            slide.classList.toggle('opacity-0', i !== index);
        });

        indicators.forEach((indicator, i) => {
            indicator.classList.toggle('bg-white', i === index);
            indicator.classList.toggle('bg-white/50', i !== index);
        });

        currentSlide = index;
    }

    function nextSlide() {
        const nextIndex = (currentSlide + 1) % totalSlides;
        showSlide(nextIndex);
    }

    function prevSlide() {
        const prevIndex = (currentSlide - 1 + totalSlides) % totalSlides;
        showSlide(prevIndex);
    }

    // Event listeners
    if (prevBtn) prevBtn.addEventListener('click', prevSlide);
    if (nextBtn) nextBtn.addEventListener('click', nextSlide);

    indicators.forEach((indicator, index) => {
        indicator.addEventListener('click', () => showSlide(index));
    });

    // Keyboard navigation
    document.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowLeft') {
            prevSlide();
        } else if (e.key === 'ArrowRight') {
            nextSlide();
        }
    });

    // Touch/swipe support
    let startX = 0;
    let endX = 0;

    carousel.addEventListener('touchstart', (e) => {
        startX = e.touches[0].clientX;
    });

    carousel.addEventListener('touchend', (e) => {
        endX = e.changedTouches[0].clientX;
        const diffX = startX - endX;

        if (Math.abs(diffX) > 50) { // Minimum swipe distance
            if (diffX > 0) {
                nextSlide(); // Swipe left
            } else {
                prevSlide(); // Swipe right
            }
        }
    });

    // Auto-play (optional, commented out)
    // setInterval(nextSlide, 5000);
}

// Gallery Popup Functions
function openGallery(type, startIndex = 0) {
    @php
        $validImages = array_filter((array) ($package->images ?? []), function($img) {
            return is_string($img) && !empty($img);
        });
        $imageUrls = array_map(function($img) {
            return '/storage/' . $img;
        }, $validImages);
    @endphp
    @if($validImages && count($validImages) > 0)
    const images = @json($imageUrls);
    window.galleryImages = images; // Store globally for other functions
    const galleryModal = createGalleryModal(images, startIndex, '{{ $package->name_package }}');
    document.body.appendChild(galleryModal);
    document.body.style.overflow = 'hidden';
    @endif
}

function createGalleryModal(images, startIndex, title) {
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-black bg-opacity-90 z-50 flex items-center justify-center p-4';
    modal.innerHTML = `
        <div class="relative max-w-5xl max-h-full w-full">
            <!-- Close Button -->
            <button onclick="closeGallery()" class="absolute top-4 right-4 z-10 bg-black bg-opacity-50 text-white p-2 rounded-full hover:bg-opacity-70 transition-all">
                <i class="fas fa-times text-xl"></i>
            </button>

            <!-- Main Image -->
            <div class="relative bg-black rounded-lg overflow-hidden">
                <img id="gallery-main-image" src="${images[startIndex]}" alt="${title}" class="w-full h-auto max-h-[80vh] object-contain">
            </div>

            <!-- Navigation Buttons -->
            <button id="gallery-prev" class="absolute left-4 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 text-white p-3 rounded-full hover:bg-opacity-70 transition-all">
                <i class="fas fa-chevron-left text-xl"></i>
            </button>
            <button id="gallery-next" class="absolute right-4 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 text-white p-3 rounded-full hover:bg-opacity-70 transition-all">
                <i class="fas fa-chevron-right text-xl"></i>
            </button>

            <!-- Thumbnails -->
            <div class="mt-4 flex justify-center space-x-2 overflow-x-auto max-w-full">
                ${images.map((image, index) => `
                    <button onclick="showGalleryImage(${index})" class="flex-shrink-0 w-16 h-16 rounded-lg overflow-hidden border-2 ${index === startIndex ? 'border-white' : 'border-gray-600'} hover:border-gray-400 transition-all">
                        <img src="${image}" alt="Thumbnail ${index + 1}" class="w-full h-full object-cover">
                    </button>
                `).join('')}
            </div>

            <!-- Image Counter -->
            <div class="text-center mt-2 text-white">
                <span id="gallery-counter">${startIndex + 1} / ${images.length}</span>
            </div>
        </div>
    `;

    // Add event listeners
    let currentIndex = startIndex;

    const mainImage = modal.querySelector('#gallery-main-image');
    const prevBtn = modal.querySelector('#gallery-prev');
    const nextBtn = modal.querySelector('#gallery-next');
    const counter = modal.querySelector('#gallery-counter');

    function updateImage(index) {
        mainImage.src = images[index];
        currentIndex = index;
        counter.textContent = `${index + 1} / ${images.length}`;

        // Update thumbnail borders
        const thumbnails = modal.querySelectorAll('.flex-shrink-0');
        thumbnails.forEach((thumb, i) => {
            thumb.classList.toggle('border-white', i === index);
            thumb.classList.toggle('border-gray-600', i !== index);
        });
    }

    function showPrev() {
        const newIndex = currentIndex > 0 ? currentIndex - 1 : images.length - 1;
        updateImage(newIndex);
    }

    function showNext() {
        const newIndex = currentIndex < images.length - 1 ? currentIndex + 1 : 0;
        updateImage(newIndex);
    }

    prevBtn.addEventListener('click', showPrev);
    nextBtn.addEventListener('click', showNext);

    // Keyboard navigation
    document.addEventListener('keydown', function galleryKeyHandler(e) {
        if (e.key === 'ArrowLeft') showPrev();
        if (e.key === 'ArrowRight') showNext();
        if (e.key === 'Escape') closeGallery();

        // Store reference for cleanup
        modal._keyHandler = galleryKeyHandler;
    });

    // Click outside to close
    modal.addEventListener('click', function(e) {
        if (e.target === modal) closeGallery();
    });

    return modal;
}

function showGalleryImage(index) {
    const modal = document.querySelector('.fixed.inset-0.bg-black');
    if (modal) {
        const images = window.galleryImages;
        const mainImage = modal.querySelector('#gallery-main-image');
        const counter = modal.querySelector('#gallery-counter');

        mainImage.src = images[index];
        counter.textContent = `${index + 1} / ${images.length}`;

        // Update thumbnail borders
        const thumbnails = modal.querySelectorAll('.flex-shrink-0');
        thumbnails.forEach((thumb, i) => {
            thumb.classList.toggle('border-white', i === index);
            thumb.classList.toggle('border-gray-600', i !== index);
        });
    }
}

function closeGallery() {
    const modal = document.querySelector('.fixed.inset-0.bg-black');
    if (modal) {
        // Remove keyboard event listener
        if (modal._keyHandler) {
            document.removeEventListener('keydown', modal._keyHandler);
        }
        modal.remove();
        document.body.style.overflow = '';
    }
}

// Tab functionality
document.addEventListener('DOMContentLoaded', function() {
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');

    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            const tabName = button.getAttribute('data-tab');

            // Remove active class from all buttons
            tabButtons.forEach(btn => {
                btn.classList.remove('active', 'text-blue-600', 'border-blue-600');
                btn.classList.add('text-gray-500', 'border-transparent');
            });

            // Add active class to clicked button
            button.classList.add('active', 'text-blue-600', 'border-blue-600');
            button.classList.remove('text-gray-500', 'border-transparent');

            // Hide all tab contents
            tabContents.forEach(content => {
                content.classList.add('hidden');
            });

            // Show selected tab content
            const activeTab = document.getElementById(tabName + '-tab');
            if (activeTab) {
                activeTab.classList.remove('hidden');
            }
        });
    });
});
</script>
@endsection
