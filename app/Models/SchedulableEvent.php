<?php

namespace App\Models;

use App\Models\EventTimeWindow;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchedulableEvent extends Model
{
    const ACTIVE = 1;
    use HasFactory;

    protected $fillable = [
        'abbr',
        'max_booking',
        'max_participant',
        'duration',
        'prepartion_time',
        'advance_booking',
        'status',
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function availableSlots()
    {
        return $this->hasMany(EventTimeWindow::class)->where('is_window_available', EventTimeWindow::AVAILABLE);
    }

    public function unAvailableSlots()
    {
        return $this->hasMany(EventTimeWindow::class)->where('is_window_available', EventTimeWindow::NOT_AVAILABLE);
    }

    public function slotWindows()
    {
        return $this->hasMany(EventTimeWindow::class, 'schedule_event_id', 'id');
    }

    public static function getAvailableWindow($id, $day)
    {
        $event = self::with('slotWindows')
            ->join('event_time_windows', 'schedulable_events.id', 'event_time_windows.schedule_event_id')
            ->where('schedulable_events.id', $id)
            ->whereRaw("FIND_IN_SET($day,event_time_windows.event_days)")
            ->where([
                'event_time_windows.is_window_available' => EventTimeWindow::AVAILABLE,
                'schedulable_events.status' => self::ACTIVE,
            ])
            ->first();
        if ($event) {
            return $event->toArray();
        }
        return [];
    }
}
