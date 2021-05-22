<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventTimeWindow extends Model
{
    use HasFactory;

    const AVAILABLE = true;
    const NOT_AVAILABLE = false;

    protected $fillable = [
        'schedule_event_id',
        'event_days',
        'start_time',
        'end_time',
        'is_window_available',
    ];

    protected $casts = [
        'is_window_available' => 'bool',
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function getEventDaysAttribute($value)
    {
        if ($value) {
            return explode(',', $value);
        }
        return [];
    }
}
