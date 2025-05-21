<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class RoomController extends Controller
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
        $tenant = Auth::guard('tenant')->user();
        $rooms = $tenant->getRooms()->with('photos')->get();
        
        // Determine the maximum number of photos allowed based on the tenant's plan
        $maxPhotos = $tenant->plan === 'pro' ? 'unlimited' : 3;
        
        // Pass rooms and maxPhotos to the view
        return view('tenant.rooms.index', compact('rooms', 'maxPhotos'));
    }

    public function create()
    {
        $this->switchToTenantDatabase();
        $tenant = Auth::guard('tenant')->user();
        $roomCount = $tenant->getRooms()->count();
        $maxRooms = $tenant->plan === 'pro' ? PHP_INT_MAX : 3;

        if ($roomCount >= $maxRooms) {
            return redirect()->route('tenant.rooms.index')
                ->with('error', 'You have reached the maximum number of rooms allowed for your plan. Please upgrade to Pro plan for unlimited rooms.');
        }

        return view('tenant.rooms.create', compact('tenant'));
    }

    public function store(Request $request)
    {
        $this->switchToTenantDatabase();
        $tenant = Auth::guard('tenant')->user();

        // Simple check for duplicate room number for this tenant
        $existingRoom = DB::connection('tenant')
                          ->table('rooms')
                          ->where('tenant_id', $tenant->id)
                          ->where('room_number', $request->room_number)
                          ->first();

        if ($existingRoom) {
            return redirect()->route('tenant.rooms.create')
                ->withInput() // Keep old input
                ->with('error', 'Room number ' . $request->room_number . ' already exists for your property.');
        }

        $roomCount = $tenant->getRooms()->count();
        $maxRooms = $tenant->plan === 'pro' ? PHP_INT_MAX : 3;

        if ($roomCount >= $maxRooms) {
            return redirect()->route('tenant.rooms.index')
                ->with('error', 'You have reached the maximum number of rooms allowed for your plan. Please upgrade to Pro plan for unlimited rooms.');
        }

        $validated = $request->validate([
            'room_number' => 'required|string|max:255',
            'floor' => 'required|integer|min:1',
            'capacity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'photos' => 'required',
            'photos.*' => 'image|max:2048',
        ]);

        // Use the authenticated user's id as tenant_id
        $validated['tenant_id'] = Auth::guard('tenant')->id();

        // Handle multiple photo uploads and apply plan limits BEFORE creating room
        $photos = $request->file('photos');
        $maxPhotosAllowed = $tenant->plan === 'pro' ? PHP_INT_MAX : 3;

        if ($request->hasFile('photos') && count($photos) > $maxPhotosAllowed) {
             return redirect()->route('tenant.rooms.create')
                ->withInput()
                ->with('error', 'You can only upload up to ' . $maxPhotosAllowed . ' photos per room on your current plan.');
        }

        // Remove 'photos' from $validated before creating the room data itself
        unset($validated['photos']);

        // Create the room after validation
        $room = Room::on('tenant')->create($validated);

        // Now save the photos if validation passed
        if ($request->hasFile('photos')) {
             foreach ($photos as $photo) {
                $path = $photo->store('rooms', 'public');
                $room->photos()->create(['photo_path' => $path]);
            }
        }

        return redirect()->route('tenant.rooms.index')
            ->with('success', 'Room created successfully.');
    }

    public function show($id)
    {
        $this->switchToTenantDatabase();
        $room = Room::on('tenant')
            ->where('id', $id)
            ->where('tenant_id', Auth::guard('tenant')->id())
            ->firstOrFail();
        
        // Get confirmed and pending bookings for this room using the tenant database
        $confirmedBookings = DB::connection('tenant')
            ->table('bookings')
            ->join('rooms', 'bookings.room_id', '=', 'rooms.id')
            ->leftJoin('room_tenants', 'bookings.user_id', '=', 'room_tenants.id')
            ->select('bookings.*', 'room_tenants.name as user_name', 'rooms.room_number')
            ->where('bookings.room_id', $room->id)
            ->where('bookings.status', 'confirmed')
            ->get();
            
        $pendingBookings = DB::connection('tenant')
            ->table('bookings')
            ->join('rooms', 'bookings.room_id', '=', 'rooms.id')
            ->leftJoin('room_tenants', 'bookings.user_id', '=', 'room_tenants.id')
            ->select('bookings.*', 'room_tenants.name as user_name', 'rooms.room_number')
            ->where('bookings.room_id', $room->id)
            ->where('bookings.status', 'pending')
            ->get();
            
        // Format the bookings to match the expected structure for the sidebar
        $confirmedBookings = $confirmedBookings->map(function ($booking) {
            $booking->check_in = \Carbon\Carbon::parse($booking->check_in);
            $booking->check_out = \Carbon\Carbon::parse($booking->check_out);
            $booking->user = (object) ['name' => $booking->user_name];
            $booking->room = (object) ['room_number' => $booking->room_number];
            return $booking;
        });
        
        $pendingBookings = $pendingBookings->map(function ($booking) {
            $booking->check_in = \Carbon\Carbon::parse($booking->check_in);
            $booking->check_out = \Carbon\Carbon::parse($booking->check_out);
            $booking->user = (object) ['name' => $booking->user_name];
            $booking->room = (object) ['room_number' => $booking->room_number];
            return $booking;
        });
        
        return view('tenant.rooms.show', compact('room', 'confirmedBookings', 'pendingBookings'));
    }

    public function edit($id)
    {
        $this->switchToTenantDatabase();
        $room = Room::on('tenant')->where('id', $id)
            ->where('tenant_id', Auth::guard('tenant')->id())
            ->firstOrFail();
        return view('tenant.rooms.edit', compact('room'));
    }

    public function update(Request $request, $id)
    {
        $this->switchToTenantDatabase();
        $room = Room::on('tenant')->where('id', $id)
            ->where('tenant_id', Auth::guard('tenant')->id())
            ->firstOrFail();

        $validated = $request->validate([
            'room_number' => 'required|string|max:255',
            'floor' => 'required|integer|min:1',
            'capacity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'photo' => 'nullable|image|max:2048'
        ]);

        $room->update($validated);

        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($room->photo) {
                Storage::disk('public')->delete($room->photo);
            }
            $path = $request->file('photo')->store('rooms', 'public');
            $room->update(['photo' => $path]);
        }

        return redirect()->route('tenant.rooms.index')
            ->with('success', 'Room updated successfully.');
    }

    public function destroy($id)
    {
        $this->switchToTenantDatabase();
        $room = Room::on('tenant')->where('id', $id)
            ->where('tenant_id', Auth::guard('tenant')->id())
            ->firstOrFail();

        if ($room->photo) {
            Storage::disk('public')->delete($room->photo);
        }

        $room->delete();

        return redirect()->route('tenant.rooms.index')
            ->with('success', 'Room deleted successfully.');
    }

    public function uploadPhoto(Request $request, $id)
    {
        $this->switchToTenantDatabase();
        $room = Room::on('tenant')->where('id', $id)
            ->where('tenant_id', Auth::guard('tenant')->id())
            ->firstOrFail();

        $request->validate([
            'photo' => 'required|image|max:2048'
        ]);

        if ($room->photo) {
            Storage::disk('public')->delete($room->photo);
        }

        $path = $request->file('photo')->store('rooms', 'public');
        $room->update(['photo' => $path]);

        return response()->json([
            'success' => true,
            'photo_url' => Storage::url($path)
        ]);
    }

    // New method to check room number uniqueness via AJAX
    public function checkRoomNumber(Request $request)
    {
        $this->switchToTenantDatabase();
        $tenant = Auth::guard('tenant')->user();

        $query = DB::connection('tenant')
                   ->table('rooms')
                   ->where('tenant_id', $tenant->id)
                   ->where('room_number', $request->room_number);

        // Exclude current room for update scenarios (though this method is mainly for create)
        if ($request->has('room_id')) {
            $query->where('id', '!=', $request->room_id);
        }

        $exists = $query->exists();

        return response()->json(['exists' => $exists]);
    }

    // Method to generate rooms report PDF
    public function generateRoomsReportPdf()
    {
        $this->switchToTenantDatabase();
        $tenant = Auth::guard('tenant')->user();

        // Fetch rooms data
        $rooms = $tenant->getRooms()->with('photos')->get();

        // Fetch booking data for the tenant by joining with the rooms table
        $bookings = DB::connection('tenant')
                      ->table('bookings')
                      ->join('rooms', 'bookings.room_id', '=', 'rooms.id')
                      ->where('rooms.tenant_id', $tenant->id)
                      ->select('bookings.*', 'rooms.room_number') // Select booking columns and room_number
                      ->orderBy('bookings.created_at', 'desc')
                      ->get();

        // Load the view for the PDF report, passing both rooms and bookings
        $pdf = app('dompdf.wrapper'); // Make sure you have run 'composer require barryvdh/laravel-dompdf'
        $pdf->loadView('tenant.reports.rooms_pdf', compact('rooms', 'tenant', 'bookings'));

        // Optional: Set paper size and orientation
        // $pdf->setPaper('a4', 'landscape');

        // Download the PDF file
        return $pdf->download('rooms_and_bookings_report_' . Str::slug($tenant->boarding_house_name) . '.pdf');
    }
} 