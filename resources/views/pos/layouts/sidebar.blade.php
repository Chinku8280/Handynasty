<!-- Sidebar Menu -->
<nav class="mt-4">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false"
        id="sidebarnav">
        <!-- Add icons to the links using the .nav-icon class
             with font-awesome or any other icon font library -->

        <li class="nav-item">
            <a href="{{ route('pos.dashboard', request()->route('outlet_slug')) }}"
                class="nav-link {{ request()->is('outlet/pos/' . request()->route('outlet_slug') . '/dashboard') ? 'active' : '' }}">
                <i class="nav-icon icon-dashboard"></i>
                <p>
                    @lang('menu.dashboard')
                </p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('pos.bookings', request()->route('outlet_slug')) }}"
                class="nav-link {{ request()->is('outlet/pos/' . request()->route('outlet_slug') . '/bookings') ? 'active' : '' }}"">
                <i class="nav-icon icon-calendar"></i>
                <p>
                    @lang('menu.bookings')
                </p>
            </a>
        </li>

        <li class="nav-item">
            <a href="#"
                class="nav-link {{ request()->is('outlet/pos/' . request()->route('outlet_slug') . '/schedule') ? 'active' : '' }}"">
                <i class="nav-icon icon-calendar"></i>
                <p>
                    Schedule
                </p>
            </a>
        </li>

        <li class="nav-item">
            <a href="#"
                class="nav-link {{ request()->is('outlet/pos/' . request()->route('outlet_slug') . '/session-report') ? 'active' : '' }}"">
                <i class="nav-icon icon-bar-chart"></i>
                <p>
                    Session Report
                </p>
            </a>
        </li>
        
    </ul>
</nav>
<!-- /.sidebar-menu -->
