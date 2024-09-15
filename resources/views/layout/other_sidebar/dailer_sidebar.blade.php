
<ul class="sidebar-menu">
    <li class="dropdown {{ request()->is('orders') ? 'active' : '' }}" >
        <a href="{{ route('inventory.products.orders') }}" class="nav-link"><i class="fa fa-list-alt" aria-hidden="true"></i><span>Orders</span></a>
    </li>

</ul>
