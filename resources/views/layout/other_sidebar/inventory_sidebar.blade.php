
<ul class="sidebar-menu">
    <li class="dropdown {{ request()->is('orders') ? 'active' : '' }}" >
        <a href="{{ route('inventory.products.purchase_orders') }}" class="nav-link"><i class="fas fa-file-powerpoint" aria-hidden="true"></i><span>Purchase Order's</span></a>
    </li>

    <li class="dropdown {{ request()->is('orders') ? 'active' : '' }}" >
        <a href="{{ route('inventory.products.store.stock') }}" class="nav-link"><i class="fas fa-boxes" aria-hidden="true"></i><span>Stock</span></a>
    </li>


  

    <li class="dropdown">
        <a href="#" class="menu-toggle nav-link has-dropdown"><i
                class="fa fa-undo"></i><span>Return's</span></a>
                <ul class="dropdown-menu">
                    <li><a class="nav-link" href="{{ route('inventory.products.store.returns') }}">
                        <i data-feather="file-text"></i>Courier Returns</a></li>
                    <li><a class="nav-link" href="{{ route('inventory.products.store.return_record') }}">
                        <i data-feather="file-text"></i>Record</a></li>
                </ul>
    </li>

    <li class="dropdown">
        <a href="#" class="menu-toggle nav-link has-dropdown"><i
                class="fa fa-bell"></i><span>Good Receive's</span></a>
                <ul class="dropdown-menu">
                    <li><a class="nav-link" href="{{ route('inventory.products.store.purchase_orders') }}">
                        <i data-feather="file-text"></i>Inward</a></li>
                    <li><a class="nav-link" href="{{ route('inventory.products.store.record') }}">
                        <i data-feather="file-text"></i>Record</a></li>
                </ul>
    </li>

</ul>
