
<ul class="sidebar-menu">
    <li class="dropdown {{ request()->is('orders') ? 'active' : '' }}" >
        <a href="{{ route('inventory.products.orders') }}" class="nav-link"><i class="fa fa-list-alt" aria-hidden="true"></i><span>Orders</span></a>
    </li>

    <li class="dropdown {{ request()->is('orders') ? 'active' : '' }}" >
        <a href="{{ route('inventory.products.order_record') }}" class="nav-link"><i class="fas fa-save"></i><span>Orders Record</span></a>
    </li>

    <li class="dropdown">
        <a href="#" class="menu-toggle nav-link has-dropdown"><i
                class="fas fa-sign-out-alt"></i><span>Check Out's</span></a>
                <ul class="dropdown-menu">
                    <li><a class="nav-link" href="{{ route('inventory.products.store.check_out') }}">
                        <i data-feather="file-text"></i>Direct Sale's</a></li>
                    <li><a class="nav-link" href="{{ route('inventory.products.check_out.return_record') }}">
                        <i data-feather="file-text"></i>Record</a></li>
                </ul>
    </li>

</ul>
