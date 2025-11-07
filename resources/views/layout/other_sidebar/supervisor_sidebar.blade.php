
<ul class="sidebar-menu">


    <ul class="sidebar-menu">
        <li class="dropdown {{ request()->is('orders') ? 'active' : '' }}" >
            <a href="{{ route('inventory.products.orders') }}" class="nav-link"><i class="fa fa-book" aria-hidden="true"></i><span>Orders</span></a>
        </li>

    </ul>

    <li class="dropdown {{ request()->routeIs('dropshipper.payouts') ? 'active' : '' }}" >
        <a href="{{ route('dropshipper.payouts') }}" class="nav-link"><i class="fas fa-money-check" aria-hidden="true"></i><span>Pay Out's</span></a>
    </li>


    <li class="dropdown">
        <a href="#" class="menu-toggle nav-link has-dropdown"><i
                class="fa fa-warehouse"></i><span>Inventory</span></a>
                <ul class="dropdown-menu">
                    <li><a class="nav-link" href="{{ route('inventory.products.purchase_orders.requests') }}">
                        <i data-feather="file-text"></i>Purchase Order's</a></li>

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

                    <li><a class="nav-link" href="{{ route('inventory.products.store.stock') }}">
                        <i class="fas fa-boxes"></i>Stock</a></li>
                </ul>

                <!-- <li class="dropdown">
                    <a href="#" class="menu-toggle nav-link has-dropdown"><i
                            class="fa fa-undo"></i><span>Return's</span></a>
                            <ul class="dropdown-menu">
                                <li><a class="nav-link" href="{{ route('inventory.products.store.returns') }}">
                                    <i data-feather="file-text"></i>Courier Returns</a></li>
                                <li><a class="nav-link" href="{{ route('inventory.products.store.return_record') }}">
                                    <i data-feather="file-text"></i>Record</a></li>
                            </ul>
                </li> -->

    </li>


    <li class="dropdown">
        <a href="#" class="menu-toggle nav-link has-dropdown"><i
                class="fas fa-tachometer-alt"></i><span>Report's</span></a>
                <ul class="dropdown-menu">
                    <li><a class="nav-link" href="{{ route('reports.fis') }}">
                        <i data-feather="file-text"></i>FIS</a></li>
                    <li><a href="{{route('account.report.finance')}}" class="nav-link">
                        <i data-feather="file-text"></i><span>Finance</span></a>
                        </li>
                </ul>
    </li>

</ul>
