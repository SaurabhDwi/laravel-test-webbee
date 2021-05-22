<?php

namespace App\Http\Controllers;

use App\Http\Requests\Appointment\AvailableSchedulableEventRequest;
use App\Http\Requests\Appointment\SaveBookingEventRequest;
use App\Services\BookingService;

class SchedulableEventController extends Controller
{
    private $bookingServiceObj;
    public function __construct(BookingService $bookingServiceObj)
    {
        $this->bookingServiceObj = $bookingServiceObj;
    }
    public function returnAvilableBookingSlot($eventId, AvailableSchedulableEventRequest $scReq)
    {
        return $this->bookingServiceObj->getAvailableWindows($eventId, $scReq->input('date'));
    }

    public function saveUserBooking($eventId, SaveBookingEventRequest $saveReq)
    {
        return $this->bookingServiceObj->createBooking($eventId, $saveReq->all());
    }
}
