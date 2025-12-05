@extends('layouts.user')

@section('title', 'My Wishlist')

@section('welcome')
My Wishlist
@endsection

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">My Wishlist</h1>
        <p class="text-gray-600">Items you've saved for later</p>
    </div>

    @if($wishlists->count() > 0)
        <!-- Clear All Button -->
        <div class="mb-6 flex justify-end">
            <button onclick="clearAllWishlist()"
                    class="inline-flex items-center px-4 py-2 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition-colors duration-200">
                <i class="fas fa-trash mr-2"></i>
                Clear All
            </button>
        </div>

        <!-- Packages Section -->
        @php
            $packages = $wishlists->filter(function($wishlist) {
                return $wishlist->wishable_type === 'App\Models\Package' && $wishlist->wishable;
            });
        @endphp
        @if($packages->count() > 0)
        <div class="mb-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                <i class="fas fa-box text-blue-600 mr-3"></i>
                Packages ({{ $packages->count() }})
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($packages as $wishlist)
                    @php
                        $item = $wishlist->wishable;
                        $image = $item->images[0] ?? null;
                        $name = $item->name_package;
                        $description = $item->description;
                        $price = $item->final_price;
                    @endphp
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden hover:shadow-xl transition-shadow duration-300">
                        <!-- Image -->
                        <div class="relative">
                            @if($image)
                                <img src="{{ asset('storage/' . $image) }}" alt="{{ $name }}" class="w-full h-48 object-cover">
                            @else
                                <div class="w-full h-48 bg-gradient-to-r from-blue-400 to-purple-500 flex items-center justify-center">
                                    <i class="fas fa-box text-white text-3xl"></i>
                                </div>
                            @endif

                            <!-- Remove Button -->
                            <button onclick="removeFromWishlist('package', '{{ $item->id }}')"
                                    class="absolute top-3 right-3 bg-red-500 text-white p-2 rounded-full hover:bg-red-600 transition-colors duration-200">
                                <i class="fas fa-times text-sm"></i>
                            </button>

                            <!-- Type Badge -->
                            <div class="absolute top-3 left-3">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                    Package
                                </span>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="p-6">
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ $name }}</h3>

                            <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ Str::limit($description, 100) }}</p>

                            <!-- Price and Rating -->
                            <div class="flex items-center justify-between mb-4">
                                <div class="text-lg font-bold text-blue-600">Rp {{ number_format($price, 0, ',', '.') }}</div>

                                <!-- Rating -->
                                <div class="flex items-center space-x-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= floor($item->averageRating()))
                                            <i class="fas fa-star text-yellow-400 text-sm"></i>
                                        @elseif($i - 0.5 <= $item->averageRating())
                                            <i class="fas fa-star-half-alt text-yellow-400 text-sm"></i>
                                        @else
                                            <i class="far fa-star text-yellow-400 text-sm"></i>
                                        @endif
                                    @endfor
                                    <span class="text-sm text-gray-600 ml-1">({{ $item->reviews->count() }})</span>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex space-x-2">
                                <a href="{{ route('user.package_detail', $item->id) }}" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-semibold transition-colors duration-200 text-center text-sm">
                                    View Details
                                </a>
                                <a href="{{ route('user.form_booker', ['package' => $item->id]) }}" class="flex-1 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-semibold transition-colors duration-200 text-center text-sm">
                                    Book Now
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Products Section -->
        @php
            $products = $wishlists->filter(function($wishlist) {
                return $wishlist->wishable_type === 'App\Models\Product' && $wishlist->wishable;
            });
        @endphp
        @if($products->count() > 0)
        <div class="mb-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                <i class="fas fa-shopping-cart text-green-600 mr-3"></i>
                Products ({{ $products->count() }})
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($products as $wishlist)
                    @php
                        $item = $wishlist->wishable;
                        $image = $item->images[0] ?? null;
                        $name = $item->name;
                        $description = $item->description;
                        $price = $item->final_price;
                    @endphp
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden hover:shadow-xl transition-shadow duration-300">
                        <!-- Image -->
                        <div class="relative">
                            @if($image)
                                <img src="{{ asset('storage/' . $image) }}" alt="{{ $name }}" class="w-full h-48 object-cover">
                            @else
                                <div class="w-full h-48 bg-gradient-to-r from-green-400 to-blue-500 flex items-center justify-center">
                                    <i class="fas fa-shopping-cart text-white text-3xl"></i>
                                </div>
                            @endif

                            <!-- Remove Button -->
                            <button onclick="removeFromWishlist('product', '{{ $item->id }}')"
                                    class="absolute top-3 right-3 bg-red-500 text-white p-2 rounded-full hover:bg-red-600 transition-colors duration-200">
                                <i class="fas fa-times text-sm"></i>
                            </button>

                            <!-- Type Badge -->
                            <div class="absolute top-3 left-3">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    Product
                                </span>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="p-6">
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ $name }}</h3>

                            <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ Str::limit($description, 100) }}</p>

                            <!-- Price and Rating -->
                            <div class="flex items-center justify-between mb-4">
                                @if($item->final_price < $item->total_price_before_discount)
                                    <div class="text-lg font-bold text-green-600">
                                        <span class="text-sm text-gray-500 line-through">Rp {{ number_format($item->total_price_before_discount, 0, ',', '.') }}</span>
                                        Rp {{ number_format($item->final_price, 0, ',', '.') }}
                                    </div>
                                @else
                                    <div class="text-lg font-bold text-green-600">Rp {{ number_format($item->final_price, 0, ',', '.') }}</div>
                                @endif

                                <!-- Rating -->
                                <div class="flex items-center space-x-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= floor($item->averageRating()))
                                            <i class="fas fa-star text-yellow-400 text-sm"></i>
                                        @elseif($i - 0.5 <= $item->averageRating())
                                            <i class="fas fa-star-half-alt text-yellow-400 text-sm"></i>
                                        @else
                                            <i class="far fa-star text-yellow-400 text-sm"></i>
                                        @endif
                                    @endfor
                                    <span class="text-sm text-gray-600 ml-1">({{ $item->reviews->count() }})</span>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex space-x-2">
                                <a href="{{ route('user.product_detail', $item->id) }}" class="flex-1 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-semibold transition-colors duration-200 text-center text-sm">
                                    View Details
                                </a>
                                <a href="{{ route('user.form_booker', ['product' => $item->id]) }}" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-semibold transition-colors duration-200 text-center text-sm">
                                    Book Now
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Addons Section -->
        @php
            $addons = $wishlists->filter(function($wishlist) {
                return $wishlist->wishable_type === 'App\Models\Addon' && $wishlist->wishable;
            });
        @endphp
        @if($addons->count() > 0)
        <div class="mb-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                <i class="fas fa-plus-circle text-orange-600 mr-3"></i>
                Addons ({{ $addons->count() }})
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($addons as $wishlist)
                    @php
                        $item = $wishlist->wishable;
                        $image = $item->images[0] ?? null;
                        $name = $item->addons;
                        $description = $item->desc;
                        $price = $item->final_price;
                    @endphp
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden hover:shadow-xl transition-shadow duration-300">
                        <!-- Image -->
                        <div class="relative">
                            @if($image)
                                <img src="{{ asset('storage/' . $image) }}" alt="{{ $name }}" class="w-full h-48 object-cover">
                            @else
                                <div class="w-full h-48 bg-gradient-to-r from-orange-400 to-red-500 flex items-center justify-center">
                                    <i class="fas fa-plus-circle text-white text-3xl"></i>
                                </div>
                            @endif

                            <!-- Remove Button -->
                            <button onclick="removeFromWishlist('addon', '{{ $item->id }}')"
                                    class="absolute top-3 right-3 bg-red-500 text-white p-2 rounded-full hover:bg-red-600 transition-colors duration-200">
                                <i class="fas fa-times text-sm"></i>
                            </button>

                            <!-- Type Badge -->
                            <div class="absolute top-3 left-3">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800">
                                    Addon
                                </span>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="p-6">
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ $name }}</h3>

                            <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ Str::limit($description, 100) }}</p>

                            <!-- Price and Rating -->
                            <div class="flex items-center justify-between mb-4">
                                @if($item->finalPrice < $item->basic_price)
                                    <div class="text-lg font-bold text-orange-600">
                                        <span class="text-sm text-gray-500 line-through">Rp {{ number_format($item->basic_price, 0, ',', '.') }}</span>
                                        Rp {{ number_format($item->finalPrice, 0, ',', '.') }}
                                    </div>
                                @else
                                    <div class="text-lg font-bold text-orange-600">Rp {{ number_format($item->finalPrice, 0, ',', '.') }}</div>
                                @endif

                                <!-- Rating -->
                                <div class="flex items-center space-x-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= floor($item->averageRating()))
                                            <i class="fas fa-star text-yellow-400 text-sm"></i>
                                        @elseif($i - 0.5 <= $item->averageRating())
                                            <i class="fas fa-star-half-alt text-yellow-400 text-sm"></i>
                                        @else
                                            <i class="far fa-star text-yellow-400 text-sm"></i>
                                        @endif
                                    @endfor
                                    <span class="text-sm text-gray-600 ml-1">({{ $item->reviews->count() }})</span>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex space-x-2">
                                <a href="{{ route('user.addon_detail', $item->id) }}" class="flex-1 bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg font-semibold transition-colors duration-200 text-center text-sm">
                                    View Details
                                </a>
                                <a href="{{ route('user.form_booker', ['addon' => $item->id]) }}" class="flex-1 bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg font-semibold transition-colors duration-200 text-center text-sm">
                                    Book Now
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    @else
        <!-- Empty State -->
        <div class="text-center py-16">
            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-heart text-gray-400 text-3xl"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Your wishlist is empty</h3>
            <p class="text-gray-600 mb-8">Start exploring and save items you like for later</p>
            <a href="{{ route('user.home') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors duration-200">
                <i class="fas fa-search mr-2"></i>
                Explore Items
            </a>
        </div>
    @endif
</div>

<script>
function removeFromWishlist(type, id) {
    window.showConfirm('Hapus dari Wishlist?', 'Apakah Anda yakin ingin menghapus item ini dari wishlist Anda?').then(result => {
        if (result.isConfirmed) {
            fetch(`{{ url('/wishlist/remove') }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    type: type,
                    id: id
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.showSuccess('Berhasil!', 'Item berhasil dihapus dari wishlist');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    window.showError('Gagal!', 'Gagal menghapus item dari wishlist');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                window.showError('Error!', 'Terjadi kesalahan saat menghapus item');
            });
        }
    });
}

function clearAllWishlist() {
    window.showConfirm('Hapus Semua Wishlist?', 'Apakah Anda yakin ingin menghapus semua item dari wishlist Anda? Tindakan ini tidak dapat dibatalkan.').then(result => {
        if (result.isConfirmed) {
            fetch(`{{ url('/wishlist/clear-all') }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.showSuccess('Berhasil!', 'Wishlist berhasil dikosongkan');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    window.showError('Gagal!', 'Gagal mengosongkan wishlist');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                window.showError('Error!', 'Terjadi kesalahan saat mengosongkan wishlist');
            });
        }
    });
}
</script>
@endsection
