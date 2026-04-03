<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends BaseModel
{
    /** @use HasFactory<\Database\Factories\SupplierFactory> */
    use HasFactory;
    protected static bool $logEnabled = true;
    protected $fillable = [
        'nama',
        'contact_person',
        'telepon',
        'email',
        'alamat',
    ];
}
