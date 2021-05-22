<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchedulableEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schedulable_events', function (Blueprint $table) {
            $table->id();
            $table->string('abbr')->comment('abbreviation as title');
            $table->unsignedInteger('max_booking')->comment('per slot');
            $table->unsignedSmallInteger('duration')->comment('in minutes');
            $table->unsignedSmallInteger('prepartion_time')->comment('in minutes');
            $table->unsignedSmallInteger('advance_booking')->comment('in days');
            $table->unsignedTinyInteger('status')->comment('1-Active, 2-inactive');
            $table->softDeletes();
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
        Schema::dropIfExists('schedulable_events');
    }
}
