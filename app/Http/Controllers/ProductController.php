<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Helpers\SweetAlert;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'vendor']);

        if ($request->has('city_id') && $request->city_id) {
            $query->whereHas('vendor.vendorInfo', function ($q) use ($request) {
                $q->where('id_city', $request->city_id);
            });
        }

        if ($request->has('vendor_id') && $request->vendor_id) {
            $query->where('id_vendor', $request->vendor_id);
        }

        $products = $query->get();
        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    public function show($id)
    {
        $product = Product::with(['category', 'vendor'])->find($id);
        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Product not found'], 404);
        }
        return response()->json(['success' => true, 'data' => $product]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'description' => 'required|string',
            'location' => 'required|string',
            'address' => 'nullable|string',
            'phone' => 'nullable|string',
            'basic_price' => 'required|numeric|min:0',
            'nta' => 'required|numeric|min:0',
            'upsale' => 'nullable|numeric|min:0',
            'discount_type' => 'nullable|in:percentage,fixed',
            'discount_value' => 'nullable|numeric|min:0',
            'discount_expires_at' => 'nullable|date',
            'id_category' => 'required|exists:categories,id',
            'id_vendor' => 'required|exists:vendor,id',
            'pax' => 'required|integer|min:1',
            'jumlah' => 'required|integer|min:0',
            'status' => 'required|in:available,unavailable,draft,publish',
        ]);

        if ($validator->fails()) {
            if (function_exists('alert')) {
                alert()->error('Validation Failed', 'Mohon periksa kembali form Anda');
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $data = $request->all();
            $imagePaths = [];

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $file) {
                    $imageName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $file->storeAs('images', $imageName, 'public');
                    $imagePaths[] = 'images/' . $imageName;
                }
            }

            $data['images'] = !empty($imagePaths) ? $imagePaths : null;

            // Calculate discount_amount using NTA + upsale
            $data['discount_amount'] = $this->calculateDiscountAmount(
                $data['nta'] ?? ($data['basic_price'] ?? 0),
                $data['upsale'] ?? 0,
                $data['discount_type'] ?? null,
                $data['discount_value'] ?? null,
                $data['discount_expires_at'] ?? null
            );

            $product = Product::create($data);

            if (function_exists('alert')) {
                alert()->success('Success', 'Produk berhasil dibuat');
            }
            return redirect()->route('products.index');
        } catch (\Exception $e) {
            if (function_exists('alert')) {
                alert()->error('Error', 'Produk gagal dibuat: ' . $e->getMessage());
            }
            return redirect()->back()
                ->withInput();
        }
    }

    public function update(Request $request, $id)
    {
        $product = Product::find($id);
        if (!$product) {
            if (function_exists('alert')) {
                alert()->error('Error', 'Produk tidak ditemukan');
            }
            return redirect()->back();
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'description' => 'required|string',
            'location' => 'required|string',
            'address' => 'nullable|string',
            'phone' => 'nullable|string',
            'basic_price' => 'required|numeric|min:0',
            'nta' => 'required|numeric|min:0',
            'upsale' => 'nullable|numeric|min:0',
            'discount_type' => 'nullable|in:percentage,fixed',
            'discount_value' => 'nullable|numeric|min:0',
            'discount_expires_at' => 'nullable|date',
            'id_category' => 'required|exists:categories,id',
            'id_vendor' => 'required|exists:vendor,id',
            'pax' => 'required|integer|min:1',
            'jumlah' => 'required|integer|min:0',
            'status' => 'required|in:available,unavailable,draft,publish',
        ]);

        if ($validator->fails()) {
            if (function_exists('alert')) {
                alert()->error('Validation Failed', 'Mohon periksa kembali form Anda');
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $data = $request->all();
            $oldImages = $product->images ?? [];
            $imagePaths = is_array($oldImages) ? $oldImages : [];

            // Handle image removal
            if ($request->has('remove_images') && is_array($request->remove_images)) {
                foreach ($request->remove_images as $index) {
                    if (isset($imagePaths[$index])) {
                        $imageToRemove = $imagePaths[$index];
                        $path = str_replace('storage/', '', $imageToRemove);
                        if (Storage::disk('public')->exists($path)) {
                            Storage::disk('public')->delete($path);
                        }
                        unset($imagePaths[$index]);
                    }
                }
                // Reindex array
                $imagePaths = array_values($imagePaths);
            }

            // Handle new image uploads
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $file) {
                    $imageName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $file->storeAs('images', $imageName, 'public');
                    $imagePaths[] = 'images/' . $imageName;
                }
            }

            // Update image field only if there are changes
            if ($request->hasFile('images') || $request->has('remove_images')) {
                $data['images'] = !empty($imagePaths) ? $imagePaths : null;
            } else {
                unset($data['images']);
            }

            // Calculate discount_amount using NTA + upsale
            $data['discount_amount'] = $this->calculateDiscountAmount(
                $data['nta'] ?? $product->nta ?? $product->basic_price,
                $data['upsale'] ?? $product->upsale ?? 0,
                $data['discount_type'] ?? $product->discount_type,
                $data['discount_value'] ?? $product->discount_value,
                $data['discount_expires_at'] ?? $product->discount_expires_at
            );

            $product->update($data);

            if (function_exists('alert')) {
                alert()->success('Success', 'Produk berhasil diperbarui');
            }
            return redirect()->route('products.index');
        } catch (\Exception $e) {
            if (function_exists('alert')) {
                alert()->error('Error', 'Produk gagal diperbarui: ' . $e->getMessage());
            }
            return redirect()->back()
                ->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $product = Product::find($id);
            if (!$product) {
                if (function_exists('alert')) {
                    alert()->error('Error', 'Produk tidak ditemukan');
                }
                return redirect()->back();
            }

            if ($product->images && is_array($product->images)) {
                foreach ($product->images as $image) {
                    $path = str_replace('storage/', '', $image);
                    if (Storage::disk('public')->exists($path)) {
                        Storage::disk('public')->delete($path);
                    }
                }
            }

            $product->delete();

            if (function_exists('alert')) {
                alert()->success('Success', 'Produk berhasil dihapus');
            }
            return redirect()->route('products.index');
        } catch (\Exception $e) {
            if (function_exists('alert')) {
                alert()->error('Error', 'Produk gagal dihapus: ' . $e->getMessage());
            }
            return redirect()->back();
        }
    }

    /**
     * Calculate discount amount based on discount type and value
     */
    private function calculateDiscountAmount($nta, $upsale, $discountType, $discountValue, $expiresAt)
    {
        // Check if discount is expired
        if ($expiresAt && \Carbon\Carbon::parse($expiresAt)->isPast()) {
            return 0.00;
        }

        // Calculate price before discount: NTA + Upsale
        $priceBeforeDiscount = (float) $nta + (float) $upsale;

        $discountAmount = 0.00;

        if ($discountType === 'percentage' && $discountValue > 0) {
            // Percentage discount
            $discountAmount = $priceBeforeDiscount * ($discountValue / 100);
        } elseif ($discountType === 'fixed' && $discountValue > 0) {
            // Fixed discount
            $discountAmount = $discountValue;
        }

        // Ensure discount amount is not negative
        return max(0, round($discountAmount, 2));
    }

    public function showDetail($id)
    {
        $product = Product::with(['category', 'vendor.vendorInfo', 'reviews.user'])->find($id);
        if (!$product) {
            abort(404, 'Product not found');
        }

        $isInWishlist = false;
        if (Auth::check()) {
            $isInWishlist = Auth::user()->wishlists()
                ->where('wishable_type', Product::class)
                ->where('wishable_id', $id)
                ->exists();
        }

        return view('user.product_detail', compact('product', 'isInWishlist'));
    }
}
