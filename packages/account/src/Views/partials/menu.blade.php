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