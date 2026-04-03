<?php

namespace App\Models;

use Spatie\Activitylog\Models\Activity as SpatieActivity;

class Activity extends SpatieActivity
{
    protected $table = 'activity_log';

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'causer_id');
    }
}
