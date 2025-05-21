@extends('layouts.tenant')

@section('title', 'Subscription')

@section('content')
<style>
    .subscription-card {
        background: #f1f5f9;
        border-radius: 1rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        transition: all 0.2s ease;
        height: 100%;
    }

    .subscription-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }

    .plan-header {
        padding: 1.5rem;
        border-radius: 1rem 1rem 0 0;
        background: linear-gradient(135deg, #1e293b, #334155);
        color: #ffffff;
    }

    .plan-header.pro {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
    }

    .plan-name {
        font-size: 1.5rem;
        font-weight: 600;
        margin: 0;
    }

    .plan-price {
        font-size: 2.5rem;
        font-weight: 700;
        color: #1e293b;
        margin: 1rem 0 0.5rem;
    }

    .plan-period {
        color: #64748b;
        font-size: 0.875rem;
    }

    .feature-list {
        padding: 0;
        margin: 1.5rem 0;
    }

    .feature-item {
        display: flex;
        align-items: center;
        padding: 0.5rem 0;
        color: #475569;
    }

    .feature-item i {
        color: #3b82f6;
        margin-right: 0.75rem;
        font-size: 1.25rem;
    }

    .stat-card-modern {
        background: #f1f5f9;
        border-radius: 1rem;
        padding: 1.5rem;
        height: 100%;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        transition: all 0.2s ease;
        border-left: 4px solid #3b82f6;
    }

    .stat-card-modern:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 12px -2px rgba(0, 0, 0, 0.1);
    }

    .stat-title-modern {
        color: #64748b;
        font-size: 0.875rem;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 0.5rem;
    }

    .stat-value-modern {
        color: #1e293b;
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .stat-subtitle-modern {
        color: #64748b;
        font-size: 0.875rem;
    }

    .btn-subscription {
        width: 100%;
        padding: 0.75rem;
        border-radius: 0.5rem;
        font-weight: 600;
        transition: all 0.2s ease;
    }

    .btn-subscription.btn-primary {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        border: none;
        color: #ffffff;
    }

    .btn-subscription.btn-primary:hover {
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        transform: translateY(-1px);
        box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.5);
    }

    .btn-subscription.btn-secondary {
        background: #e2e8f0;
        border: none;
        color: #64748b;
    }
</style>

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="stat-card-modern">
                <div class="stat-title-modern">Current Plan</div>
                <div class="stat-value-modern">{{ ucfirst($tenant->plan) }}</div>
                <div class="stat-subtitle-modern">
                    @if($tenant->plan === 'free')
                        Free forever
                    @else
                        Pro features unlocked
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="stat-card-modern">
                <div class="stat-title-modern">Product Usage</div>
                <div class="stat-value-modern">{{ $productCount }} / {{ $maxProducts === PHP_INT_MAX ? '∞' : $maxProducts }}</div>
                <div class="stat-subtitle-modern">
                    @if($canAddMoreProducts)
                        You can add more products
                    @else
                        Maximum limit reached
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        @foreach($plans as $planId => $plan)
            <div class="col-md-6 mb-4">
                <div class="subscription-card">
                    <div class="plan-header {{ $planId === 'pro' ? 'pro' : '' }}">
                        <h3 class="plan-name">{{ $plan['name'] }}</h3>
                    </div>
                    <div class="card-body">
                        <div class="text-center">
                            <div class="plan-price">₱{{ number_format($plan['price'], 2) }}</div>
                            <div class="plan-period">per month</div>
                        </div>
                        <ul class="feature-list list-unstyled">
                            @foreach($plan['features'] as $feature)
                                <li class="feature-item">
                                    <i class="material-icons">check_circle</i>
                                    {{ $feature }}
                                </li>
                            @endforeach
                        </ul>
                        @if($tenant->plan === $planId)
                            <button class="btn btn-subscription btn-secondary" disabled>Current Plan</button>
                        @elseif($planId === 'pro' && $tenant->plan === 'free')
                            <form action="{{ route('tenant.subscription.upgrade') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-subscription btn-primary">Upgrade Now</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection 