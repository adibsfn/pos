<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;
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

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function satuan()
    {
        return $this->belongsTo(Satuan::class);
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
