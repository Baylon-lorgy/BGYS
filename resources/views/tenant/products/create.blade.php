@extends('layouts.tenant')

@section('title', 'Add New Product')

@section('content')
<style>
    .product-form-card {
        background: linear-gradient(to bottom, #f1f5f9, #e2e8f0);
        border-radius: 0.75rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        margin-bottom: 1.5rem;
        border: 1px solid rgba(148, 163, 184, 0.2);
    }

    .card-header-primary {
        background: linear-gradient(135deg, #0f172a, #1e293b);
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 0.75rem 0.75rem 0 0;
        border-bottom: 2px solid #3b82f6;
    }

    .card-header-primary .card-title {
        font-size: 1.25rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.025em;
        margin: 0;
        color: #f8fafc;
    }

    .card-body {
        padding: 1.5rem;
        background: rgba(255, 255, 255, 0.5);
        backdrop-filter: blur(10px);
    }

    .form-group {
        margin-bottom: 1rem;
        position: relative;
    }

    .form-group label {
        color: #334155;
        font-weight: 600;
        margin-bottom: 0.375rem;
        display: block;
        font-size: 0.875rem;
    }

    .form-control {
        background-color: rgba(255, 255, 255, 0.9);
        border: 1px solid #cbd5e1;
        border-radius: 0.375rem;
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
        transition: all 0.2s ease;
        color: #1e293b;
    }

    .form-control:focus {
        background-color: #ffffff;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        outline: none;
    }

    .form-control::placeholder {
        color: #94a3b8;
    }

    .form-control-file {
        background: linear-gradient(to bottom, #f8fafc, #f1f5f9);
        border: 2px dashed #cbd5e1;
        border-radius: 0.375rem;
        padding: 0.75rem;
        width: 100%;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .form-control-file:hover {
        border-color: #3b82f6;
        background: linear-gradient(to bottom, #f1f5f9, #e2e8f0);
    }

    select.form-control {
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236B7280'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 0.75rem center;
        background-size: 1.25em;
        padding-right: 2.5rem;
    }

    textarea.form-control {
        min-height: 100px;
        resize: vertical;
    }

    .btn {
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        font-weight: 600;
        font-size: 0.875rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        text-decoration: none;
        cursor: pointer;
        letter-spacing: 0.025em;
    }

    .btn i {
        margin-right: 0.375rem;
        font-size: 1.125rem;
    }

    .btn-primary {
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        color: white;
        border: none;
        box-shadow: 0 2px 4px rgba(37, 99, 235, 0.3);
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #1d4ed8, #1e40af);
        transform: translateY(-1px);
        box-shadow: 0 4px 6px rgba(37, 99, 235, 0.4);
    }

    .btn-secondary {
        background: linear-gradient(135deg, #475569, #334155);
        color: #f8fafc;
        border: none;
        box-shadow: 0 2px 4px rgba(51, 65, 85, 0.2);
    }

    .btn-secondary:hover {
        background: linear-gradient(135deg, #334155, #1e293b);
        color: #ffffff;
        transform: translateY(-1px);
        box-shadow: 0 4px 6px rgba(51, 65, 85, 0.3);
    }

    .alert {
        border-radius: 0.375rem;
        padding: 0.75rem 1rem;
        margin-bottom: 1rem;
        border: none;
        font-size: 0.875rem;
    }

    .alert-danger {
        background: linear-gradient(135deg, #fecaca, #fee2e2);
        color: #991b1b;
        border-left: 4px solid #dc2626;
    }

    .alert-danger ul {
        margin: 0;
        padding-left: 1.25rem;
    }

    .form-text {
        color: #64748b;
        font-size: 0.75rem;
        margin-top: 0.25rem;
    }

    .row {
        margin-bottom: 0.75rem;
    }

    /* Input group styling */
    .price-input {
        position: relative;
    }

    .price-input:before {
        content: "₱";
        position: absolute;
        left: 0.75rem;
        top: 50%;
        transform: translateY(-50%);
        color: #64748b;
        font-weight: 500;
    }

    .price-input input {
        padding-left: 1.75rem;
    }

    /* Status select styling */
    .status-active {
        color: #059669;
    }

    .status-inactive {
        color: #dc2626;
    }

    /* Form sections */
    .form-section {
        background: rgba(255, 255, 255, 0.6);
        border-radius: 0.5rem;
        padding: 1rem;
        margin-bottom: 1rem;
        border: 1px solid rgba(203, 213, 225, 0.5);
    }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="product-form-card">
                <div class="card-header-primary">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title">
                            <i class="material-icons" style="vertical-align: middle; margin-right: 0.5rem;">add_box</i>
                            Add New Product
                        </h4>
                        <a href="{{ route('tenant.products.index') }}" class="btn btn-primary">
                            <i class="material-icons">arrow_back</i> Back to Products
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('tenant.products.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-section">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Product Name</label>
                                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required placeholder="Enter product name">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Section</label>
                                        <select name="section_id" class="form-control" required>
                                            <option value="">Select Section</option>
                                            @foreach($sections as $section)
                                                <option value="{{ $section->id }}" {{ old('section_id') == $section->id ? 'selected' : '' }}>
                                                    {{ $section->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Price</label>
                                        <div class="price-input">
                                            <input type="number" name="price" class="form-control" value="{{ old('price') }}" step="0.01" min="0" required placeholder="0.00">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Stock</label>
                                        <input type="number" name="stock" class="form-control" value="{{ old('stock') }}" min="0" required placeholder="Enter stock quantity">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-section">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Description</label>
                                        <textarea name="description" class="form-control" rows="4" required placeholder="Enter product description">{{ old('description') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-section">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Product Image</label>
                                        <input type="file" name="image" class="form-control-file" accept="image/*">
                                        <small class="form-text">Maximum file size: 2MB. Supported formats: JPG, PNG, GIF.</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Status</label>
                                        <select name="status" class="form-control" required>
                                            <option value="active" {{ old('status') == 'active' ? 'selected' : '' }} class="status-active">Active</option>
                                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }} class="status-inactive">Inactive</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="material-icons">save</i> Save Product
                                </button>
                                <a href="{{ route('tenant.products.index') }}" class="btn btn-secondary">
                                    <i class="material-icons">cancel</i> Cancel
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 