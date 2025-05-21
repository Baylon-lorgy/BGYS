@extends('layouts.app')

@section('title', 'Tenant Login')

@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-4 offset-md-4">
                <div class="card">
                    <div class="card-header card-header-primary">
                        <h4 class="card-title">Tenant Login</h4>
                    </div>
                    <div class="card-body">
                        @if(session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('tenant.login') }}">
                            @csrf

                            <div class="form-group">
                                <label class="bmd-label-floating">Domain Name</label>
                                <input type="text" class="form-control @error('domain_name') is-invalid @enderror" name="domain_name" value="{{ old('domain_name') }}" placeholder="e.g., templa.localhost:8000" required autofocus>
                                <small class="form-text text-muted">Enter your full domain name (e.g., templa.localhost:8000)</small>
                                @error('domain_name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label class="bmd-label-floating">Email Address</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required>
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label class="bmd-label-floating">Password</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" required>
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary btn-block">
                                    Login
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 