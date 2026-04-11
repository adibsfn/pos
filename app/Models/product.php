<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class product extends BaseModel
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;
    protected static bool $logEnabled = true;

    protected $fillable = [
        'nama',
        'category_id',
        'satuan_id',
        'deskripsi',
        'barcode',
        'image',
        'harga_beli',
        'harga_jual',
        'stock',
        'minimum_stock',
        'is_active',
    ];

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id');
    }
    public function satuan()
    {
        return $this->belongsTo(Satuan::class);
    }
    public function transactionitems()
    {
        return $this->hasMany(TransactionItem::class);
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    //delete old image when updating or deleting product
    protected static function booted()
    {
        static::deleting(function ($product) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
        });

        static::updating(function ($product) {
            if ($product->isDirty('image')) {
                $old = $product->getOriginal('image');

                if ($old) {
                    Storage::disk('public')->delete($old);
                }
            }
        });
    }
}
