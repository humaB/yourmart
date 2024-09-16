
<ul class="sidebar-menu">
    <li class="dropdown {{ request()->is('products') ? 'active' : '' }}" >
        <a href="{{ route('inventory.products.dropshipper.orders') }}" class="nav-link"><i class="fa fa-list-alt" aria-hidden="true"></i><span>Orders</span></a>
    </li>

</ul>
