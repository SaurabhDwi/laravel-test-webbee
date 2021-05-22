<?php

namespace App\Services;

use App\Models\EventBooking;
use App\Models\SchedulableEvent;
use App\Traits\ResponseTrait;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;

class BookingService
{
    use ResponseTrait;

    public function getAvailableWindows($eventId, $bookingDate)
    {
        try {

            $getEventDay = date('N', strtotime($bookingDate));
            $getEventWindows = SchedulableEvent::getAvailableWindow($eventId, $getEventDay);
            if ($getEventWindows) {

                /**
                 * Check Requested is available for booking or not
                 */
                $futureDateAllowed = Carbon::now()->addDay($getEventWindows['advance_booking'])->format('Y-m-d');
                if ($futureDateAllowed < $bookingDate) {
                    return $this->errorResponse(__('message.booking.futurebookingerror'), 422);
                }
                /**
                 * get all available event.
                 */
                $getEventWindows = $this->createWindowSlots($getEventWindows['id'], $bookingDate, $getEventDay, $getEventWindows['duration'], $getEventWindows['max_booking'], $getEventWindows['slot_windows']);
            }

            return $this->successResponse($getEventWindows, __('message.booking.data'));
        } catch (Exception $ex) {
            logger($ex);
            return $this->errorResponse();
        }
    }

    /**
     * Function will create all possible available slot for the user.
     *
     * @param integer $schedulableEventId
     * @param string $bookingDate
     * @param integer $day
     * @param integer $duration
     * @param integer $maxBooking
     * @param array $windowSlots
     * @return array
     */
    public function createWindowSlots(int $schedulableEventId, $bookingDate, int $day, int $duration, int $maxBooking, array $windowSlots): array
    {
        $availableWindowSlots = [];

        foreach ($windowSlots as $windowSlot) {

            /**
             * Selected day will be present in available in window slot
             */
            if ($windowSlot['is_window_available'] && in_array($day, $windowSlot['event_days'])) {

                /**
                 * Submitted day available for slot
                 * Now create window time frame
                 */

                $slotToSkip = $this->getUnAvailableStartEndTime($windowSlots);
                $windowAvailableSlots = $this->splitWindowFrame($windowSlot['start_time'], $windowSlot['end_time'], $duration, $slotToSkip);

                $availableWindowSlots = array_filter($windowAvailableSlots, function ($slot) use ($schedulableEventId, $maxBooking, $bookingDate) {
                    return $this->checkWindowSlotCanBeBooked($schedulableEventId, $slot, $bookingDate, $maxBooking);
                });
                $availableWindowSlots = array_values($availableWindowSlots);
            }
        }
        return $availableWindowSlots;
    }

    public function getUnAvailableStartEndTime(array $windowSlots): array
    {
        $times = [];
        foreach ($windowSlots as $windowSlot) {
            if ($windowSlot['is_window_available'] == false) {
                $times['startTime'] = $windowSlot['start_time'];
                $times['endTime'] = $windowSlot['end_time'];
            }
        }
        return $times;
    }

    public function splitWindowFrame($startTime, $endTime, $duration = 15, $slotToSkip = []): array
    {
        $timeFrames = [];
        $startTime = strtotime($startTime);
        $endTime = strtotime($endTime);
        /**
         * Convert into min.
         */
        $duration = $duration * 60;

        while ($startTime <= $endTime) {

            $timeSlot = date("G:i", $startTime);
            $startTime += $duration;

            if (time() > $startTime) {
                /**
                 * Past time will skipped from slot.
                 */
                continue;
            }

            if ($slotToSkip) {
                /**
                 * If slot is available to skip then skip it.
                 */
                if (strtotime($slotToSkip['startTime']) <= $startTime && strtotime($slotToSkip['endTime']) > $startTime) {
                    continue;
                }
            }
            $timeFrames[] = $timeSlot;

        }
        return $timeFrames;
    }

    public function checkWindowSlotCanBeBooked(int $schedulableEventId, string $slot, $bookingdate, int $maxBooking): bool
    {
        $bookedSlotCount = EventBooking::where(['schedule_event_id' => $schedulableEventId, 'booking_date' => $bookingdate, 'start_time' => $slot])->count('id');
        if ($bookedSlotCount >= $maxBooking) {
            return false;
        }
        return true;
    }

    /**
     * This function will create booking.
     *
     * Function logic:
     * First checked requested slot is available to book.
     * If slot available then booked that slot for user or return error message.
     *
     * @param array $request
     * @return json
     */
    public function createBooking($eventId, array $request)
    {
        try {
            $bookingDate = $request['date'];
            $getEventDay = date('N', strtotime($bookingDate));
            $getEventWindows = SchedulableEvent::getAvailableWindow($eventId, $getEventDay);
            if ($getEventWindows) {

                $futureDateAllowed = Carbon::now()->addDay($getEventWindows['advance_booking'])->format('Y-m-d');
                if ($futureDateAllowed < $bookingDate) {
                    return $this->errorResponse(__('message.booking.futurebookingerror'), 422);
                }

                if (time() > strtotime($request['slot'])) {
                    return $this->errorResponse(__('message.booking.slotexpired'), 422);
                }

                $availableEventWindows = $this->createWindowSlots($getEventWindows['id'], $bookingDate, $getEventDay, $getEventWindows['duration'], $getEventWindows['max_booking'], $getEventWindows['slot_windows']);
                if (in_array($request['slot'], $availableEventWindows)) {

                    $endTime = date('Y-m-d H:i', strtotime("+{$getEventWindows['duration']} minutes", strtotime($request['slot'])));
                    EventBooking::create([
                        'first_name' => $request['first_name'],
                        'last_name' => $request['last_name'],
                        'email' => $request['email'],
                        'schedule_event_id' => $eventId,
                        'booking_date' => $request['date'],
                        'start_time' => $request['slot'],
                        'end_time' => $endTime,
                    ]);
                    return $this->successResponse([], __('message.booking.confirmed'));
                }
                return $this->errorResponse(__('message.booking.slotnotavailable'), 422);
            }

        } catch (Exception $ex) {
            logger($ex);
            return $this->errorResponse();
        }
    }

}
