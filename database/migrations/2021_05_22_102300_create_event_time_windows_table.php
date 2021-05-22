<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventTimeWindowsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_time_windows', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('schedule_event_id');
            $table->foreign('schedule_event_id')->references('id')->on('schedulable_events');

            $table->string('event_days', 14)->comment('1-Monday, 2-Tuesday, 3-Wednesday, 4-Thursday, 5-Friday,6-Saturday and Sunday');
            $table->time('start_time');
            $table->time('end_time');
            $table->boolean('is_window_available')->comment('1-yes available, 0-not available');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('event_time_windows');
    }
}
