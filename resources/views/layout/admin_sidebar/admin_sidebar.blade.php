
<ul class="sidebar-menu">
    <li class="dropdown {{ request()->is('products') ? 'active' : '' }}" >
        <a href="{{ route('inventory.products') }}" class="nav-link"><i class="fa fa-list-alt" aria-hidden="true"></i><span>Products</span></a>
    </li>

    <li class="dropdown {{ request()->is('users') ? 'active' : '' }}" >
        <a href="{{ route('user') }}" class="nav-link"><i
                class="fas fa-user-alt"></i><span>Users</span></a>
    </li>
</ul>
