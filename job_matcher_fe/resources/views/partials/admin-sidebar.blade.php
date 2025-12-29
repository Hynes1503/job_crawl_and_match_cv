<nav class="admin-sidebar d-none d-md-block">
    <div class="sidebar-sticky">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link active" href="{{ route('admin.dashboard') }}">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.users') }}">
                    <i class="fas fa-users"></i> Users
                </a>
            </li>
            <!-- Thêm menu khác nếu cần -->
        </ul>
    </div>
</nav>