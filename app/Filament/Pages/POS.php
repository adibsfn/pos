<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use BackedEnum;
use Filament\Support\Icons\Heroicon;

class POS extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static ?string $navigationLabel = 'POS';
    protected static ?string $title = '';
    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.pos';

    public $products = [];
    public $categories = [];
    public $cart = [];
    public $discount = 0;
    public $tax = 0;

    public function mount()
    {
        // $this->categories = Category::select('id', 'nama')
        //     ->selectRaw('
        //         (SELECT COUNT(*)
        //         FROM products
        //         WHERE products.category_id = categories.id
        //         AND products.is_active = true) as products_count
        //     ')
        //     ->get()
        //     ->map(fn($cat) => [
        //         'id' => $cat->id,
        //         'nama' => $cat->nama,
        //         'count' => $cat->products_count
        //     ])
        //     ->values()
        //     ->toArray();

        // $this->products = Product::where('is_active', true)
        //     ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
        //     ->select([
        //         'products.id',
        //         'products.nama',
        //         'products.harga_jual',
        //         'products.stock',
        //         'products.category_id',
        //         'products.image',
        //         'categories.nama as category_nama'
        //     ])
        //     ->get()
        //     ->map(fn ($p) => [
        //         'id' => $p->id,
        //         'nama' => $p->nama,
        //         'harga_jual' => (int) $p->harga_jual,
        //         'stock' => $p->stock,
        //         'category_id' => $p->category_id,
        //         'image' => $p->image ? asset('storage/' . $p->image) : '/images/no-image.png',
        //     ])
        //     ->values()
        //     ->toArray();
    }

    public function checkout()
    {
        DB::transaction(function () {
            $subtotal = collect($this->cart)->sum(fn($i) => $i['qty'] * $i['harga_jual']);
            $taxAmount = ($this->tax / 100) * $subtotal;
            $total = $subtotal + $taxAmount - $this->discount;

            $trx = Transaction::create([
                'invoice' => 'INV-' . now()->timestamp,
                'user_id' => Auth::id(),
                'pelanggan_id' => 1,
                'subtotal' => $subtotal,
                'discount' => $this->discount,
                'tax' => $this->tax,
                'total' => $total,
            ]);

            foreach ($this->cart as $item) {
                $trx->items()->create([
                    'product_id' => $item['id'],
                    'qty' => $item['qty'],
                    'price' => $item['harga_jual'],
                    'total' => $item['qty'] * $item['harga_jual'],
                ]);
            }
        });

        $this->cart = [];
        $this->discount = 0;
        $this->tax = 0;
    }
}
