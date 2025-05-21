@extends('layouts.tenant')

@section('title', 'Edit Booking')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Booking</h1>
        <a href="{{ route('tenant.bookings.index') }}" class="btn btn-secondary">
            <i class="material-icons">arrow_back</i> Back to Bookings
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header card-header-primary">
            <h4 class="card-title mb-0">Booking Details</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('tenant.bookings.update', $booking->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="room_id">Room</label>
                            <select name="room_id" id="room_id" class="form-control" required>
                                <option value="">Select a room</option>
                                @foreach($rooms as $room)
                                    <option value="{{ $room->id }}" {{ $booking->room_id == $room->id ? 'selected' : '' }}>
                                        Room #{{ $room->room_number }} (Floor {{ $room->floor }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    @if($booking->user_id == 0)
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="guest_name">Guest Name</label>
                            <input type="text" name="guest_name" id="guest_name" class="form-control" 
                                value="{{ old('guest_name', $booking->guest_name ?? 'Guest User') }}" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="guest_email">Guest Email</label>
                            <input type="email" name="guest_email" id="guest_email" class="form-control" 
                                value="{{ old('guest_email', $booking->guest_email ?? '') }}" required>
                        </div>
                    </div>
                    @else
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="user_id">Guest</label>
                            <select name="user_id" id="user_id" class="form-control" required>
                                <option value="">Select a guest</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->user_id }}" {{ $booking->user_id == $user->user_id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    @endif
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="check_in">Check-in Date</label>
                            <input type="date" name="check_in" id="check_in" class="form-control" 
                                value="{{ old('check_in', $booking->check_in->format('Y-m-d')) }}" required>
                        </div>
                    </div>
                    {{-- <div class="col-md-6">
                        <div class="form-group">
                            <label for="check_out">Check-out Date</label>
                            <input type="date" name="check_out" id="check_out" class="form-control" 
                                value="{{ old('check_out', $booking->check_out->format('Y-m-d')) }}" required>
                        </div>
                    </div> --}}
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="guests">Number of Guests</label>
                            <input type="number" name="guests" id="guests" class="form-control" 
                                value="{{ old('guests', $booking->guests) }}" min="1" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select name="status" id="status" class="form-control" required>
                                <option value="pending" {{ $booking->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="confirmed" {{ $booking->status == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                <option value="cancelled" {{ $booking->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                <option value="completed" {{ $booking->status == 'completed' ? 'selected' : '' }}>Completed</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="special_requests">Special Requests</label>
                    <textarea name="special_requests" id="special_requests" class="form-control" rows="3">{{ old('special_requests', $booking->special_requests) }}</textarea>
                </div>

                @if($booking->user_id != 0)
                <div class="form-group">
                    <label for="notes">Internal Notes</label>
                    <textarea name="notes" id="notes" class="form-control" rows="3">{{ old('notes', $booking->notes) }}</textarea>
                </div>
                @endif

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Update Booking</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    {{-- document.addEventListener('DOMContentLoaded', function() {
        const checkInInput = document.getElementById('check_in');
        const checkOutInput = document.getElementById('check_out');
        
        checkInInput.addEventListener('change', function() {
            const checkInDate = new Date(this.value);
            const nextDay = new Date(checkInDate);
            nextDay.setDate(nextDay.getDate() + 1);
            
            const month = String(nextDay.getMonth() + 1).padStart(2, '0');
            const day = String(nextDay.getDate()).padStart(2, '0');
            const nextDayFormatted = `${nextDay.getFullYear()}-${month}-${day}`;
            
            if (checkOutInput.value && new Date(checkOutInput.value) <= checkInDate) {
                checkOutInput.value = nextDayFormatted;
            }
        });
    }); --}}
</script>
@endsection 