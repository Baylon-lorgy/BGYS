<?php

namespace App\Policies;

use App\Models\Room;
use App\Models\Tenant;
use Illuminate\Auth\Access\HandlesAuthorization;

class RoomPolicy
{
    use HandlesAuthorization;

    public function viewAny(Tenant $tenant)
    {
        return true;
    }

    public function view(Tenant $tenant, Room $room)
    {
        return $tenant->id === $room->tenant_id;
    }

    public function create(Tenant $tenant)
    {
        $roomCount = $tenant->rooms()->count();
        $maxRooms = $tenant->plan === 'pro' ? PHP_INT_MAX : 3;
        return $roomCount < $maxRooms;
    }

    public function update(Tenant $tenant, Room $room)
    {
        return $tenant->id === $room->tenant_id;
    }

    public function delete(Tenant $tenant, Room $room)
    {
        return $tenant->id === $room->tenant_id;
    }
} 