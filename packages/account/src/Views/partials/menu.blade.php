<li class="nav-item">
    <a href="{{ route("account.transactions.index") }}" class="nav-link {{ request()->is('account/transactions') ? 'active' : '' }}">
        <i class="fas fa-cogs nav-icon">

        </i>
        {{ trans('global.transaction.title') }}
    </a>
</li>

<li class="nav-item">
    <a href="{{ route("account.transactions.summary") }}" class="nav-link {{ request()->is('account/transactions/summary') ? 'active' : '' }}">
        <i class="fas fa-cogs nav-icon">

        </i>
        {{ trans('global.transaction.account') }}
    </a>
</li>

<li class="nav-item">
    <a href="{{ route("account.import.create") }}" class="nav-link {{ request()->is('account/import/create') ? 'active' : '' }}">
        <i class="fas fa-cogs nav-icon">

        </i>
        {{ trans('global.import.title') }}
    </a>
</li>

<li class="nav-item nav-dropdown">
    <a class="nav-link  nav-dropdown-toggle">
        <i class="fas fa-users nav-icon">

        </i>
        Report
    </a>
    <ul class="nav-dropdown-items">

        @if(auth()->user()->isAdmin())
            <li class="nav-item">
                <a href="{{ route('account.reports.employee-summary') }}" class="nav-link">
                    <i class="nav-icon fas fa-user-clock"></i>
                    Employee summary
                </a>
            </li>
        @endif
    </ul>
</li>

<li class="nav-item">
    <a href="{{ route("account.comment-templates.index") }}" class="nav-link {{ request()->is('account/comment-templates/*') ? 'active' : '' }}">
        <i class="fas fa-copy nav-icon">

        </i>
        Comment templates
    </a>
</li>