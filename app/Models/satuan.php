<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class satuan extends BaseModel
{
    /** @use HasFactory<\Database\Factories\SatuanFactory> */
    use HasFactory;
    protected static bool $logEnabled = true;

    protected $fillable = [
        'nama',
        'deskripsi',
    ];
}
