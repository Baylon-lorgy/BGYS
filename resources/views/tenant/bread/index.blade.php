@extends('layouts.tenant')

@section('title', 'Bookings')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-500">Bookings</h1>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header card-header-primary">
            <h4 class="card-title mb-0">All Bookings</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Room</th>
                            <th>Guest</th>
                            <th>Email</th>
                            <th>Check In</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bookings as $booking)
                            <tr>
                                <td>Room #{{ $booking->room->room_number }}</td>
                                <td>{{ $booking->user_name ?? $booking->user->name ?? 'Guest User' }}</td>
                                <td>
                                    @if($booking->user_id == 0 && $booking->notes)
                                        @php
                                            $guestInfo = json_decode($booking->notes, true);
                                        @endphp
                                        {{ $guestInfo['guest_email'] ?? 'N/A' }}
                                    @else
                                        {{ $booking->user->email ?? 'N/A' }}
                                    @endif
                                </td>
                                <td>{{ \Carbon\Carbon::parse($booking->check_in)->format('M d, Y') }}</td>
                                <td>
                                    @if($booking->status === 'confirmed')
                                        <span class="badge badge-success">Confirmed</span>
                                    @elseif($booking->status === 'pending')
                                        <span class="badge badge-warning">Pending</span>
                                    @elseif($booking->status === 'cancelled')
                                        <span class="badge badge-danger">Cancelled</span>
                                    @else
                                        <span class="badge badge-info">{{ ucfirst($booking->status) }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        @if($booking->status === 'pending')
                                            <form action="{{ route('tenant.bookings.approve', $booking->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success" title="Approve Booking">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                        @endif
                                        @if($booking->status === 'confirmed')
                                            <form action="{{ route('tenant.bookings.update', $booking->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="completed">
                                                <button type="submit" class="btn btn-sm btn-info" title="Complete Booking">
                                                    <i class="fas fa-check-double"></i>
                                                </button>
                                            </form>
                                        @endif
                                        <a href="{{ route('tenant.bookings.edit', $booking->id) }}" class="btn btn-sm btn-primary" title="Edit Booking">
                                            <i class="material-icons">edit</i>
                                        </a>
                                        <form action="{{ route('tenant.bookings.destroy', $booking->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this booking?')" title="Delete Booking">
                                                <i class="material-icons">delete</i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No bookings found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
.table-hover tbody tr:hover {
    background-color: #e6e6fa !important; /* Light lavender */
    color: #2d0b52 !important;           /* Dark purple */
}
.table-hover tbody tr:hover td, .table-hover tbody tr:hover th {
    color: #2d0b52 !important;
}
.btn-group .btn {
    width: 32px;
    height: 32px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
@endsection 