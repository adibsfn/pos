<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Product;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use BackedEnum;
use Filament\Support\Icons\Heroicon;

class POS extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static ?string $navigationLabel = 'POS';
    protected static ?string $title = 'POS';
    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.pos';

    public $products = [];
    public $categories = [];
    public $cart = [];
    public $discount = 0;
    public $tax = 0;

    public function mount()
    {
        $this->categories = Category::select('id', 'nama')->get()->toArray();

        $this->products = Product::where('is_active', true)
            ->get()
            ->map(fn ($p) => [
                'id' => $p->id,
                'nama' => $p->nama,
                'harga_jual' => (int) $p->harga_jual,
                'stock' => $p->stock,
                'category_id' => $p->category_id,
                'image' => $p->image ? asset('storage/' . $p->image) : '/images/no-image.png',
            ])
            ->values()
            ->toArray();
    }

    public function checkout()
    {
        DB::transaction(function () {

            $subtotal = collect($this->cart)->sum(fn($i) => $i['qty'] * $i['price']);
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
                    'price' => $item['price'],
                    'total' => $item['qty'] * $item['price'],
                ]);
            }
        });

        $this->cart = [];
        $this->discount = 0;
        $this->tax = 0;
    }
}
