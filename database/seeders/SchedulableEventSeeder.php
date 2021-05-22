<?php

namespace Database\Seeders;

use App\Models\EventTimeWindow;
use App\Models\SchedulableEvent;
use Illuminate\Database\Seeder;

class SchedulableEventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $event1 = SchedulableEvent::create([
            'abbr' => 'Laravel convention 2020',
            'max_booking' => 3,
            'duration' => 30,
            'prepartion_time' => 5,
            'advance_booking' => 10,
            'status' => SchedulableEvent::ACTIVE,
        ]);

        EventTimeWindow::create([
            'schedule_event_id' => $event1->id,
            'event_days' => '1,2,4,6',
            'start_time' => '12:00:00',
            'end_time' => '20:00:00',
            'is_window_available' => EventTimeWindow::AVAILABLE,
        ]);

        EventTimeWindow::create([
            'schedule_event_id' => $event1->id,
            'event_days' => '1,2,4,6',
            'start_time' => '14:00:00',
            'end_time' => '16:00:00',
            'is_window_available' => EventTimeWindow::NOT_AVAILABLE,
        ]);

        $event2 = SchedulableEvent::create([
            'abbr' => 'PHP convention 2021',
            'max_booking' => 4,
            'duration' => 20,
            'prepartion_time' => 10,
            'advance_booking' => 5,
            'status' => SchedulableEvent::ACTIVE,
        ]);

        EventTimeWindow::create([
            'schedule_event_id' => $event2->id,
            'event_days' => '3,5',
            'start_time' => '14:00:00',
            'end_time' => '18:00:00',
            'is_window_available' => EventTimeWindow::NOT_AVAILABLE,
        ]);

    }
}
