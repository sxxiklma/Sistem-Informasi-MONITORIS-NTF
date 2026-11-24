<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Product::where('is_active', true)->latest();

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%");
            });
        }

        if ($category = $request->get('category')) {
            if ($category !== 'semua') {
                $query->where('category', $category);
            }
        }

        $products = $query->get()->map(fn ($p) => [
            'id' => $p->id,
            'sku' => $p->sku,
            'name' => $p->name,
            'category' => $p->category ?? 'Umum',
            'unit' => $p->unit ?? 'pcs',
            'image' => $p->image,
            'image_url' => $p->image_url,
            'price' => (float) $p->price,
            'formatted_price' => 'Rp '.number_format($p->price, 0, ',', '.'),
            'stock_on_hand' => (int) $p->stock_on_hand,
            'stock_carried' => (int) $p->stock_carried,
            'is_active' => (bool) $p->is_active,
        ]);

        $categories = Product::where('is_active', true)
            ->distinct()
            ->pluck('category')
            ->filter()
            ->values();

        return response()->json([
            'success' => true,
            'total' => $products->count(),
            'categories' => $categories,
            'products' => $products,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'unit' => ['required', 'string', 'max:50'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock_on_hand' => ['required', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $sku = $request->input('sku') ?: ('PRD-'.strtoupper(substr(uniqid(), -6)));
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        $product = Product::create([
            ...$validated,
            'sku' => $sku,
            'image' => $imagePath,
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Produk {$product->name} berhasil ditambahkan.",
            'product' => $product,
        ]);
    }

    public function update(Request $request, Product $product): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'category' => ['sometimes', 'string', 'max:100'],
            'unit' => ['sometimes', 'string', 'max:50'],
            'price' => ['sometimes', 'numeric', 'min:0'],
            'stock_on_hand' => ['sometimes', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        if ($request->hasFile('image')) {
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($validated);

        return response()->json([
            'success' => true,
            'message' => "Produk {$product->name} berhasil diperbarui.",
            'product' => $product,
        ]);
    }
}
