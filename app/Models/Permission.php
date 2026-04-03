<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends BaseModel
{
    protected static bool $logEnabled = true;
    protected $fillable = ['name'];
    protected $attributes = [
        'guard_name' => 'web',
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }
}
