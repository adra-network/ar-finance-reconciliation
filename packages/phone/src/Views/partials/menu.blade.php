<li class="nav-item">
    <a href="{{ route("phone.transactions.index") }}"
       class="nav-link {{ request()->is('phone/transactions') ? 'active' : '' }}">
        <i class="fas fa-cogs nav-icon">

        </i>
        {{ trans('global.transactions') }}
    </a>
    <a href="{{ route("phone.import.create") }}" class="nav-link {{ request()->is('phone/import') ? 'active' : '' }}">
        <i class="fas fa-file-import nav-icon">

        </i>
        {{ trans('global.import.title') }}
    </a>
    @if(auth()->user()->isAdmin())
        <a href="{{ route("phone.caller-numbers.index") }}"
           class="nav-link {{ request()->is('phone/caller-numbers') ? 'active' : '' }}">
            <i class="fas fa-phone-square nav-icon">

            </i>
            Caller numbers
        </a>
    @endif
    <a href="{{ route("phone.allocations.index") }}" class="nav-link {{ request()->is('phone/allocations') ? 'active' : '' }}">
        <i class="fas fa-money-check nav-icon">

        </i>
        {{ trans('global.allocations.title') }}
    </a>
</li>