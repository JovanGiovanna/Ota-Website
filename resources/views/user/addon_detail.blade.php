@extends('layouts.user')

@section('title', $addon->addons . ' - Addon Detail')

@section('welcome')
Addon Details
@endsection

@section('content')
<!-- Addon Hero Section -->
<div class="bg-gradient-to-r from-orange-600 via-red-600 to-pink-800 rounded-2xl p-8 mb-8 text-white shadow-2xl">
    <div class="max-w-6xl mx-auto">
        <div class="flex flex-col lg:flex-row items-start gap-8">
            <!-- Addon Image Carousel -->
            <div class="lg:w-1/2">
                @php
                    $validImages = array_filter((array) ($addon->images ?? []), function($img) {
                        return is_string($img) && !empty($img);
                    });
                @endphp
                @if($validImages && count($validImages) > 0)
                    <div class="relative w-full h-96 rounded-2xl overflow-hidden shadow-2xl cursor-pointer" onclick="openGallery('addon', 0)">
                        <!-- Main Image Container -->
                        <div id="addon-carousel" class="relative w-full h-full">
                            @foreach($validImages as $index => $image)
                                <div class="carousel-slide absolute inset-0 transition-opacity duration-500 {{ $index === 0 ? 'opacity-100' : 'opacity-0' }}">
                                    <img src="{{ asset('storage/' . $image) }}" alt="{{ $addon->addons }} - Image {{ $index + 1 }}" class="w-full h-full object-cover">
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
                            <button id="addon-prev" class="absolute left-4 top-1/2 transform -translate-y-1/2 bg-white/90 backdrop-blur-sm hover:bg-white text-gray-800 p-3 rounded-full shadow-lg transition-all duration-200">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <button id="addon-next" class="absolute right-4 top-1/2 transform -translate-y-1/2 bg-white/90 backdrop-blur-sm hover:bg-white text-gray-800 p-3 rounded-full shadow-lg transition-all duration-200">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        @endif

                        <!-- Image Indicators -->
                        @if(count($validImages) > 1)
                            <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex space-x-2">
                                @foreach($validImages as $index => $image)
                                    <button class="addon-indicator w-3 h-3 rounded-full transition-all duration-200 {{ $index === 0 ? 'bg-white' : 'bg-white/50' }}" data-slide="{{ $index }}"></button>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @else
                    <div class="w-full h-96 bg-gradient-to-r from-orange-400 to-pink-500 rounded-2xl flex items-center justify-center shadow-2xl">
                        <i class="fas fa-plus-circle text-white text-6xl"></i>
                    </div>
                @endif
            </div>

            <!-- Addon Info -->
            <div class="lg:w-1/2">
                <div class="flex items-center space-x-2 mb-4">
                    <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-white/20 text-white">Addon</span>
                    <div class="flex items-center space-x-1">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= floor($addon->averageRating()))
                                <i class="fas fa-star text-yellow-300 text-sm"></i>
                            @elseif($i - 0.5 <= $addon->averageRating())
                                <i class="fas fa-star-half-alt text-yellow-300 text-sm"></i>
                            @else
                                <i class="far fa-star text-yellow-300 text-sm"></i>
                            @endif
                        @endfor
                        <span class="text-sm ml-1">{{ number_format($addon->averageRating(), 1) }}</span>
                        <span class="text-sm text-orange-100">({{ $addon->reviews->count() }} reviews)</span>
                    </div>
                </div>

                <h1 class="text-4xl font-bold mb-4">{{ $addon->addons }}</h1>
                <p class="text-orange-100 text-lg mb-6">{{ Str::limit($addon->desc, 200) }}</p>

                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-6 mb-6">
                    <div class="text-3xl font-bold text-white mb-2">Rp {{ number_format($addon->finalPrice, 0, ',', '.') }}</div>
                    <div class="text-orange-100">per unit</div>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 text-center">
                        <div class="text-2xl font-bold text-white">{{ $addon->status == 'available' ? 'Available' : ($addon->status == 'unavailable' ? 'Unavailable' : 'Draft') }}</div>
                        <div class="text-orange-100 text-sm">Status</div>
                    </div>
                    @if($addon->pax)
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 text-center">
                        <div class="text-2xl font-bold text-white">{{ $addon->pax }}</div>
                        <div class="text-orange-100 text-sm">Capacity (Pax)</div>
                    </div>
                    @else
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 text-center">
                        <div class="text-2xl font-bold text-white">{{ $addon->publish ? 'Published' : 'Unpublished' }}</div>
                        <div class="text-orange-100 text-sm">Publish Status</div>
                    </div>
                    @endif
                </div>

                <div class="flex flex-col sm:flex-row gap-4">
                    @if(request('from') == 'form_booker')
                        <a href="{{ route('user.form_booker', ['addon' => $addon->id]) }}" class="flex-1 bg-white text-orange-600 px-8 py-4 rounded-xl font-semibold hover:bg-orange-50 transition-all duration-200 text-center shadow-lg">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Return to Form Booking
                        </a>
                    @else
                        <a href="{{ route('user.form_booker', ['addon' => $addon->id]) }}" class="flex-1 bg-white text-orange-600 px-8 py-4 rounded-xl font-semibold hover:bg-orange-50 transition-all duration-200 text-center shadow-lg">
                            <i class="fas fa-plus-circle mr-2"></i>
                            Book Now
                        </a>
                    @endif
                    <button id="wishlist-btn" class="flex-1 bg-white/20 backdrop-blur-sm text-white px-8 py-4 rounded-xl font-semibold hover:bg-white/30 transition-all duration-200 border border-white/30" onclick="toggleWishlist('addon', '{{ $addon->id }}')">
                        <i class="far fa-heart mr-2" id="wishlist-icon"></i>
                        <span id="wishlist-text">Save to Wishlist</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Addon Details -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
    <!-- Main Content -->
    <div class="lg:col-span-2 space-y-8">
        <!-- Description -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-info-circle text-orange-600"></i>
                </div>
                About This Addon
            </h2>
            <p class="text-gray-600 leading-relaxed">{{ $addon->desc }}</p>
        </div>

        <!-- Addon Specifications -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-list text-blue-600"></i>
                </div>
                Addon Specifications
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="flex justify-between items-center py-3 border-b border-gray-100">
                    <span class="text-gray-600">Status</span>
                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $addon->status == 'available' ? 'bg-green-100 text-green-800' : ($addon->status == 'unavailable' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                        {{ ucfirst($addon->status) }}
                    </span>
                </div>
                <div class="flex justify-between items-center py-3 border-b border-gray-100">
                    <span class="text-gray-600">Publish Status</span>
                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $addon->publish ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                        {{ $addon->publish ? 'Published' : 'Unpublished' }}
                    </span>
                </div>
                @if($addon->pax)
                <div class="flex justify-between items-center py-3 border-b border-gray-100">
                    <span class="text-gray-600">Capacity (Pax)</span>
                    <span class="font-semibold text-gray-800">{{ $addon->pax }}</span>
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

            @if($addon->reviews->count() > 0)
                <div class="space-y-6">
                    @foreach($addon->reviews as $review)
                        <div class="border-b border-gray-100 pb-6 last:border-b-0 last:pb-0">
                            <div class="flex items-start space-x-4">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-user text-orange-600"></i>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex items-center space-x-2">
                                            <span class="font-semibold text-gray-800">{{ $review->user->name ?? 'Anonymous' }}</span>
                                            <div class="flex items-center">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="fas fa-star {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }} text-sm"></i>
                                                @endfor
                                            </div>
                                        </div>
                                        <span class="text-sm text-gray-500">{{ $review->created_at->format('M d, Y') }}</span>
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
                    <p class="text-gray-500">No reviews yet for this addon</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Sidebar -->
    <div class="space-y-8">
        <!-- Vendor Info -->
        @if($addon->vendor && $addon->vendor->vendorInfo)
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                <div class="w-6 h-6 bg-orange-100 rounded-lg flex items-center justify-center mr-2">
                    <i class="fas fa-building text-orange-600"></i>
                </div>
                Vendor Information
            </h3>
            <div class="space-y-3">
                <div>
                    <span class="text-sm font-medium text-gray-500">Vendor Name</span>
                    <p class="text-gray-800">{{ $addon->vendor->vendorInfo->name_corporate }}</p>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-500">Location</span>
                    <p class="text-gray-800">{{ $addon->vendor->vendorInfo->city->name ?? 'N/A' }}, {{ $addon->vendor->vendorInfo->province->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-500">Contact</span>
                    <p class="text-gray-800">{{ $addon->vendor->vendorInfo->phone ?? 'N/A' }}</p>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-500">Rating</span>
                    <div class="flex items-center space-x-1">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= floor($addon->vendor->vendorInfo->averageRating()))
                                <i class="fas fa-star text-yellow-400 text-sm"></i>
                            @elseif($i - 0.5 <= $addon->vendor->vendorInfo->averageRating())
                                <i class="fas fa-star-half-alt text-yellow-400 text-sm"></i>
                            @else
                                <i class="far fa-star text-yellow-400 text-sm"></i>
                            @endif
                        @endfor
                        <span class="text-gray-800 ml-1">{{ number_format($addon->vendor->vendorInfo->averageRating(), 1) }}</span>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Related Addons -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4">Related Addons</h3>
            <div class="space-y-4">
                <div class="text-center py-8">
                    <i class="fas fa-plus-circle text-gray-300 text-4xl mb-4"></i>
                    <p class="text-gray-500">No related addons available</p>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4">Quick Actions</h3>
            <div class="space-y-3">
                @if(request('from') == 'form_booker')
                    <a href="{{ route('user.form_booker', ['addon' => $addon->id]) }}" class="w-full bg-orange-600 text-white px-4 py-3 rounded-xl font-semibold hover:bg-orange-700 transition-all duration-200 text-center block">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Return to Form Booking
                    </a>
                @else
                    <a href="{{ route('user.form_booker', ['addon' => $addon->id]) }}" class="w-full bg-orange-600 text-white px-4 py-3 rounded-xl font-semibold hover:bg-orange-700 transition-all duration-200 text-center block">
                        <i class="fas fa-plus-circle mr-2"></i>
                        Book Now
                    </a>
                @endif
                <button class="w-full bg-gray-100 text-gray-700 px-4 py-3 rounded-xl font-semibold hover:bg-gray-200 transition-all duration-200">
                    <i class="fas fa-share mr-2"></i>
                    Share Addon
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
    // Initialize addon carousel
    @php
        $validImages = array_filter((array) ($addon->images ?? []), function($img) {
            return is_string($img) && !empty($img);
        });
    @endphp
    @if($validImages && count($validImages) > 1)
    initializeCarousel('addon');
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

// Gallery Popup Functions
function openGallery(type, startIndex = 0) {
    @php
        $validImages = array_filter((array) ($addon->images ?? []), function($img) {
            return is_string($img) && !empty($img);
        });
    @endphp
    @if($validImages && count($validImages) > 0)
    const images = @json($validImages).map(img => '{{ asset('storage/') }}/' + img);
    window.galleryImages = images;
    const galleryModal = createGalleryModal(images, startIndex, '{{ $addon->addons }}');
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
        const images = @json($validImages);
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
</script>
@endsection
