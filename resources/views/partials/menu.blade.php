<div class="sidebar">
    <nav class="sidebar-nav ps ps--active-y">

        <ul class="nav">
            <li class="nav-item">
                <a href="{{ route("admin.transactions.index") }}" class="nav-link {{ request()->is('admin/transactions') ? 'active' : '' }}">
                    <i class="fas fa-cogs nav-icon">

                    </i>
                    {{ trans('global.transaction.title') }}
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route("admin.transactions.account") }}" class="nav-link {{ request()->is('admin/transactions/account') ? 'active' : '' }}">
                    <i class="fas fa-cogs nav-icon">

                    </i>
                    {{ trans('global.transaction.account') }}
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route("admin.import.create") }}" class="nav-link {{ request()->is('admin/import/create') ? 'active' : '' }}">
                    <i class="fas fa-cogs nav-icon">

                    </i>
                    {{ trans('global.import.title') }}
                </a>
            </li>
            @can('user_management_access')
                <li class="nav-item nav-dropdown">
                    <a class="nav-link  nav-dropdown-toggle">
                        <i class="fas fa-users nav-icon">

                        </i>
                        {{ trans('global.userManagement.title') }}
                    </a>
                    <ul class="nav-dropdown-items">
                        <li class="nav-item">
                            <a href="{{ route("admin.accounts.index") }}" class="nav-link {{ request()->is('admin/accounts') || request()->is('admin/accounts/*') ? 'active' : '' }}">
                                <i class="fas fa-cogs nav-icon">

                                </i>
                                {{ trans('global.account.title') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route("admin.users.index") }}" class="nav-link {{ request()->is('admin/users') || request()->is('admin/users/*') ? 'active' : '' }}">
                                <i class="fas fa-user nav-icon">

                                </i>
                                {{ trans('global.user.title') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route("admin.audit-logs.index") }}" class="nav-link {{ request()->is('admin/audit-logs') || request()->is('admin/audit-logs/*') ? 'active' : '' }}">
                                <i class="fas fa-file-alt nav-icon">

                                </i>
                                {{ trans('global.auditLog.title') }}
                            </a>
                        </li>
                    </ul>
                </li>
            @endcan
            <li class="nav-item">
                <a href="#" class="nav-link" onclick="event.preventDefault(); document.getElementById('logoutform').submit();">
                    <i class="nav-icon fas fa-sign-out-alt">

                    </i>
                    {{ trans('global.logout') }}
                </a>
            </li>
        </ul>

        <div class="ps__rail-x" style="left: 0px; bottom: 0px;">
            <div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px;"></div>
        </div>
        <div class="ps__rail-y" style="top: 0px; height: 869px; right: 0px;">
            <div class="ps__thumb-y" tabindex="0" style="top: 0px; height: 415px;"></div>
        </div>
    </nav>
    <button class="sidebar-minimizer brand-minimizer" type="button"></button>
</div>