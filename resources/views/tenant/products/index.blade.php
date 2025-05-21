@extends('layouts.tenant')

@section('title', 'Products')

@section('content')
<style>
    .product-card {
        background: #f3f4f6;
        border-radius: 1rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        transition: all 0.2s ease;
        overflow: hidden;
    }

    .product-header {
        background: linear-gradient(135deg, #1e293b, #334155);
        padding: 1.25rem 1.5rem;
        border-radius: 1rem 1rem 0 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .product-title {
        color: #ffffff;
        font-size: 1.25rem;
        font-weight: 600;
        margin: 0;
    }

    .btn-add-product {
        display: inline-flex;
        align-items: center;
        padding: 0.5rem 1rem;
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: #ffffff;
        border-radius: 0.5rem;
        font-weight: 500;
        transition: all 0.2s ease;
        border: none;
        text-decoration: none;
        box-shadow: 0 2px 4px rgba(59, 130, 246, 0.3);
    }

    .btn-add-product:hover {
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        transform: translateY(-1px);
        box-shadow: 0 4px 6px rgba(59, 130, 246, 0.4);
        color: #ffffff;
        text-decoration: none;
    }

    .btn-add-product i {
        margin-right: 0.5rem;
        font-size: 1.25rem;
    }

    .product-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    .product-table th {
        background: #e5e7eb;
        color: #64748b;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        padding: 1rem;
        border-bottom: 2px solid #d1d5db;
    }

    .product-table td {
        padding: 1rem;
        color: #475569;
        border-bottom: 1px solid #d1d5db;
        vertical-align: middle;
        background: #f8f9fa;
    }

    .product-table tr:hover {
        background-color: #e5e7eb;
    }

    .product-image {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 0.5rem;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .product-image-placeholder {
        width: 50px;
        height: 50px;
        background-color: #e5e7eb;
        border-radius: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #94a3b8;
    }

    .badge {
        padding: 0.5rem 0.75rem;
        border-radius: 0.375rem;
        font-weight: 500;
        font-size: 0.75rem;
    }

    .badge-success {
        background: linear-gradient(135deg, #22c55e, #16a34a);
        color: white;
    }

    .badge-warning {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: white;
    }

    .badge-danger {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: white;
    }

    .btn-action {
        padding: 0.5rem;
        border-radius: 0.375rem;
        border: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        margin: 0 0.25rem;
    }

    .btn-action.edit {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: white;
    }

    .btn-action.delete {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: white;
    }

    .btn-action:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .btn-action i {
        font-size: 1.25rem;
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

    .pagination {
        margin-top: 1.5rem;
        display: flex;
        justify-content: center;
    }

    .page-link {
        padding: 0.5rem 1rem;
        margin: 0 0.25rem;
        border-radius: 0.375rem;
        color: #3b82f6;
        background-color: #f3f4f6;
        border: 1px solid #d1d5db;
        transition: all 0.2s ease;
    }

    .page-link:hover {
        background-color: #e5e7eb;
        color: #2563eb;
        border-color: #bfdbfe;
    }

    .page-item.active .page-link {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        border-color: #2563eb;
        color: #ffffff;
    }

    .alert {
        border-radius: 0.5rem;
        padding: 1rem;
        margin-bottom: 1rem;
        border: none;
    }

    .alert-success {
        background: linear-gradient(135deg, #22c55e, #16a34a);
        color: white;
    }

    .alert-danger {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: white;
    }
</style>

<div class="container-fluid" style="background-color: #f3f4f6;">
    <div class="product-card">
        <div class="product-header">
            <h4 class="product-title">Products</h4>
            <a href="{{ route('tenant.products.create') }}" class="btn-add-product">
                <i class="material-icons">add</i> Add New Product
            </a>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <div class="table-responsive">
                <table class="product-table">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Section</th>
                            <th>Stock</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
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
                                    <a href="{{ route('tenant.products.edit', $product->id) }}" class="btn-action edit">
                                        <i class="material-icons">edit</i>
                                    </a>
                                    <form action="{{ route('tenant.products.destroy', $product->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action delete" onclick="return confirm('Are you sure you want to delete this product?')">
                                            <i class="material-icons">delete</i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state">
                                        <p>No products found. <a href="{{ route('tenant.products.create') }}">Add your first product</a>.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($products instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <div class="pagination">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 