<?php

use Illuminate\Support\Facades\Route;
use App\Models\Product;
use App\Models\Category;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/notif-count', function () {
    return request()->user()->unreadNotifications()->count();
})->middleware('auth');

Route::get('/pos-app', function () {
    return view('pos.full', [
        'products' => Product::all()->map(function ($p) {
            return [
                'id' => $p->id,
                'nama' => $p->nama,
                'stock' => $p->stock,
                'harga_jual' => $p->harga_jual,
                'category_id' => $p->category_id,
                'image' => $p->image
                    ? asset('storage/' . $p->image)
                    : asset('images/no-image.png'),
            ];
        }),
        'categories' => Category::select('id', 'nama')
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
            ->values(),
    ]);
})->name('pos.app');
