<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends BaseModel
{
    /** @use HasFactory<\Database\Factories\CategoryFactory> */
    use HasFactory;
    protected static bool $logEnabled = true;
    protected $fillable = [
        'nama',
        'deskripsi',
    ];

        public function products()
    {
        return $this->hasMany(Product::class, 'category_id');
    }
}
