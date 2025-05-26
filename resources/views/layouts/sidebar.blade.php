<!-- Sidebar Menu -->
<nav class="mt-4">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false"
        id="sidebarnav">
        <!-- Add icons to the links using the .nav-icon class
             with font-awesome or any other icon font library -->

        @if (Session::has('outlet_slug'))
            <li class="nav-item">
                <a href="{{ route('outlet.dashboard', Session::get('outlet_slug')) }}"
                    class="nav-link {{ request()->is('outlet/' . Session::get('outlet_slug') . '/dashboard') ? 'active' : '' }}">
                    <i class="nav-icon icon-dashboard"></i>
                    <p>
                        @lang('menu.dashboard')
                    </p>
                </a>
            </li>
        @else
            <li class="nav-item">
                <a href="{{ route('admin.dashboard') }}"
                    class="nav-link {{ request()->is('account/dashboard*') ? 'active' : '' }}">
                    <i class="nav-icon icon-dashboard"></i>
                    <p>
                        @lang('menu.dashboard')
                    </p>
                </a>
            </li>
        @endif
        
        {{-- @if ($user->roles()->withoutGlobalScopes()->first()->hasPermission('read_branch'))
            <li class="nav-item">
                <a href="{{ route('admin.branches.index') }}"
                    class="nav-link {{ request()->is('account/branches*') ? 'active' : '' }}">
                    <i class="nav-icon icon-map-alt"></i>
                    <p>
                        @lang('menu.branches')
                    </p>
                </a>
            </li>
        @endif --}}

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
            $user->roles()->withoutGlobalScopes()->first()->hasPermission('create_coupon') ||
                $user->roles()->withoutGlobalScopes()->first()->hasPermission('read_coupon'))
            <li class="nav-item">
                <a href="{{ route('admin.coupons.index') }}"
                    class="nav-link {{ request()->is('account/coupons*') ? 'active' : '' }}">
                    <i class="nav-icon fa  fa-tags"></i>
                    <p>
                        @lang('menu.coupons')
                    </p>
                </a>
            </li>
        @endif

        @if (
            $user->roles()->withoutGlobalScopes()->first()->hasPermission('create_voucher') ||
                $user->roles()->withoutGlobalScopes()->first()->hasPermission('read_voucher'))
            <li class="nav-item">
                <a href="{{ route('admin.vouchers.index') }}"
                    class="nav-link {{ request()->is('account/vouchers*') ? 'active' : '' }}">
                    <i class="nav-icon fa fa-ticket"></i>

                    <p>
                        @lang('menu.vouchers')
                    </p>
                </a>
            </li>
        @endif


        @if ($user->roles()->withoutGlobalScopes()->first()->hasPermission('read_product'))
            <li class="nav-item">
                <a href="{{ route('admin.products.index') }}"
                    class="nav-link {{ request()->is('account/products*') ? 'active' : '' }}">
                    <i class="nav-icon fa fa-shopping-cart"></i>
                    <p>Products</p>
                </a>
            </li>
        @endif

        @if ($user->roles()->withoutGlobalScopes()->first()->hasPermission('read_loyalty_shop'))
            <li class="nav-item">
                <a href="{{ route('admin.loyalty-shop.index') }}"
                    class="nav-link {{ request()->is('account/loyalty-shop*') ? 'active' : '' }}">
                    <i class="nav-icon fa fa-shopping-cart"></i>
                    <p>Loyalty Shop</p>
                </a>
            </li>
        @endif

        @if ($user->roles()->withoutGlobalScopes()->first()->hasPermission('read_promotion'))
            <li class="nav-item">
                <a href="{{ route('admin.promotion.index') }}"
                    class="nav-link {{ request()->is('account/promotion*') ? 'active' : '' }}">
                    <i class="nav-icon fa fa-handshake-o"></i>
                    <p>
                        @lang('menu.promotion')
                    </p>
                </a>
            </li>         
        @endif

        @if ($user->roles()->withoutGlobalScopes()->first()->hasPermission('read_happening'))
            <li class="nav-item">
                <a href="{{ route('admin.happening.index') }}"
                    class="nav-link {{ request()->is('account/happening*') ? 'active' : '' }}">
                    <i class="nav-icon icon-book"></i>
                    <p>
                        @lang('menu.happening')
                    </p>
                </a>
            </li>
        @endif


        @if (
            $user->roles()->withoutGlobalScopes()->first()->hasPermission('create_package') ||
                $user->roles()->withoutGlobalScopes()->first()->hasPermission('read_package'))
            <li class="nav-item">
                <a href="{{ route('admin.packages.index') }}"
                    class="nav-link {{ request()->is('account/packages*') ? 'active' : '' }}">
                    <i class="nav-icon fa fa-ticket"></i>

                    <p>
                        @lang('menu.packages')
                    </p>
                </a>
            </li>
        @endif

        @if (
            $user->roles()->withoutGlobalScopes()->first()->hasPermission('create_offer') ||
                $user->roles()->withoutGlobalScopes()->first()->hasPermission('read_offer'))
            <li class="nav-item">
                <a href="{{ route('admin.offers.index') }}"
                    class="nav-link {{ request()->is('account/offers*') ? 'active' : '' }}">
                    <i class="nav-icon fa fa-gift"></i>
                    <p>
                        @lang('menu.offers')
                    </p>
                </a>
            </li>
        @endif

        {{-- @if (
            $user->roles()->withoutGlobalScopes()->first()->hasPermission('create_deal') ||
                $user->roles()->withoutGlobalScopes()->first()->hasPermission('read_deal'))
            <li class="nav-item">
                <a href="{{ route('admin.deals.index') }}"
                    class="nav-link {{ request()->is('account/deals*') ? 'active' : '' }}">
                    <i class="nav-icon fa fa-handshake-o"></i>
                    <p>
                        @lang('menu.deals')
                    </p>
                </a>
            </li>
        @endif --}}

        @if ($user->roles()->withoutGlobalScopes()->first()->hasPermission('create_booking'))
            <li class="nav-item">
                <a href="{{ route('admin.pos.create') }}"
                    class="nav-link {{ request()->is('account/pos*') ? 'active' : '' }}">
                    <i class="nav-icon icon-shopping-cart"></i>
                    <p>
                        @lang('menu.pos')
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

        {{-- @if ($user->is_admin || $user->is_employee)
            <li class="nav-item">
                <a href="{{ route('admin.todo-items.index') }}"
                    class="nav-link {{ request()->is('account/todo-items*') ? 'active' : '' }}">
                    <i class="nav-icon icon-notepad"></i>
                    <p>
                        @lang('menu.todoList')
                    </p>
                </a>
            </li>
        @endif --}}

        @if ($user->is_admin)
            <li class="nav-item">
                <a href="{{ route('admin.todo-items.index') }}"
                    class="nav-link {{ request()->is('account/todo-items*') ? 'active' : '' }}">
                    <i class="nav-icon icon-notepad"></i>
                    <p>
                        @lang('menu.todoList')
                    </p>
                </a>
            </li>
        @endif

        @if ($user->roles()->withoutGlobalScopes()->first()->hasPermission('read_report'))
            <li class="nav-item">
                <a href="{{ route('admin.reports.index') }}"
                    class="nav-link {{ request()->is('account/reports*') ? 'active' : '' }}">
                    <i class="nav-icon icon-pie-chart"></i>
                    <p>
                        @lang('menu.reports')
                    </p>
                </a>
            </li>
        @endif

        @if ($user->roles()->withoutGlobalScopes()->first()->hasPermission('read_discover'))
            <li class="nav-item">
                <a href="{{ route('admin.discover.index') }}"
                    class="nav-link {{ request()->is('account/discover*') ? 'active' : '' }}">
                    <i class="nav-icon icon-search"></i>
                    <p>
                        @lang('menu.discover')
                    </p>
                </a>
            </li>
        @endif

        @if ($user->roles()->withoutGlobalScopes()->first()->hasPermission('read_faqs'))
            <li class="nav-item">
                <a href="{{ route('admin.faq.index') }}"
                    class="nav-link {{ request()->is('account/faq*') ? 'active' : '' }}">
                    <i class="nav-icon icon-info"></i>
                    <p>
                        @lang('menu.faq')
                    </p>
                </a>
            </li>
        @endif        

        @if ($user->roles()->withoutGlobalScopes()->first()->hasPermission('read_feedbacks'))
            <li class="nav-item">
                <a href="{{ route('admin.feedback.index') }}"
                    class="nav-link {{ request()->is('account/feedback*') ? 'active' : '' }}">
                    <i class="nav-icon bi-yelp"></i>
                    <p>
                        @lang('menu.feedback')
                    </p>
                </a>
            </li>
        @endif

        @if ($user->roles()->withoutGlobalScopes()->first()->hasPermission('manage_settings'))
            <li class="nav-item">
                <a href="{{ route('admin.settings.index') }}"
                    class="nav-link {{ request()->is('account/settings*') ? 'active' : '' }}">
                    <i class="nav-icon icon-settings"></i>
                    <p>
                        @lang('menu.settings')
                    </p>
                </a>
            </li>
        @endif

    </ul>
</nav>
<!-- /.sidebar-menu -->
