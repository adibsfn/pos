<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;

class BaseModel extends Model
{
    use LogsActivity;

    protected static bool $logEnabled = false;

    public function getActivitylogOptions(): LogOptions
    {
        if (!static::$logEnabled) {
            return LogOptions::defaults()
                ->logOnly([]);
        }

        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn (string $eventName) => class_basename($this) . " {$eventName}");
    }

        public function tapActivity(Activity $activity, string $eventName)
    {
        $activity->properties = $activity->properties->merge([
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
