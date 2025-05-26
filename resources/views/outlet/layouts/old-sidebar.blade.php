<!-- Sidebar Menu -->
<nav class="mt-4">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false"
        id="sidebarnav">
        <!-- Add icons to the links using the .nav-icon class
             with font-awesome or any other icon font library -->
        <li class="nav-item">
            <a href="{{ route('outlet.dashboard', request()->route('outlet_slug')) }}"
                class="nav-link {{ request()->is('outlet/' . request()->route('outlet_slug') . '/dashboard') ? 'active' : '' }}">
                <i class="nav-icon icon-dashboard"></i>
                <p>
                    @lang('menu.dashboard')
                </p>
            </a>
        </li>

        @if ($user->roles()->withoutGlobalScopes()->first()->hasPermission('read_outlet'))
            <li class="nav-item">
                <a href="{{ route('outlet.outlet.index', request()->route('outlet_slug')) }}"
                    class="nav-link {{ request()->is('outlet/' . request()->route('outlet_slug') . '/outlet') ? 'active' : '' }}">
                    <i class="nav-icon bi bi-shop"></i>
                    <p>
                        @lang('menu.outlet')
                    </p>
                </a>
            </li>
        @endif

        @if ($user->roles()->withoutGlobalScopes()->first()->hasPermission('read_category'))
            <li class="nav-item">
                <a href="{{ route('outlet.categories.index', request()->route('outlet_slug')) }}"
                    class="nav-link {{ request()->is('outlet/' . request()->route('outlet_slug') . '/categories') ? 'active' : '' }}">
                    <i class="nav-icon icon-list"></i>
                    <p>
                        @lang('menu.categories')
                    </p>
                </a>
            </li>
        @endif

        @if ($user->roles()->withoutGlobalScopes()->first()->hasPermission('read_business_service'))
            <li class="nav-item">
                <a href="{{ route('outlet.business-services.index', request()->route('outlet_slug')) }}"
                    class="nav-link {{ request()->is('outlet/' . request()->route('outlet_slug') . '/categories') ? 'active' : '' }}">
                    <i class="nav-icon fa fa-wrench"></i>
                    <p>
                        @lang('menu.services')
                    </p>
                </a>
            </li>
        @endif

        @if ($user->roles()->withoutGlobalScopes()->first()->hasPermission('read_customer'))
            <li class="nav-item">
                <a href="{{ route('outlet.customers.index', request()->route('outlet_slug')) }}"
                    class="nav-link {{ request()->is('outlet/' . request()->route('outlet_slug') . '/customers') ? 'active' : '' }}">
                    <i class="nav-icon icon-user"></i>
                    <p>
                        @lang('menu.customers')
                    </p>
                </a>
            </li>
        @endif

        @if ($user->roles()->withoutGlobalScopes()->first()->hasPermission('read_employee'))
            <li class="nav-item">
                <a href="{{ route('outlet.employee.index', request()->route('outlet_slug')) }}"
                    class="nav-link {{ request()->is('outlet/' . request()->route('outlet_slug') . '/employee') ? 'active' : '' }}">
                    <i class="nav-icon icon-user"></i>
                    <p>
                        @lang('menu.employee')
                    </p>
                </a>
            </li>
        @endif

        @if (
            $user->roles()->withoutGlobalScopes()->first()->hasPermission('read_booking') ||
                $user->roles()->withoutGlobalScopes()->first()->hasPermission('create_booking'))
            <li class="nav-item">
                <a href="{{ route('outlet.bookings.index', request()->route('outlet_slug')) }}"
                    class="nav-link {{ request()->is('outlet/' . request()->route('outlet_slug') . '/bookings') ? 'active' : '' }}">
                    <i class="nav-icon icon-calendar"></i>
                    <p>
                        @lang('menu.bookings')
                    </p>
                </a>
            </li>
        @endif

    </ul>
</nav>
<!-- /.sidebar-menu -->
