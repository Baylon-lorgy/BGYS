@extends('layouts.app')

@section('title', 'Tenants')
@section('nav', 'Tenants')
@section('content')
<style>
    .content {
        padding: 2rem;
        background-color: #f8fafc;
        min-height: calc(100vh - 64px);
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
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        padding: 1.25rem 1.5rem;
        border-bottom: none;
    }

    .card-title {
        color: #ffffff;
        font-size: 1.25rem;
        font-weight: 600;
        margin: 0;
    }

    .card-body {
        padding: 1.5rem;
    }

    .table {
        width: 100%;
        margin-bottom: 0;
        border-collapse: separate;
        border-spacing: 0;
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
        white-space: nowrap;
    }

    .table td {
        padding: 1rem;
        vertical-align: middle;
        color: #1e293b;
        border-bottom: 1px solid #e2e8f0;
        transition: background-color 0.2s;
    }

    .table tr:hover td {
        background-color: #f8fafc;
    }

    /* Column-specific styles */
    .table td.name-column {
        font-weight: 500;
        color: #1e293b;
    }

    .table td.email-column {
        font-family: monospace;
        color: #3b82f6;
        letter-spacing: -0.5px;
    }

    .table td.domain-column {
        font-family: monospace;
        color: #64748b;
    }

    .table td.contact-column {
        font-family: monospace;
        letter-spacing: 0.5px;
    }

    .table td.plan-column {
        text-transform: capitalize;
        font-weight: 500;
    }

    .badge {
        padding: 0.5rem 0.75rem;
        font-size: 0.75rem;
        font-weight: 600;
        border-radius: 0.375rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .badge-success {
        background-color: #22c55e;
        color: #ffffff;
    }

    .badge-warning {
        background-color: #f97316;
        color: #ffffff;
    }

    .badge-danger {
        background-color: #ef4444;
        color: #ffffff;
    }

    .btn-group {
        display: flex;
        gap: 0.5rem;
        align-items: center;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.5rem;
        border-radius: 0.375rem;
        border: none;
        transition: all 0.2s;
        cursor: pointer;
    }

    .btn-sm {
        padding: 0.375rem;
        font-size: 0.875rem;
    }

    .btn-info {
        background-color: #0ea5e9;
        color: #ffffff;
    }

    .btn-success {
        background-color: #22c55e;
        color: #ffffff;
    }

    .btn-warning {
        background-color: #f97316;
        color: #ffffff;
    }

    .btn-danger {
        background-color: #ef4444;
        color: #ffffff;
    }

    .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 4px -1px rgba(0, 0, 0, 0.1);
    }

    .alert {
        border-radius: 0.5rem;
        padding: 1rem;
        margin-bottom: 1rem;
    }

    .alert-success {
        background-color: #f0fdf4;
        border: 1px solid #bbf7d0;
        color: #15803d;
    }

    .pagination {
        margin-top: 1.5rem;
        display: flex;
        justify-content: center;
        gap: 0.5rem;
    }

    .page-link {
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        border: 1px solid #e2e8f0;
        color: #3b82f6;
        background-color: #ffffff;
        transition: all 0.2s;
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
</style>

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header card-header-primary">
                        <h4 class="card-title">All Tenants</h4>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Domain</th>
                                        <th>Contact</th>
                                        <th>Plan</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tenants as $tenant)
                                        <tr>
                                            <td class="name-column">{{ $tenant->name }}</td>
                                            <td class="email-column">{{ $tenant->email }}</td>
                                            <td class="domain-column">{{ $tenant->domain_name }}</td>
                                            <td class="contact-column">{{ $tenant->contact_number }}</td>
                                            <td class="plan-column">{{ $tenant->plan }}</td>
                                            <td>
                                                @if($tenant->status === 'active')
                                                    <span class="badge badge-success">Active</span>
                                                @elseif($tenant->status === 'suspended')
                                                    <span class="badge badge-danger">Suspended</span>
                                                @else
                                                    <span class="badge badge-warning">Pending</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('tenants.edit', $tenant) }}" class="btn btn-sm btn-info" title="Edit">
                                                        <i class="material-icons">edit</i>
                                                    </a>
                                                    @if($tenant->status === 'pending')
                                                        <form action="{{ route('tenants.approve', $tenant) }}" method="POST">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-success" title="Approve">
                                                                <i class="material-icons">check</i>
                                                            </button>
                                                        </form>
                                                        <form action="{{ route('tenants.reject', $tenant) }}" method="POST">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-danger" title="Reject" onclick="return confirm('Are you sure you want to reject this tenant?')">
                                                                <i class="material-icons">close</i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                    @if($tenant->status === 'active')
                                                        <form action="{{ route('tenants.suspend', $tenant) }}" method="POST">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-warning" title="Suspend">
                                                                <i class="material-icons">pause</i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                    @if($tenant->status === 'suspended')
                                                        <form action="{{ route('tenants.activate', $tenant) }}" method="POST">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-success" title="Activate">
                                                                <i class="material-icons">play_arrow</i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                    <form action="{{ route('tenants.destroy', $tenant) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this tenant?')" title="Delete">
                                                            <i class="material-icons">delete</i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="pagination">
                            {{ $tenants->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 