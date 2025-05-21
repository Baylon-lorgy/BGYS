<!DOCTYPE html>
<html>
<head>
    <title>Products Report</title>
    <style>
        body {
            font-family: sans-serif;
            margin: 20px;
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
            font-size: 12px;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        .section-header {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        .low-stock {
            color: #dc3545;
        }
        .report-info {
            margin-bottom: 20px;
            font-size: 12px;
        }
        .report-info p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <h1>Products Report</h1>

    <div class="report-info">
        <p><strong>Business Name:</strong> {{ $tenant->name }}</p>
        <p><strong>Generated On:</strong> {{ now()->format('F j, Y h:i A') }}</p>
        <p><strong>Total Products:</strong> {{ $products->count() }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Section</th>
                <th>Product Name</th>
                <th>Description</th>
                <th>Price</th>
                <th>Status</th>
                <th>Total Sold</th>
                <th>Total Revenue</th>
            </tr>
        </thead>
        <tbody>
            @php
                $currentSection = '';
            @endphp
            
            @forelse($products as $product)
                @if($currentSection !== $product->section_name)
                    @php
                        $currentSection = $product->section_name;
                    @endphp
                @endif
                <tr>
                    <td>{{ $product->section_name }}</td>
                    <td>{{ $product->name }}</td>
                    <td>{{ Str::limit($product->description, 50) }}</td>
                    <td>₱{{ number_format($product->price, 2) }}</td>
                    <td>{{ ucfirst($product->status) }}</td>
                    <td>{{ $salesData->get($product->id)?->total_sold ?? 0 }}</td>
                    <td>₱{{ number_format($salesData->get($product->id)?->total_revenue ?? 0, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center;">No products found for this report.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="report-info">
        <p><strong>Note:</strong> This report includes all products and their sales data across all sections.</p>
    </div>
</body>
</html> 