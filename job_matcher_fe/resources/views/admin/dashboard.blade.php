@extends('layouts.admin')

@section('page-title', 'Admin Dashboard')

@section('content')
<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card h-100 shadow-lg border-0">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="fas fa-users fa-4x text-primary"></i>
                </div>
                <h5 class="card-title fw-bold">Total Users</h5>
                <h1 class="card-text display-4 fw-bold text-primary">{{ \App\Models\User::count() }}</h1>
                <p class="text-muted">Registered users</p>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card h-100 shadow-lg border-0">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="fas fa-user-shield fa-4x text-success"></i>
                </div>
                <h5 class="card-title fw-bold">Admins</h5>
                <h1 class="card-text display-4 fw-bold text-success">{{ \App\Models\User::where('role', 'admin')->count() }}</h1>
                <p class="text-muted">Administrator accounts</p>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-4">
        <div class="card h-100 shadow-lg border-0">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="fas fa-user fa-4x text-info"></i>
                </div>
                <h5 class="card-title fw-bold">Regular Users</h5>
                <h1 class="card-text display-4 fw-bold text-info">{{ \App\Models\User::where('role', 'user')->count() }}</h1>
                <p class="text-muted">Standard users</p>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-6 mb-4">
        <div class="card shadow-lg border-0">
            <div class="card-header bg-gradient-primary text-white">
                <h5 class="mb-0"><i class="fas fa-chart-line"></i> Recent Activity</h5>
            </div>
            <div class="card-body">
                <p class="text-muted">Latest user registrations and activities will appear here.</p>
                <small class="text-muted">Feature coming soon...</small>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-4">
        <div class="card shadow-lg border-0">
            <div class="card-header bg-gradient-success text-white">
                <h5 class="mb-0"><i class="fas fa-bolt"></i> Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.users') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-users"></i> Manage Users
                    </a>
                    <button class="btn btn-outline-secondary btn-lg" disabled>
                        <i class="fas fa-cog"></i> System Settings
                    </button>
                    <button class="btn btn-outline-secondary btn-lg" disabled>
                        <i class="fas fa-chart-bar"></i> View Reports
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection