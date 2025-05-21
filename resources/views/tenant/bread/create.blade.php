@extends('tenant.layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">New Booking</h1>
        <a href="{{ route('tenant.bookings.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Bookings
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('tenant.bookings.store') }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="room_id">Room</label>
                            <select class="form-control @error('room_id') is-invalid @enderror" id="room_id" name="room_id" required>
                                <option value="">Select a room</option>
                                @foreach($rooms as $room)
                                    <option value="{{ $room->id }}" {{ old('room_id') == $room->id ? 'selected' : '' }}>
                                        Room {{ $room->room_number }} - Floor {{ $room->floor_number }} 
                                        (Capacity: {{ $room->capacity }} persons, Price: ${{ number_format($room->price, 2) }})
                                    </option>
                                @endforeach
                            </select>
                            @error('room_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="check_in">Check In Date</label>
                            <input type="date" class="form-control @error('check_in') is-invalid @enderror" 
                                id="check_in" name="check_in" value="{{ old('check_in') }}" required>
                            @error('check_in')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="check_out">Check Out Date</label>
                            <input type="date" class="form-control @error('check_out') is-invalid @enderror" 
                                id="check_out" name="check_out" value="{{ old('check_out') }}" required>
                            @error('check_out')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="guests">Number of Guests</label>
                            <input type="number" class="form-control @error('guests') is-invalid @enderror" 
                                id="guests" name="guests" value="{{ old('guests') }}" min="1" required>
                            @error('guests')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="special_requests">Special Requests</label>
                            <textarea class="form-control @error('special_requests') is-invalid @enderror" 
                                id="special_requests" name="special_requests" rows="4">{{ old('special_requests') }}</textarea>
                            @error('special_requests')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Create Booking
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Set minimum date for check-in to today
    document.getElementById('check_in').min = new Date().toISOString().split('T')[0];
    
    // Update check-out minimum date when check-in changes
    document.getElementById('check_in').addEventListener('change', function() {
        document.getElementById('check_out').min = this.value;
    });
</script>
@endpush 