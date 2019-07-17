<li class="nav-item">
    <a href="{{ route("phone.transactions.index") }}" class="nav-link {{ request()->is('phone/transactions') ? 'active' : '' }}">
        <i class="fas fa-cogs nav-icon">

        </i>
        {{ trans('global.transactions') }}
    </a>
    <a href="{{ route("phone.import.create") }}" class="nav-link {{ request()->is('phone/import') ? 'active' : '' }}">
        <i class="fas fa-cogs nav-icon">

        </i>
        {{ trans('global.import.title') }}
    </a>
</li>