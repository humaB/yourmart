
<ul class="sidebar-menu">
    <li class="dropdown {{ request()->is('products') ? 'active' : '' }}" >
        <a href="{{ route('inventory.products') }}" class="nav-link"><i class="fa fa-list-alt" aria-hidden="true"></i><span>Products</span></a>
    </li>

    <ul class="sidebar-menu">
        <li class="dropdown {{ request()->is('orders') ? 'active' : '' }}" >
            <a href="{{ route('inventory.products.orders') }}" class="nav-link"><i class="fa fa-list-alt" aria-hidden="true"></i><span>Orders</span></a>
        </li>

    </ul>

    <li class="dropdown {{ request()->is('users') ? 'active' : '' }}" >
        <a href="{{ route('user') }}" class="nav-link"><i
                class="fas fa-user-alt"></i><span>Users</span></a>
    </li>

    <li class="dropdown">
        <a href="#" class="menu-toggle nav-link has-dropdown"><i
                class="fa fa-bell"></i><span>Request's</span></a>
                <ul class="dropdown-menu">
                    <li><a class="nav-link" href="{{ route('request.dropshipper') }}">
                        <i data-feather="file-text"></i>Dropshippers</a></li>
                    <li><a class="nav-link" href="{{ route('request.supplier') }}">
                        <i data-feather="file-text"></i>Suppliers</a></li>
                </ul>
    </li>

    <li class="dropdown">
        <a href="#" class="menu-toggle nav-link has-dropdown"><i
                class="fa fa-cog"></i><span>Setting's</span></a>
                <ul class="dropdown-menu">
                    <li><a class="nav-link" href="{{ route('inventory.products.moq') }}">
                        <i data-feather="file-text"></i>Minimum Order Qty</a></li>
                    <li><a class="nav-link" href="{{ route('inventory.products.shipping_classes') }}">
                        <i data-feather="file-text"></i>Shipping Classes</a></li>
                    <li><a class="nav-link" href="{{ route('couriers') }}">
                        <i data-feather="file-text"></i>Couriers</a></li>
                </ul>
    </li>
</ul>
