<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pelanggan extends BaseModel
{
    /** @use HasFactory<\Database\Factories\PelangganFactory> */
    use HasFactory;
    protected static bool $logEnabled = true;
    protected $fillable = [
        'nama',
        'telepon',
        'email',
        'alamat',
    ];
}
