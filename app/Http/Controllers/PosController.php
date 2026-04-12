<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;

class PosController extends Controller
{
    public function index()
    {
        $products = Product::where('is_active', 1)
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'nama' => $p->nama,
                    'minimum_stock' => $p->minimum_stock,
                    'stock' => $p->stock,
                    'harga_jual' => $p->harga_jual,
                    'category_id' => $p->category_id,
                    'image' => $p->image
                        ? asset('storage/' . $p->image)
                        : asset('images/no-image.png'),
                ];
            });

        $categories = Category::select('id', 'nama')
            ->selectRaw('
                (SELECT COUNT(*)
                FROM products
                WHERE products.category_id = categories.id
                AND products.is_active = 1
            ) as products_count
            ')
            ->get()
            ->map(fn($cat) => [
                'id' => $cat->id,
                'nama' => $cat->nama,
                'count' => $cat->products_count
            ])
            ->values();

        return view('pos.full', compact('products', 'categories'));
    }
}
