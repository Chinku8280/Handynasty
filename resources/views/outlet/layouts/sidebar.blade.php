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
                <a href="{{ route('admin.outlet.index') }}"
                    class="nav-link {{ request()->is('account/outlet*') ? 'active' : '' }}">
                    <i class="nav-icon bi bi-shop"></i>
                    <p>
                        @lang('menu.outlet')
                    </p>
                </a>
            </li>
        @endif

        @if ($user->roles()->withoutGlobalScopes()->first()->hasPermission('read_category'))
            <li class="nav-item">
                <a href="{{ route('admin.categories.index') }}"
                    class="nav-link {{ request()->is('account/categories*') ? 'active' : '' }}">
                    <i class="nav-icon icon-list"></i>
                    <p>
                        @lang('menu.categories')
                    </p>
                </a>
            </li>
        @endif

        @if ($user->roles()->withoutGlobalScopes()->first()->hasPermission('read_business_service'))
            <li class="nav-item">
                <a href="{{ route('admin.business-services.index') }}"
                    class="nav-link {{ request()->is('account/business-services*') ? 'active' : '' }}">
                    <i class="nav-icon fa fa-wrench"></i>
                    <p>
                        @lang('menu.services')
                    </p>
                </a>
            </li>
        @endif

        @if ($user->roles()->withoutGlobalScopes()->first()->hasPermission('read_customer'))
            <li class="nav-item">
                <a href="{{ route('admin.customers.index') }}"
                    class="nav-link {{ request()->is('account/customers*') ? 'active' : '' }}">
                    <i class="nav-icon icon-user"></i>
                    <p>
                        @lang('menu.customers')
                    </p>
                </a>
            </li>
        @endif

        @if ($user->roles()->withoutGlobalScopes()->first()->hasPermission('read_employee'))
            <li class="nav-item">
                <a href="{{ route('admin.employee.index') }}"
                    class="nav-link {{ request()->is('account/employee*') ? 'active' : '' }}">
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
                <a href="{{ route('admin.bookings.index') }}"
                    class="nav-link {{ request()->is('account/bookings*') ? 'active' : '' }}">
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
