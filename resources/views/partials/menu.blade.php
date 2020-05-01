<div class="sidebar">
    <nav class="sidebar-nav">

        <ul class="nav">

            @includeWhen($packageNamespace, $packageNamespace . '::partials.menu')

            @can('user_management_access')
                <li class="nav-item nav-dropdown">
                    <a class="nav-link  nav-dropdown-toggle">
                        <i class="fas fa-users nav-icon">

                        </i>
                        {{ trans('global.userManagement.title') }}
                    </a>
                    <ul class="nav-dropdown-items">

                        <li class="nav-item">
                            <a href="{{ route('account.late-transactions.index') }}" class="nav-link">
                                <i class="nav-icon fas fa-user-clock"></i>
                                Late transactions
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route("account.accounts.index") }}" class="nav-link {{ request()->is('account/accounts') || request()->is('account/accounts/*') ? 'active' : '' }}">
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
                        <li class="nav-item">
                            <a href="{{ route('account.send-pdfs.index') }}" class="nav-link">
                                <i class="nav-icon fas fa-user-clock"></i>
                                Send PDFs
                            </a>
                        </li>
                    </ul>
                </li>
            @endcan
            <li class="nav-item">
                <a href="{{ route('admin.profile.index') }}" class="nav-link">
                    <i class="nav-icon fas fa-user"></i>
                    Profile
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link" onclick="event.preventDefault(); document.getElementById('logoutform').submit();">
                    <i class="nav-icon fas fa-sign-out-alt">

                    </i>
                    {{ trans('global.logout') }}
                </a>
            </li>
        </ul>
    </nav>
    <button class="sidebar-minimizer brand-minimizer" type="button"></button>
</div>
