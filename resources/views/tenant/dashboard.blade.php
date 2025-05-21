@extends('layouts.tenant')

@section('title', 'Dashboard')

@section('content')
<style>
    .dashboard-container {
        padding: 2rem;
        color: #2C3E50;
        background-color: #f8fafc;
        min-height: calc(100vh - 64px);
    }

    .stats-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: #ffffff;
        border-radius: 1rem;
        padding: 1.5rem;
        position: relative;
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 100%;
        min-height: 160px;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }

    .stat-card:nth-child(1) {
        background: linear-gradient(135deg, #2C3E50, #1A1D21);
        border-left: 4px solid #3b82f6;
    }

    .stat-card:nth-child(2) {
        background: linear-gradient(135deg, #FF9800, #F57C00);
        border-left: 4px solid #22c55e;
    }

    .stat-card:nth-child(3) {
        background: linear-gradient(135deg, #56CCF2, #2F80ED);
        border-left: 4px solid #d946ef;
    }

    .stat-card:nth-child(4) {
        background: linear-gradient(135deg, #E53E3E, #C53030);
        border-left: 4px solid #f97316;
    }

    .stat-icon {
        font-size: 2.5rem;
        color: #FFFFFF;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        opacity: 0.9;
    }

    .stat-title {
        font-size: 0.875rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: rgba(255, 255, 255, 0.7);
        margin-bottom: 0.5rem;
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        color: #FFFFFF;
        margin-bottom: 0.5rem;
    }

    .stat-subtitle {
        font-size: 0.75rem;
        color: rgba(255, 255, 255, 0.7);
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    .card {
        background: #ffffff;
        border: none;
        border-radius: 1rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        margin-bottom: 2rem;
        overflow: hidden;
    }

    .card-header-primary {
        background: linear-gradient(135deg, #2C3E50, #1A1D21);
        padding: 1.25rem 1.5rem;
        border-bottom: none;
    }

    .card-title {
        color: #ffffff;
        font-size: 1.25rem;
        font-weight: 600;
        margin: 0;
    }

    .table-container {
        padding: 1.5rem;
        background: #ffffff;
    }

    .table {
        width: 100%;
        margin-bottom: 0;
    }

    .table th {
        background-color: #f8fafc;
        color: #64748b;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        padding: 1rem;
        border-bottom: 2px solid #e2e8f0;
    }

    .table td {
        padding: 1rem;
        vertical-align: middle;
        color: #1e293b;
        border-bottom: 1px solid #e2e8f0;
    }

    .badge {
        padding: 0.5rem 0.75rem;
        font-size: 0.75rem;
        font-weight: 600;
        border-radius: 0.375rem;
    }

    .badge-success {
        background: linear-gradient(135deg, #56CCF2, #2F80ED);
        color: #ffffff;
    }

    .badge-warning {
        background: linear-gradient(135deg, #FF9800, #F57C00);
        color: #ffffff;
    }

    .badge-danger {
        background: linear-gradient(135deg, #E53E3E, #C53030);
        color: #ffffff;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        font-weight: 500;
        border-radius: 0.5rem;
        transition: all 0.2s;
    }

    .btn-primary {
        background: linear-gradient(135deg, #2C3E50, #1A1D21);
        color: #ffffff;
        border: none;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #1A1D21, #2C3E50);
        transform: translateY(-1px);
    }

    .btn-info {
        background: linear-gradient(135deg, #56CCF2, #2F80ED);
        color: #ffffff;
        border: none;
    }

    .btn-info:hover {
        background: linear-gradient(135deg, #2F80ED, #56CCF2);
        transform: translateY(-1px);
    }

    .actions-column {
        width: 120px;
    }

    .btn-sm {
        padding: 0.375rem;
        font-size: 0.875rem;
    }

    .pagination {
        margin-top: 1.5rem;
        justify-content: center;
    }

    .page-link {
        padding: 0.5rem 1rem;
        color: #3b82f6;
        background-color: #ffffff;
        border: 1px solid #e2e8f0;
        margin: 0 0.25rem;
        border-radius: 0.375rem;
    }

    .page-link:hover {
        background-color: #f8fafc;
        color: #2563eb;
    }

    .page-item.active .page-link {
        background-color: #3b82f6;
        border-color: #3b82f6;
        color: #ffffff;
    }

    .empty-state {
        text-align: center;
        padding: 3rem 1.5rem;
        color: #64748b;
    }

    .empty-state a {
        color: #3b82f6;
        text-decoration: none;
        font-weight: 500;
    }

    .empty-state a:hover {
        text-decoration: underline;
    }

    .product-image {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 0.5rem;
    }

    .product-image-placeholder {
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #f3f4f6;
        border-radius: 0.5rem;
        color: #9ca3af;
    }

    .product-image-placeholder i {
        font-size: 1.5rem;
    }
</style>

<div class="dashboard-container">
    @if(isset($error))
        <div class="alert alert-danger">
            {{ $error }}
        </div>
    @endif

    <div class="d-flex justify-content-end mb-4">
        <a href="{{ route('tenant.products.report.pdf') }}" class="btn btn-info">
            <i class="material-icons">picture_as_pdf</i>
            Generate Report
        </a>
    </div>

    <div class="stats-cards">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="material-icons">bakery_dining</i>
                <span class="stat-title">TOTAL PRODUCTS</span>
            </div>
            <div>
                <div class="stat-value">{{ $dashboardData['product_stats']['total'] }}</div>
                <div class="stat-subtitle">
                    <i class="material-icons" style="font-size: 1rem;">date_range</i>
                    All Time
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="material-icons">inventory</i>
                <span class="stat-title">IN STOCK</span>
            </div>
            <div>
                <div class="stat-value">{{ $dashboardData['product_stats']['in_stock'] }}</div>
                <div class="stat-subtitle">
                    <i class="material-icons" style="font-size: 1rem;">update</i>
                    Just Updated
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="material-icons">trending_up</i>
                <span class="stat-title">BEST SELLERS</span>
            </div>
            <div>
                <div class="stat-value">{{ $dashboardData['product_stats']['best_sellers'] }}</div>
                <div class="stat-subtitle">
                    <i class="material-icons" style="font-size: 1rem;">stars</i>
                    Top Performing
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="material-icons">inventory_2</i>
                <span class="stat-title">LOW STOCK</span>
            </div>
            <div>
                <div class="stat-value">{{ $dashboardData['product_stats']['low_stock'] }}</div>
                <div class="stat-subtitle">
                    <i class="material-icons" style="font-size: 1rem;">warning</i>
                    Need Restock
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header card-header-primary">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Recent Products</h4>
                <a href="{{ route('tenant.products.create') }}" class="btn btn-primary">
                    <i class="material-icons">add</i>
                    Add New Product
                </a>
            </div>
        </div>
        <div class="table-container">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Section</th>
                            <th>Stock</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th class="actions-column">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($products) && count($products) > 0)
                            @foreach($products as $product)
                                <tr>
                                    <td>
                                        @if($product->image)
                                            <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" class="product-image">
                                        @else
                                            <div class="product-image-placeholder">
                                                <i class="material-icons">image</i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>{{ $product->name }}</td>
                                    <td>{{ $product->section_name }}</td>
                                    <td>{{ $product->stock }}</td>
                                    <td>₱{{ number_format($product->price, 2) }}</td>
                                    <td>
                                        @if($product->stock > 10)
                                            <span class="badge badge-success">In Stock</span>
                                        @elseif($product->stock > 0)
                                            <span class="badge badge-warning">Low Stock</span>
                                        @else
                                            <span class="badge badge-danger">Out of Stock</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('tenant.products.edit', $product->id) }}" class="btn btn-sm btn-primary">
                                                <i class="material-icons">edit</i>
                                            </a>
                                            <form action="{{ route('tenant.products.destroy', $product->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this product?')">
                                                    <i class="material-icons">delete</i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state">
                                        <p>No products found. <a href="{{ route('tenant.products.create') }}">Add your first product</a> to get started.</p>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            @if(isset($products) && $products instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <div class="pagination">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

@endsection