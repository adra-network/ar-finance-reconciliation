<li class="nav-item">
    <a href="{{ route("card.transactions.index") }}" class="nav-link {{ request()->is('card/transactions') ? 'active' : '' }}">
        <i class="fas fa-cogs nav-icon">

        </i>
        {{ trans('global.transactions') }}
    </a>
</li>