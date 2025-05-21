<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Tenant;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class PublicRoomsController extends Controller
{
    /**
     * Display a listing of rooms from all tenants
     */
    public function index()
    {
        // Get all tenants
        $tenants = Tenant::all();
        $allRooms = [];
        
        // Fetch rooms from each tenant's database
        foreach ($tenants as $tenant) {
            Config::set('database.connections.tenant.database', $tenant->database_name);
            DB::purge('tenant');
            DB::reconnect('tenant');
            
            $rooms = DB::connection('tenant')
                ->table('rooms')
                ->select('rooms.*')
                ->where('tenant_id', $tenant->id)
                ->get();
                
            // Add boarding house information and photos to each room
            foreach ($rooms as $room) {
                $room->boarding_house_name = $tenant->boarding_house_name;
                $room->tenant_id = $tenant->id;
                // Fetch photos for this room
                $room->photos = DB::connection('tenant')
                    ->table('room_photos')
                    ->where('room_id', $room->id)
                    ->get();
                $allRooms[] = $room;
            }
        }
        
        // Filter rooms based on search query, capacity, and price
        $searchQuery = request('search');
        $capacityFilter = request('capacity');
        $priceFilter = request('price');

        if ($searchQuery || $capacityFilter || $priceFilter) {
            $allRooms = collect($allRooms)->filter(function ($room) use ($searchQuery, $capacityFilter, $priceFilter) {
                $match = true;

                // Search by boarding house name
                if ($searchQuery) {
                    $match = $match && str_contains(strtolower($room->boarding_house_name), strtolower($searchQuery));
                }

                // Filter by capacity
                if ($capacityFilter) {
                    if ($capacityFilter == '4') {
                        $match = $match && ($room->capacity >= 4);
                    } else {
                        $match = $match && ($room->capacity == $capacityFilter);
                    }
                }

                // Filter by price range
                if ($priceFilter) {
                    list($minPrice, $maxPrice) = explode('-', $priceFilter);
                    $match = $match && ($room->price >= (float)$minPrice && $room->price <= (float)$maxPrice);
                }

                return $match;
            })->all();
        }
        
        return view('public.rooms.index', compact('allRooms'));
    }
    
    /**
     * Display rooms for a specific tenant
     */
    public function tenantRooms($tenantId)
    {
        $tenant = Tenant::findOrFail($tenantId);
        
        // Connect to tenant database
        Config::set('database.connections.tenant.database', $tenant->database_name);
        DB::purge('tenant');
        DB::reconnect('tenant');
        
        // Get rooms for this tenant
        $rooms = DB::connection('tenant')
            ->table('rooms')
            ->where('tenant_id', $tenant->id)
            ->get();
            
        // Add tenant information to each room
        foreach ($rooms as $room) {
            $room->boarding_house_name = $tenant->boarding_house_name;
            $room->tenant_id = $tenant->id;
        }
        
        return view('public.rooms.tenant', compact('rooms', 'tenant'));
    }
    
    /**
     * Display details for a specific room
     */
    public function show($tenantId, $roomId)
    {
        $tenant = Tenant::findOrFail($tenantId);
        
        // Connect to tenant database
        Config::set('database.connections.tenant.database', $tenant->database_name);
        DB::purge('tenant');
        DB::reconnect('tenant');
        
        // Get room details
        $room = DB::connection('tenant')
            ->table('rooms')
            ->where('id', $roomId)
            ->where('tenant_id', $tenant->id)
            ->first();
            
        if (!$room) {
            abort(404);
        }
        
        $room->boarding_house_name = $tenant->boarding_house_name;
        
        // Add tenant location data
        $room->address = $tenant->address;
        $room->city = $tenant->city;
        $room->state = $tenant->state;
        $room->postal_code = $tenant->postal_code;
        $room->latitude = $tenant->latitude;
        $room->longitude = $tenant->longitude;
        $room->location_notes = $tenant->location_notes;

        // Fetch photos for this room
        $room->photos = DB::connection('tenant')
            ->table('room_photos')
            ->where('room_id', $room->id)
            ->get();
        
        return view('public.rooms.show', compact('room', 'tenant'));
    }
    
    /**
     * Show booking form for a room
     */
    public function bookingForm($tenantId, $roomId)
    {
        $tenant = Tenant::findOrFail($tenantId);
        
        // Connect to tenant database
        Config::set('database.connections.tenant.database', $tenant->database_name);
        DB::purge('tenant');
        DB::reconnect('tenant');
        
        // Get room details
        $room = DB::connection('tenant')
            ->table('rooms')
            ->where('id', $roomId)
            ->where('tenant_id', $tenant->id)
            ->first();
            
        if (!$room) {
            abort(404);
        }
        
        $room->boarding_house_name = $tenant->boarding_house_name;
        $room->tenant_name = $tenant->name;
        
        return view('public.rooms.booking-form', compact('room', 'tenant'));
    }
    
    /**
     * Store a new booking request
     */
    public function book(Request $request, $tenantId, $roomId)
    {
        $tenant = Tenant::findOrFail($tenantId);
        
        // Validate the booking request
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'check_in' => 'required|date|after:today',
            'guests' => 'required|integer|min:1',
            'special_requests' => 'nullable|string',
        ]);
        
        // Connect to tenant database
        Config::set('database.connections.tenant.database', $tenant->database_name);
        DB::purge('tenant');
        DB::reconnect('tenant');
        
        // Verify room exists
        $room = DB::connection('tenant')
            ->table('rooms')
            ->where('id', $roomId)
            ->where('tenant_id', $tenant->id)
            ->first();
            
        if (!$room) {
            abort(404);
        }
        
        // Create booking with user information directly in the notes field
        // Set user_id to 0 or a default value since it's required by the schema
        DB::connection('tenant')->table('bookings')->insert([
            'room_id' => $roomId,
            'user_id' => null, // Set to NULL for public bookings
            'check_in' => $validated['check_in'],
            'guests' => $validated['guests'],
            'special_requests' => $validated['special_requests'] ?? null,
            'status' => 'pending',
            'notes' => json_encode([
                'guest_name' => $validated['name'],
                'guest_email' => $validated['email'],
                'booking_source' => 'public_website'
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        // Redirect to rooms index with success message
        return redirect()->route('public.rooms.index')
            ->with('success', 'Your booking request has been submitted successfully! The property owner will contact you soon.');
    }
} 