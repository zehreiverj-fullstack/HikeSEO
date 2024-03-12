<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookRequest;
use App\Mail\BookingConfirmation;
use App\Models\Booking;
use App\Models\DisabledDate;
use App\Models\User;
use App\Utilities\SlotsHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Uid\Ulid;

class SlotsController extends Controller
{

    public function index()
    {
        return inertia('Slots', [
            'slots' => SlotsHelper::getSlots(),
        ]);
    }

    public function book(BookRequest $request)
    {
        $input = $request->only(['name', 'emailAddress', 'phoneNumber', 'vehicleMake', 'vehicleModel', 'bookOn', 'slot']);

        $disabledDate = DisabledDate::where([
            ['date', '=', $input['bookOn']],
        ]);
        if ($disabledDate->exists()) return to_route('slots.index')->with('message', 'Date is disabled')->with('isSuccess', false);

        $user = User::where('email_address', $input['emailAddress'])->first();
        if (!$user) {
            $user = User::create([
                'id' => Uuid::uuid4()->toString(),
                'name' => $input['name'],
                'email_address' => $input['emailAddress'],
                'phone_number' => $input['phoneNumber'],
            ]);
        }

        $bookingFromDb = Booking::where([
            ['booked_on', $input['bookOn']],
            ['started_at', $input['slot'][0]],
            ['ended_at', $input['slot'][1]],
        ]);
        if ($bookingFromDb->exists()) {
            return to_route('slots.index')
                ->with('message', 'Slot already booked')
                ->with('isSuccess', false);
        }

        $booking = Booking::create([
            'id' => Ulid::generate(),
            'user_id' => $user->id,
            'vehicle_make' => $input['vehicleMake'],
            'vehicle_model' => $input['vehicleModel'],
            'booked_on' => $input['bookOn'],
            'started_at' => $input['slot'][0],
            'ended_at' => $input['slot'][1],
        ]);

        $user->save();
        $booking->save();

        Mail::to($user->email_address)->send(new BookingConfirmation(request()->root() . '/' . $booking->id . '/confirmation', $user->id));

        $admin = User::where('name', 'admin')->first();
        if ($admin) {
            Mail::to($admin->email_address)->send(new BookingConfirmation(request()->root() . '/' . $booking->id . '/confirmation', $admin->id));
        }

        return to_route('slots.index')
            ->with('message', 'Slot booked successfully')
            ->with('isSuccess', true);
    }

    public function confirmation(string $bookingId, string $userId)
    {
        $user = User::find($userId);
        if (!$user) return to_route('slots.index');

        $booking = Booking::find($bookingId);
        if ($user->name == 'admin') {
            $booking->admin_confirmed = true;
            if ($booking->user_confirmed) {
                $booking->is_pending = false;
                $booking->is_confirmed = true;
            }
        } else {
            $booking->user_confirmed = true;
            if ($booking->admin_confirmed) {
                $booking->is_pending = false;
                $booking->is_confirmed = true;
            }
        }

        $booking->save();
    }
}
