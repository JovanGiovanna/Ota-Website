<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use App\Models\Package;
use App\Models\Product;
use App\Models\Addon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $wishlists = Auth::user()->wishlists()->with('wishable')->get();

        return view('user.wishlist', compact('wishlists'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'type' => 'required|in:package,product,addon',
            'id' => 'required|string',
        ]);

        $user = Auth::user();
        $type = $request->type;
        $id = $request->id;

        // Check if item exists
        $model = null;
        switch ($type) {
            case 'package':
                $model = Package::find($id);
                break;
            case 'product':
                $model = Product::find($id);
                break;
            case 'addon':
                $model = Addon::find($id);
                break;
        }

        if (!$model) {
            return response()->json(['success' => false, 'message' => 'Item not found']);
        }

        // Check if already in wishlist
        $exists = Wishlist::where('user_id', $user->id)
            ->where('wishable_type', get_class($model))
            ->where('wishable_id', $id)
            ->exists();

        if ($exists) {
            return response()->json(['success' => false, 'message' => 'Item already in wishlist']);
        }

        // Add to wishlist
        Wishlist::create([
            'user_id' => $user->id,
            'wishable_type' => get_class($model),
            'wishable_id' => $id,
        ]);

        return response()->json(['success' => true, 'message' => 'Added to wishlist']);
    }

    public function remove(Request $request)
    {
        $request->validate([
            'type' => 'required|in:package,product,addon',
            'id' => 'required|string',
        ]);

        $user = Auth::user();
        $type = $request->type;
        $id = $request->id;

        // Get the model class
        $modelClass = null;
        switch ($type) {
            case 'package':
                $modelClass = Package::class;
                break;
            case 'product':
                $modelClass = Product::class;
                break;
            case 'addon':
                $modelClass = Addon::class;
                break;
        }

        // Remove from wishlist
        Wishlist::where('user_id', $user->id)
            ->where('wishable_type', $modelClass)
            ->where('wishable_id', $id)
            ->delete();

        return response()->json(['success' => true, 'message' => 'Removed from wishlist']);
    }

    public function toggle(Request $request)
    {
        $request->validate([
            'type' => 'required|in:package,product,addon',
            'id' => 'required|string',
        ]);

        $user = Auth::user();
        $type = $request->type;
        $id = $request->id;

        // Get the model class
        $modelClass = null;
        switch ($type) {
            case 'package':
                $modelClass = Package::class;
                break;
            case 'product':
                $modelClass = Product::class;
                break;
            case 'addon':
                $modelClass = Addon::class;
                break;
        }

        // Check if exists
        $exists = Wishlist::where('user_id', $user->id)
            ->where('wishable_type', $modelClass)
            ->where('wishable_id', $id)
            ->exists();

        if ($exists) {
            // Remove
            Wishlist::where('user_id', $user->id)
                ->where('wishable_type', $modelClass)
                ->where('wishable_id', $id)
                ->delete();
            return response()->json(['success' => true, 'action' => 'removed', 'message' => 'Removed from wishlist']);
        } else {
            // Add
            Wishlist::create([
                'user_id' => $user->id,
                'wishable_type' => $modelClass,
                'wishable_id' => $id,
            ]);
            return response()->json(['success' => true, 'action' => 'added', 'message' => 'Added to wishlist']);
        }
    }

    public function clearAll(Request $request)
    {
        $user = Auth::user();

        // Delete all wishlist items for the current user
        Wishlist::where('user_id', $user->id)->delete();

        return response()->json(['success' => true, 'message' => 'All items removed from wishlist']);
    }
}
