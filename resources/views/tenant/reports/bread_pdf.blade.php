<!DOCTYPE html>
<html>
<head>
    <title>Rooms Report</title>
    <style>
        body {
            font-family: sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        img {
            max-width: 100px;
            max-height: 80px;
            object-fit: cover;
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <h1>Rooms Report for {{ $tenant->boarding_house_name }}</h1>

    <table>
        <thead>
            <tr>
                <th>Photo</th>
                <th>Room Number</th>
                <th>Floor</th>
                <th>Capacity</th>
                <th>Price</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rooms as $room)
                <tr>
                    <td>
                        @if($room->photos->count() > 0)
                            <img src="{{ public_path('storage/' . $room->photos->first()->photo_path) }}" alt="Room Photo">
                        @else
                            No Photo
                        @endif
                    </td>
                    <td>{{ $room->room_number }}</td>
                    <td>{{ $room->floor }}</td>
                    <td>{{ $room->capacity }}</td>
                    <td>${{ number_format($room->price, 2) }}</td>
                    <td>{{ $room->description }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center;">No rooms found for this report.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <h2>Bookings Report</h2>

    <table>
        <thead>
            <tr>
                <th>Booking ID</th>
                <th>Room Number</th>
                <th>Check-in Date</th>
                <th>Check-out Date</th>
                <th>Status</th>
                {{-- Add other relevant booking columns here --}}
            </tr>
        </thead>
        <tbody>
            @forelse($bookings as $booking)
                <tr>
                    <td>{{ $booking->id }}</td>
                    <td>{{ $booking->room_number ?? 'N/A' }}</td> {{-- Assuming room_number is available or can be joined --}}
                    <td>{{ \Carbon\Carbon::parse($booking->check_in)->format('Y-m-d') }}</td>
                    <td>{{ \Carbon\Carbon::parse($booking->check_out)->format('Y-m-d') }}</td>
                    <td>{{ ucfirst($booking->status) }}</td>
                    {{-- Add other relevant booking data here --}}
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center;">No bookings found for this report.</td> {{-- Update colspan based on number of columns --}}
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html> 