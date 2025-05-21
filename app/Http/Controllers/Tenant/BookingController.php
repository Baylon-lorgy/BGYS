<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    private function switchToTenantDatabase()
    {
        if (Auth::guard('tenant')->check()) {
            $tenant = Auth::guard('tenant')->user();
            Config::set('database.connections.tenant.database', $tenant->database_name);
            DB::purge('tenant');
            DB::reconnect('tenant');
        }
    }
    
    public function index()
    {
        $this->switchToTenantDatabase();
        $bookings = Booking::on('tenant')
            ->with(['room'])
            ->latest()
            ->get();
            
        // Process bookings to extract guest information from notes field when user_id is 0
        foreach ($bookings as $booking) {
            if ($booking->user_id == 0 && $booking->notes) {
                $guestInfo = json_decode($booking->notes, true);
                if ($guestInfo && isset($guestInfo['guest_name'])) {
                    $booking->user_name = $guestInfo['guest_name'];
                    $booking->user_email = $guestInfo['guest_email'] ?? '';
                } else {
                    $booking->user_name = 'Guest User';
                }
            }
        }
        
        return view('tenant.bookings.index', compact('bookings'));
    }

    public function create()
    {
        // Disabled booking creation for tenants
        return redirect()->route('tenant.bookings.index')
            ->with('error', 'Creating new bookings has been disabled.');
    }

    public function store(Request $request)
    {
        // Disabled booking creation for tenants
        return redirect()->route('tenant.bookings.index')
            ->with('error', 'Creating new bookings has been disabled.');
    }

    public function show($id)
    {
        $this->switchToTenantDatabase();
        
        $booking = Booking::on('tenant')
            ->with(['room'])
            ->findOrFail($id);
        
        // Extract guest information from notes field when user_id is 0
        if ($booking->user_id == 0 && $booking->notes) {
            $guestInfo = json_decode($booking->notes, true);
            if ($guestInfo && isset($guestInfo['guest_name'])) {
                $booking->guest_name = $guestInfo['guest_name'];
                $booking->guest_email = $guestInfo['guest_email'] ?? '';
            }
        }
        
        return view('tenant.bookings.show', compact('booking'));
    }

    public function edit($id)
    {
        $this->switchToTenantDatabase();
        
        $booking = Booking::on('tenant')
            ->findOrFail($id);
            
        // Process booking to extract guest information from notes field when user_id is 0
        if ($booking->user_id == 0 && $booking->notes) {
            $guestInfo = json_decode($booking->notes, true);
            if ($guestInfo && isset($guestInfo['guest_name'])) {
                $booking->guest_name = $guestInfo['guest_name'];
                $booking->guest_email = $guestInfo['guest_email'] ?? '';
            }
        }
            
        $rooms = Room::on('tenant')
            ->where('tenant_id', Auth::guard('tenant')->id())
            ->get();
        
        // Only get users from room_tenants if not a direct booking from public site
        if ($booking->user_id != 0) {
            $users = DB::connection('tenant')
                ->table('room_tenants')
                ->select('id as user_id', 'name')
                ->get();
        } else {
            $users = collect([
                (object) ['user_id' => 0, 'name' => $booking->guest_name ?? 'Guest User']
            ]);
        }
        
        return view('tenant.bookings.edit', compact('booking', 'rooms', 'users'));
    }

    public function update(Request $request, $id)
    {
        $this->switchToTenantDatabase();
        
        $booking = Booking::on('tenant')->findOrFail($id);

        // Different validation based on whether it's a direct booking
        if ($booking->user_id == 0) {
            $validated = $request->validate([
                'room_id' => 'required|exists:tenant.rooms,id',
                'check_in' => 'required|date',
                'guests' => 'required|integer|min:1',
                'special_requests' => 'nullable|string',
                'status' => 'required|in:pending,confirmed,cancelled,completed',
                'guest_name' => 'required|string|max:255',
                'guest_email' => 'required|email',
            ]);
            
            // Update guest info in notes
            $notes = json_encode([
                'guest_name' => $validated['guest_name'],
                'guest_email' => $validated['guest_email'],
                'booking_source' => 'public_website'
            ]);
        } else {
            $validated = $request->validate([
                'room_id' => 'required|exists:tenant.rooms,id',
                'user_id' => 'required',
                'check_in' => 'required|date',
                'guests' => 'required|integer|min:1',
                'special_requests' => 'nullable|string',
                'status' => 'required|in:pending,confirmed,cancelled,completed',
                'notes' => 'nullable|string'
            ]);
            
            $notes = $validated['notes'];
        }

        // Verify room belongs to tenant
        $room = Room::on('tenant')
            ->where('id', $validated['room_id'])
            ->where('tenant_id', Auth::guard('tenant')->id())
            ->firstOrFail();

        // Check if room is available for the selected dates (excluding current booking)
        if ($validated['status'] === 'confirmed') {
            $isAvailable = !Booking::on('tenant')
                ->where('room_id', $room->id)
                ->where('id', '!=', $booking->id)
                ->where('status', 'confirmed')
                ->where(function($query) use ($validated) {
                    $query->whereBetween('check_in', [$validated['check_in'], $validated['check_out']])
                        ->orWhereBetween('check_out', [$validated['check_in'], $validated['check_out']])
                        ->orWhere(function($q) use ($validated) {
                            $q->where('check_in', '<=', $validated['check_in'])
                              ->where('check_out', '>=', $validated['check_out']);
                        });
                })
                ->exists();

            if (!$isAvailable) {
                return back()->withErrors(['room_id' => 'This room is not available for the selected dates.'])->withInput();
            }
        }

        // Prepare data for update
        $updateData = [
            'room_id' => $validated['room_id'],
            'check_in' => $validated['check_in'],
            'guests' => $validated['guests'],
            'special_requests' => $validated['special_requests'] ?? null,
            'status' => $validated['status'],
            'notes' => $notes
        ];
        
        // Only update user_id if it's not a direct booking
        if ($booking->user_id != 0) {
            $updateData['user_id'] = $validated['user_id'];
        }

        $booking->update($updateData);

        return redirect()->route('tenant.bookings.index')
            ->with('success', 'Booking updated successfully.');
    }

    public function destroy($id)
    {
        $this->switchToTenantDatabase();
        
        $booking = Booking::on('tenant')->findOrFail($id);
        $booking->delete();

        return redirect()->route('tenant.bookings.index')
            ->with('success', 'Booking deleted successfully.');
    }

    public function approve($id)
    {
        $this->switchToTenantDatabase();

        $booking = Booking::on('tenant')->find($id);

        if (!$booking) {
            return redirect()->route('tenant.bookings.index')->with('error', 'Booking not found.');
        }

        if ($booking->status !== 'pending') {
            return redirect()->route('tenant.bookings.index')->with('error', 'Booking cannot be approved as it is not pending.');
        }

        $booking->status = 'confirmed';
        $booking->save();

        // You might want to add logic here to notify the user or update room status if necessary
        // Note: Room status updates upon booking/checkout might be handled elsewhere or need to be added.

        return redirect()->route('tenant.bookings.index')->with('success', 'Booking approved successfully.');
    }
} 