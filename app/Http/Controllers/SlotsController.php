<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookRequest;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Slot;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Uid\Ulid;

class SlotsController extends Controller
{
    public function __invoke()
    {
        $start = today()->setTime(9, 0, 0);
        $end = today()->setTime(17, 30, 0);
        $slots = array();

        while ($start <= $end) {
            $slot = array();
            for ($i = 0; $i < 2; $i++) {
                $slot[] = $start->format('h:i A');
                $start = $start->addMinutes(30);
            }
            $slots[] = $slot;
        }

        return  inertia('Slots', compact('slots'));
    }

    public function book(BookRequest $request)
    {
        $input = $request->only(['name', 'emailAddress', 'phoneNumber', 'vehicleMake', 'vehicleModel', 'bookOn', 'slot']);

        $customer = Customer::create([
            'id' => Uuid::uuid4()->toString(),
            'name' => $input['name'],
            'email_address' => $input['emailAddress'],
            'phone_number' => $input['phoneNumber'],
        ]);

        $booking = Booking::create([
            'id' => Ulid::generate(),
            'customer_id' => $customer->id,
            'vehicle_make' => $input['vehicleMake'],
            'vehicle_model' => $input['vehicleModel'],
            'booked_on' => $input['bookOn'],
        ]);

        $slot = Slot::create([
            'id' => Ulid::generate(),
            'book_id' => $booking->id,
            'started_at' => $input['slot'][0],
            'ended_at' => $input['slot'][1],
        ]);

        $customer->save();
        $booking->save();
        $slot->save();

        return inertia('Slots', ['isSuccess' => true]);
    }
}
