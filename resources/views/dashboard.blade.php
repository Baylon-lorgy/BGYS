@extends('layouts.app')

@section('title', 'Dashboard')

@section('nav', 'Dashboard')
@section('content')
<style>
    .dashboard-container {
        padding: 20px;
        color: #FFFFFF;
    }

    .stats-cards {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 15px;
        padding: 20px;
        position: relative;
        overflow: hidden;
        transition: transform 0.3s ease;
        color: #FFFFFF;
    }

    .stat-card:hover {
        transform: translateY(-5px);
    }

    .stat-card.total {
        background: linear-gradient(135deg, #2C3E50, #1A1D21);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .stat-card.pending {
        background: linear-gradient(135deg, #FF9800, #F57C00);
    }

    .stat-card.active {
        background: linear-gradient(135deg, #56CCF2, #2F80ED);
    }

    .stat-card.suspended {
        background: linear-gradient(135deg, #E53E3E, #C53030);
    }

    .stat-icon {
        font-size: 2.5rem;
        color: #FFFFFF;
        margin-bottom: 10px;
        opacity: 0.9;
    }

    .stat-number {
        font-size: 2rem;
        font-weight: bold;
        color: #FFFFFF;
        margin: 10px 0;
    }

    .stat-label {
        color: #FFFFFF;
        font-size: 1rem;
        font-weight: 500;
        opacity: 0.9;
    }

    .stat-footer {
        color: #FFFFFF;
        font-size: 0.9rem;
        margin-top: 10px;
        display: flex;
        align-items: center;
        gap: 5px;
        opacity: 0.8;
    }

    .bakeries-table {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }

    .table-header {
        background: linear-gradient(135deg, #2C3E50, #1A1D21);
        padding: 20px;
        color: #FFFFFF;
    }

    .table-header h2 {
        margin: 0;
        font-size: 1.5rem;
        font-weight: 500;
        color: #FFFFFF;
    }

    .table {
        margin: 0;
        color: #FFFFFF;
    }

    .table th {
        color: #FFFFFF;
        font-weight: 600;
        border-bottom: 2px solid rgba(255, 255, 255, 0.1);
        padding: 15px 20px;
        font-size: 1rem;
    }

    .table td {
        vertical-align: middle;
        color: #FFFFFF;
        padding: 15px 20px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        font-size: 0.95rem;
    }

    /* Specific styling for bakery name */
    .table td:nth-child(2) {
        font-weight: 500;
        color: var(--light-blue);
    }

    .table tr:hover td:nth-child(2) {
        color: #FFFFFF;
    }

    .table tr:hover {
        background: rgba(255, 255, 255, 0.05);
    }

    .status-badge {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 500;
        color: white;
    }

    .status-active {
        background: linear-gradient(135deg, #56CCF2, #2F80ED);
    }

    .status-pending {
        background: linear-gradient(135deg, #FF9800, #F57C00);
    }

    .status-suspended {
        background: linear-gradient(135deg, #E53E3E, #C53030);
    }

    .action-buttons {
        display: flex;
        gap: 8px;
    }

    .action-btn {
        border: none;
        border-radius: 5px;
        padding: 6px 12px;
        color: white;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    }

    .btn-approve {
        background: linear-gradient(135deg, #56CCF2, #2F80ED);
    }

    .btn-reject {
        background: linear-gradient(135deg, #E53E3E, #C53030);
    }

    .btn-suspend {
        background: linear-gradient(135deg, #FF9800, #F57C00);
    }

    .pagination {
        margin: 20px;
        justify-content: center;
    }

    .page-link {
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: #FFFFFF;
        transition: all 0.2s ease;
    }

    .page-item.active .page-link {
        background: linear-gradient(135deg, #56CCF2, #2F80ED);
        border-color: #2F80ED;
        color: #FFFFFF;
    }

    .page-link:hover {
        background: rgba(86, 204, 242, 0.2);
        color: #FFFFFF;
        border-color: #56CCF2;
    }

    .page-item.disabled .page-link {
        background: rgba(255, 255, 255, 0.05);
        color: rgba(255, 255, 255, 0.5);
        border-color: rgba(255, 255, 255, 0.1);
    }

    /* Alert styling */
    .alert {
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: #FFFFFF;
        border-radius: 10px;
        margin-bottom: 20px;
    }

    .alert-success {
        background: linear-gradient(135deg, rgba(76, 175, 80, 0.9), rgba(46, 125, 50, 0.9));
        border-color: #4CAF50;
    }
</style>

<div class="dashboard-container">
    <div class="stats-cards">
        <div class="stat-card total">
            <i class="material-icons stat-icon">store</i>
            <div class="stat-number">{{ $totalTenants }}</div>
            <div class="stat-label">Total Bakeries</div>
            <div class="stat-footer">
                <i class="material-icons">date_range</i>
                <span>All Time</span>
            </div>
        </div>

        <div class="stat-card pending">
            <i class="material-icons stat-icon">hourglass_empty</i>
            <div class="stat-number">{{ $pendingTenants }}</div>
            <div class="stat-label">Pending Approvals</div>
            <div class="stat-footer">
                <i class="material-icons">update</i>
                <span>Awaiting Review</span>
            </div>
        </div>

        <div class="stat-card active">
            <i class="material-icons stat-icon">check_circle</i>
            <div class="stat-number">{{ $activeTenants }}</div>
            <div class="stat-label">Active Bakeries</div>
            <div class="stat-footer">
                <i class="material-icons">trending_up</i>
                <span>Currently Active</span>
            </div>
        </div>

        <div class="stat-card suspended">
            <i class="material-icons stat-icon">pause_circle</i>
            <div class="stat-number">{{ $suspendedTenants }}</div>
            <div class="stat-label">Suspended</div>
            <div class="stat-footer">
                <i class="material-icons">block</i>
                <span>Currently Suspended</span>
            </div>
        </div>
    </div>

    @if(session('success') && session('tenant_id') && session('password'))
        <div class="alert alert-success">
            {{ session('success') }}
            <form action="{{ route('tenants.send-approval-email', ['tenant' => session('tenant_id')]) }}" method="POST" style="display: inline;">
                @csrf
                <input type="hidden" name="password" value="{{ session('password') }}">
                <button type="submit" class="btn btn-sm btn-primary ml-2">
                    Send Approval Email
                </button>
            </form>
        </div>
    @endif

    <div class="bakeries-table">
        <div class="table-header">
            <h2>Recent Bakeries</h2>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Owner Name</th>
                        <th>Bakery Name</th>
                        <th>Email</th>
                        <th>Domain</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tenants as $tenant)
                        <tr>
                            <td>{{ $tenant->name }}</td>
                            <td>{{ $tenant->bakery_name }}</td>
                            <td>{{ $tenant->email }}</td>
                            <td>{{ $tenant->domain_name }}</td>
                            <td>
                                @if($tenant->status === 'active')
                                    <span class="status-badge status-active">Active</span>
                                @elseif($tenant->status === 'suspended')
                                    <span class="status-badge status-suspended">Suspended</span>
                                @else
                                    <span class="status-badge status-pending">Pending</span>
                                @endif
                            </td>
                            <td>
                                <div class="action-buttons">
                                    @if($tenant->status === 'pending')
                                        <form action="{{ route('tenants.approve', $tenant) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="action-btn btn-approve" title="Approve">
                                                <i class="material-icons">check</i>
                                            </button>
                                        </form>
                                        <form action="{{ route('tenants.reject', $tenant) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="action-btn btn-reject" title="Reject">
                                                <i class="material-icons">close</i>
                                            </button>
                                        </form>
                                    @endif
                                    @if($tenant->status === 'active')
                                        <form action="{{ route('tenants.suspend', $tenant) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="action-btn btn-suspend" title="Suspend">
                                                <i class="material-icons">pause</i>
                                            </button>
                                        </form>
                                    @endif
                                    @if($tenant->status === 'suspended')
                                        <form action="{{ route('tenants.activate', $tenant) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="action-btn btn-approve" title="Activate">
                                                <i class="material-icons">play_arrow</i>
                                            </button>
                                        </form>
                                    @endif
                                    <form action="{{ route('tenants.destroy', $tenant) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="action-btn btn-reject" onclick="return confirm('Are you sure you want to delete this bakery?')" title="Delete">
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
        <div class="d-flex justify-content-center mt-4 mb-4">
            {{ $tenants->links() }}
        </div>
    </div>
</div>
@endsection