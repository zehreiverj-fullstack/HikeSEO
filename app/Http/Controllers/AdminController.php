<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminRequest;
use App\Http\Requests\ApproveRequest;
use App\Http\Requests\DisableRequest;
use App\Http\Requests\UpcomingRequest;
use App\Models\Booking;
use App\Models\DisabledDate;
use App\Models\User;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Uid\Ulid;

class AdminController extends Controller
{
    public function index()
    {
        $admin = User::where('name', 'admin')->first();
        return inertia('Admin', [
            'upcomingBookings' => $this->getUpcoming(),
            'pendingBookings' => $this->getPending(),
            'admin' => $admin
        ]);
    }

    public function register(AdminRequest $request)
    {
        $input = $request->only(['emailAddress', 'phoneNumber']);

        $user = User::where('name', 'admin')->first();
        if ($user) return to_route('admin.index')->with('message', 'Email taken')->with('isSuccess', false);

        $user = User::create([
            'id' => Uuid::uuid4()->toString(),
            'name' => 'admin',
            'email_address' => $input['emailAddress'],
            'phone_number' => $input['phoneNumber'],
        ]);

        $user->save();

        return to_route('admin.index')->with('message', 'Registered successfully')->with('isSuccess', true);
    }

    public function getUpcomingBookings(UpcomingRequest $request)
    {
        $date = $request->input('date');

        $upcoming_bookings = $date ? Booking::where([
            ['booked_on', $date],
            ['is_pending', false],
            ['is_confirmed', true],
            ['is_cancelled', false],
        ])->get() : self::getUpcoming();

        $admin = User::where('name', 'admin')->first();
        return inertia('Admin', [
            'upcomingBookings' => $upcoming_bookings,
            'pendingBookings' => $this->getPending(),
            'admin' => $admin

        ]);
    }

    public function getPendingBookings()
    {
        $admin = User::where('name', 'admin')->first();
        return inertia('Admin', [
            'upcomingBookings' => $this->getUpcoming(),
            'pendingBookings' => $this->getPending(),
            'admin' => $admin
        ]);
    }

    public function disableDate(DisableRequest $request)
    {
        $date = $request->input('date');
        DisabledDate::create(['id' => Ulid::generate(), 'date' => $date]);
        return to_route('admin.index')->with('message', 'Date disabled successfully');
    }

    public function approveBooking(ApproveRequest $request)
    {
        $bookingId = $request->input('bookingId');
        $booking = Booking::find($bookingId);
        if (!$booking) return to_route('admin.index')->with('message', 'Booking not found');
        $booking->admin_confirmed = true;
        if($booking->user_confirmed) {
            $booking->is_pending = false;
            $booking->is_confirmed = true;
        }
        $booking->save();
        return to_route('admin.index')->with('message', 'Booking approved successfully');
    }

    private static function getPending()
    {
        return Booking::where([
            ['is_pending', true],
            ['is_confirmed', false],
            ['is_cancelled', false],
        ])->get();
    }
    private static function getUpcoming()
    {
        return Booking::where([
            ['is_pending', false],
            ['is_confirmed', true],
            ['is_cancelled', false],
        ])->get();
    }
}
