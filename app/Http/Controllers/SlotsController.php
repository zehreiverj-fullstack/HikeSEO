<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookRequest;
use App\Models\Booking;
use App\Models\Customer;
use App\Utilities\SlotsHelper;
use Inertia\Inertia;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Uid\Ulid;

class SlotsController extends Controller
{

    public function index()
    {
        return  inertia('Slots', [
            'slots' => SlotsHelper::getSlots(),
        ]);
    }

    public function book(BookRequest $request)
    {
        $input = $request->only(['name', 'emailAddress', 'phoneNumber', 'vehicleMake', 'vehicleModel', 'bookOn', 'slot']);


        $customer = Customer::where([
            ['email_address', '=', $input['emailAddress']],
        ]);
        if (!$customer->exists()) {
            $customer = Customer::create([
                'id' => Uuid::uuid4()->toString(),
                'name' => $input['name'],
                'email_address' => $input['emailAddress'],
                'phone_number' => $input['phoneNumber'],
            ]);
        }

        $bookingFromDb = Booking::where([
            ['booked_on', '=', $input['bookOn']],
            ['started_at', '=', $input['slot'][0]],
            ['ended_at', '=', $input['slot'][1]],
        ]);
        if ($bookingFromDb->exists()) {
            return to_route('slots.index')->with('message', 'Slot is booked already');
        };

        $booking = Booking::create([
            'id' => Ulid::generate(),
            'customer_id' => $customer->id,
            'vehicle_make' => $input['vehicleMake'],
            'vehicle_model' => $input['vehicleModel'],
            'booked_on' => $input['bookOn'],
            'started_at' => $input['slot'][0],
            'ended_at' => $input['slot'][1],
        ]);

        $customer->save();
        $booking->save();

        return to_route('slots.index')->with('message', 'Booked slot successfully');
    }
}
