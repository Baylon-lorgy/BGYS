<div class="booking-sidebar card shadow">
    <div class="card-header card-header-primary">
        <h5 class="card-title mb-0">Room Bookings</h5>
    </div>
    
    <div class="card-body">
        <div class="mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="font-weight-bold">Active Bookings</h6>
                <span class="badge badge-success">{{ count($confirmedBookings) }}</span>
            </div>
            
            @if(count($confirmedBookings) > 0)
                <div class="list-group">
                    @foreach($confirmedBookings as $booking)
                        <div class="list-group-item list-group-item-action flex-column align-items-start bg-dark text-white border-0 mb-2">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">{{ $booking->user->name ?? 'Guest User' }}</h6>
                                <small>Room #{{ $booking->room->room_number }}</small>
                            </div>
                            <p class="mb-1">{{ $booking->check_in->format('M d') }} - {{ $booking->check_out->format('M d, Y') }}</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <small>{{ $booking->guests }} {{ Str::plural('guest', $booking->guests) }}</small>
                                <div>
                                    <a href="{{ route('tenant.bookings.edit', $booking->id) }}" class="btn btn-sm btn-primary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('tenant.bookings.destroy', $booking->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this booking?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="alert alert-info">No active bookings found.</div>
            @endif
        </div>

        <div class="mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="font-weight-bold">Pending Requests</h6>
                <span class="badge badge-warning">{{ count($pendingBookings) }}</span>
            </div>
            
            @if(count($pendingBookings) > 0)
                <div class="list-group">
                    @foreach($pendingBookings as $booking)
                        <div class="list-group-item list-group-item-action flex-column align-items-start bg-dark text-white border-0 mb-2">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">{{ $booking->user->name ?? 'Guest User' }}</h6>
                                <small>Room #{{ $booking->room->room_number }}</small>
                            </div>
                            <p class="mb-1">{{ $booking->check_in->format('M d') }} - {{ $booking->check_out->format('M d, Y') }}</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <small>{{ $booking->guests }} {{ Str::plural('guest', $booking->guests) }}</small>
                                <div class="btn-group">
                                    <form action="{{ route('tenant.bookings.update', $booking->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="room_id" value="{{ $booking->room_id }}">
                                        <input type="hidden" name="user_id" value="{{ $booking->user_id }}">
                                        <input type="hidden" name="check_in" value="{{ $booking->check_in->format('Y-m-d') }}">
                                        <input type="hidden" name="check_out" value="{{ $booking->check_out->format('Y-m-d') }}">
                                        <input type="hidden" name="guests" value="{{ $booking->guests }}">
                                        <input type="hidden" name="special_requests" value="{{ $booking->special_requests }}">
                                        <input type="hidden" name="status" value="confirmed">
                                        <button type="submit" class="btn btn-sm btn-success" title="Confirm">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                    <a href="{{ route('tenant.bookings.edit', $booking->id) }}" class="btn btn-sm btn-primary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('tenant.bookings.update', $booking->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="room_id" value="{{ $booking->room_id }}">
                                        <input type="hidden" name="user_id" value="{{ $booking->user_id }}">
                                        <input type="hidden" name="check_in" value="{{ $booking->check_in->format('Y-m-d') }}">
                                        <input type="hidden" name="check_out" value="{{ $booking->check_out->format('Y-m-d') }}">
                                        <input type="hidden" name="guests" value="{{ $booking->guests }}">
                                        <input type="hidden" name="special_requests" value="{{ $booking->special_requests }}">
                                        <input type="hidden" name="status" value="cancelled">
                                        <button type="submit" class="btn btn-sm btn-danger" title="Decline">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="alert alert-info">No pending booking requests.</div>
            @endif
        </div>
    </div>
</div>

<style>
    .booking-sidebar {
        background-color: #252b42;
        border: none;
        margin-bottom: 20px;
    }
    .list-group-item {
        transition: transform 0.2s;
    }
    .list-group-item:hover {
        transform: translateY(-2px);
    }
    .btn-group .btn {
        margin: 0 2px;
    }
</style> 