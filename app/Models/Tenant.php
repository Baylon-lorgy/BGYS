<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Tenant extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'bakery_name',
        'email',
        'domain_name',
        'contact_number',
        'plan',
        'status',
        'approved_at',
        'suspended_at',
        'password',
        'database_name',
        'address',
        'city',
        'state',
        'postal_code',
        'latitude',
        'longitude',
        'location_notes'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'approved_at' => 'datetime',
        'suspended_at' => 'datetime',
        'password' => 'hashed',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($tenant) {
            // Generate database name based on tenant's domain
            if (empty($tenant->database_name)) {
                $tenant->database_name = 'tenant_' . str_replace(['.', '-'], '_', explode('.', $tenant->domain_name)[0]);
            }
        });
    }

    public function getDatabaseName()
    {
        return $this->database_name ?? 'tenant_' . str_replace(['.', '-'], '_', explode('.', $this->domain_name)[0]);
    }

    public function isApproved()
    {
        return $this->status === 'active' && $this->approved_at !== null;
    }

    public function isSuspended()
    {
        return $this->status === 'suspended' && $this->suspended_at !== null;
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * Get the rooms for the tenant.
     */
    public function rooms()
    {
        return $this->hasMany(Room::class, 'tenant_id', 'id');
    }

    /**
     * Get the rooms for the tenant using the tenant database.
     */
    public function getRooms()
    {
        $room = new Room();
        $room->setConnection('tenant');
        return $room->where('tenant_id', $this->id);
    }
} 