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
            <!-- Package Image -->
            <div class="lg:w-1/2">
                @if($package->image)
                    <img src="{{ asset('storage/' . $package->image) }}" alt="{{ $package->name_package }}" class="w-full h-96 object-cover rounded-2xl shadow-2xl">
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
                    <div class="text-3xl font-bold text-white mb-2">Rp {{ number_format($package->price_publish, 0, ',', '.') }}</div>
                    <div class="text-blue-100">Published Price</div>
                    <div class="text-sm text-blue-200 mt-2">Real Price: Rp {{ number_format($package->price_real, 0, ',', '.') }}</div>
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
                    <button class="flex-1 bg-white/20 backdrop-blur-sm text-white px-8 py-4 rounded-xl font-semibold hover:bg-white/30 transition-all duration-200 border border-white/30">
                        <i class="fas fa-heart mr-2"></i>
                        Save to Wishlist
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Package Details -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
    <!-- Main Content -->
    <div class="lg:col-span-2 space-y-8">
        <!-- Description -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-info-circle text-blue-600"></i>
                </div>
                About This Package
            </h2>
            <p class="text-gray-600 leading-relaxed">{{ $package->description }}</p>
        </div>

        <!-- Package Specifications -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8">
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
                @if($package->products_data)
                <div class="md:col-span-2">
                    <span class="text-sm font-medium text-gray-500">Products Included</span>
                    <div class="mt-2 space-y-2">
                        @foreach($package->products_data as $product)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    @if(isset($product['image']) && $product['image'])
                                        <img src="{{ asset('storage/' . $product['image']) }}" alt="{{ $product['name'] ?? 'Product' }}" class="w-10 h-10 object-cover rounded-lg">
                                    @else
                                        <div class="w-10 h-10 bg-gray-200 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-box text-gray-500 text-sm"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <p class="font-medium text-gray-800">{{ $product['name'] ?? 'Unnamed Product' }}</p>
                                        <p class="text-sm text-gray-500">{{ isset($product['description']) && $product['description'] ? Str::limit($product['description'], 50) : 'No description' }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-gray-800">Rp {{ number_format($product['price'] ?? 0, 0, ',', '.') }}</p>
                                    <p class="text-sm text-gray-500">{{ $product['quantity'] ?? 1 }} unit{{ ($product['quantity'] ?? 1) > 1 ? 's' : '' }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif
                @if($package->addons_data)
                <div class="md:col-span-2">
                    <span class="text-sm font-medium text-gray-500">Addons Included</span>
                    <div class="mt-2 space-y-2">
                        @foreach($package->addons_data as $addon)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    @if(isset($addon['image']) && $addon['image'])
                                        <img src="{{ asset('storage/' . $addon['image']) }}" alt="{{ $addon['addons'] ?? 'Addon' }}" class="w-10 h-10 object-cover rounded-lg">
                                    @else
                                        <div class="w-10 h-10 bg-gray-200 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-plus-circle text-gray-500 text-sm"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <p class="font-medium text-gray-800">{{ $addon['addons'] ?? 'Unnamed Addon' }}</p>
                                        <p class="text-sm text-gray-500">{{ isset($addon['desc']) && $addon['desc'] ? Str::limit($addon['desc'], 50) : 'No description' }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-gray-800">Rp {{ number_format($addon['price'] ?? 0, 0, ',', '.') }}</p>
                                    <p class="text-sm text-gray-500">{{ $addon['quantity'] ?? 1 }} unit{{ ($addon['quantity'] ?? 1) > 1 ? 's' : '' }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
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
</script>
@endsection
