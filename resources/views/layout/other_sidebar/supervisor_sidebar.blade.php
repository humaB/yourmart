<!-- 
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

</ul> -->



<ul class="sidebar-menu">
<li class="dropdown {{ request()->is('growthdashboard') ? 'active' : '' }}">
        <a href="{{ route('growthdashboard') }}" class="nav-link">
            <i class="fa fa-warehouse" aria-hidden="true"></i>
            <span>Growth KPIs</span>
        </a>
    </li>

    <li class="dropdown {{ request()->is('supplier') ? 'active' : '' }}">
        <a href="{{ route('supplier.dashboard') }}" class="nav-link">
            <i class="fa fa-warehouse" aria-hidden="true"></i>
            <span>Supplier Dashboard</span>
        </a>
    </li>


    <ul class="sidebar-menu">
    <li class="dropdown {{ request()->is('orders') ? 'active' : '' }}" >
            <a href="{{ route('inventory.products.orders') }}" class="nav-link"><i class="fa fa-book" aria-hidden="true"></i><span>Orders</span></a>
        </li>

        <li class="dropdown {{ request()->is('dispatchs') ? 'active' : '' }}">
            <a href="{{ route('inventory.products.orders.dispatchs') }}" class="nav-link"><i class="fa fa-box"
                    aria-hidden="true"></i><span>Shipments</span>
            </a>
        </li>
        <li class="dropdown {{ request()->routeIs('dropshipper.payouts') ? 'active' : '' }}" >
        <a href="{{ route('dropshipper.payouts') }}" class="nav-link"><i class="fas fa-money-check" aria-hidden="true"></i><span>Pay Out's</span></a>
    </li>


    </ul>


    <li class="dropdown">
        <a href="{{ route('tickets') }}" class="nav-link"><i class="fas fa-ticket-alt"></i><span>Tickets</span>
        
        </a>
    </li>

    <li class="dropdown">
        <a href="#" class="menu-toggle nav-link has-dropdown"><i class="fa fa-bell"></i><span>Request's</span>
          
        </a>
        <ul class="dropdown-menu">
            <li><a class="nav-link" href="{{ route('request.dropshipper') }}">
                    <i data-feather="file-text"></i>Dropshippers
                   
                </a></li>
            <li><a class="nav-link" href="{{ route('request.supplier') }}">
                    <i data-feather="file-text"></i>Suppliers
                   
                </a></li>
        </ul>
    </li>

    <li class="dropdown">
        <a href="#" class="menu-toggle nav-link has-dropdown"><i class="fas fa-sign-out-alt"></i><span>Check
                Out's</span></a>
        <ul class="dropdown-menu">
            <li><a class="nav-link" href="{{ route('inventory.products.store.check_out') }}">
                    <i data-feather="file-text"></i>Direct Sale's</a></li>
            <li><a class="nav-link" href="{{ route('inventory.products.check_out.return_record') }}">
                    <i data-feather="file-text"></i>Record</a></li>
        </ul>
    </li>
    <li class="dropdown">
        <a href="#" class="menu-toggle nav-link has-dropdown"><i
                class="fa fa-warehouse"></i><span>Inventory</span>
           
        </a>
        <ul class="dropdown-menu">
            <li><a class="nav-link" href="{{ route('inventory.products.purchase_orders.requests') }}">
                    <i data-feather="file-text"></i>Purchase Order's
                   
                </a></li>



            <li><a class="nav-link" href="{{ route('inventory.products.store.stock') }}">
                    <i class="fas fa-boxes"></i>Stock</a></li>
        </ul>

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

    </li>


    <!-- <li class="dropdown">
        <a href="#" class="menu-toggle nav-link has-dropdown"><i
                class="fas fa-pencil-alt"></i><span>Account</span></a>
        <ul class="dropdown-menu">
            <li class="dropdown">
                <a href="#" class="has-dropdown"><i class="far fa-user"></i><span>Add Ledger</span></a>
                <ul class="dropdown-menu">
                    <li>
                        <a href="{{ route('account.group') }}" class="nav-link"><i
                                class="far fa-dot-circle"></i><span>Tier 3/4</span></a>
                    </li>
                    <li>
                        <a href="{{ route('account.head') }}" class="nav-link"><i
                                class="far fa-dot-circle"></i><span>Ledger</span></a>
                    </li>
                    <li>
                        <a href="{{ route('account.head.bank') }}" class="nav-link"><i
                                class="far fa-dot-circle"></i><span>Bank-Ledger</span></a>
                    </li>
                    <li>
                        <a href="{{ route('account.head.cash') }}" class="nav-link"><i
                                class="far fa-dot-circle"></i><span>Cash-Ledger</span></a>
                    </li>
                </ul>
            </li>
            <li class="dropdown">
                <a href="#" class="has-dropdown"><i
                        class="far fa-money-bill-alt"></i><span>Transaction</span></a>
                <ul class="dropdown-menu">
                    <li>
                        <a href="{{ route('account.transaction.bank.transactions') }}" class="nav-link"><i
                                class="far fa-dot-circle"></i><span>Bank Transaction</span></a>
                    </li>
                    <li>
                        <a href="{{ route('account.transaction.cash.transactions') }}" class="nav-link"><i
                                class="far fa-dot-circle"></i><span>Cash Transaction</span></a>
                    </li>
                    <li>
                        <a href="{{ route('account.transaction.journal.transactions') }}" class="nav-link"><i
                                class="far fa-dot-circle"></i><span>Journal Transaction</span></a>
                    </li>
                </ul>
            </li>
        </ul>
    </li> -->

    <!-- <li
        class="dropdown {{ request()->routeIs('inventory.products.moq', 'inventory.products.shipping_classes', 'couriers', 'packaging.class', 'inventory.products.other_charges', 'email_template', 'notification') ? 'active' : '' }}">
        <a href="#" class="menu-toggle nav-link has-dropdown"><i class="fa fa-cog"></i>
            <span>Setting's</span>
        </a>
        <ul class="dropdown-menu">
            <li>
                <a class="nav-link {{ request()->routeIs('inventory.products.moq') ? 'active' : '' }}"
                    href="{{ route('inventory.products.moq') }}">
                    <i data-feather="file-text"></i>Minimum Order Qty
                </a>
            </li>
            <li>
                <a class="nav-link {{ request()->routeIs('inventory.products.shipping_classes') ? 'active' : '' }}"
                    href="{{ route('inventory.products.shipping_classes') }}">
                    <i data-feather="file-text"></i>Shipping Classes
                </a>
            </li>
            <li>
                <a class="nav-link {{ request()->routeIs('couriers') ? 'active' : '' }}"
                    href="{{ route('couriers') }}">
                    <i data-feather="file-text"></i>Couriers
                </a>
            </li>
            <li>
                <a class="nav-link {{ request()->routeIs('packaging.class') ? 'active' : '' }}"
                    href="{{ route('packaging.class') }}">
                    <i data-feather="file-text"></i>Packaging Class
                </a>
            </li>
            <li>
                <a class="nav-link {{ request()->routeIs('inventory.products.other_charges') ? 'active' : '' }}"
                    href="{{ route('inventory.products.other_charges') }}">
                    <i data-feather="file-text"></i>Other Charges
                </a>
            </li>
            <li>
                <a class="nav-link {{ request()->routeIs('email_template') ? 'active' : '' }}"
                    href="{{ route('email_template') }}">
                    <i data-feather="file-text"></i>Email Templates
                </a>
            </li>
            <li>
                <a class="nav-link {{ request()->routeIs('notification') ? 'active' : '' }}"
                    href="{{ route('notification') }}">
                    <i data-feather="file-text"></i> Notifications
                </a>
            </li>
        </ul>
    </li> -->


    <li class="dropdown">
        <a href="#" class="menu-toggle nav-link has-dropdown"><i
                class="fas fa-tachometer-alt"></i><span>Report's</span></a>
        <ul class="dropdown-menu">
            <li><a class="nav-link" href="{{ route('reports.fis') }}">
                    <i data-feather="file-text"></i>FIS</a></li>
            <li><a href="{{ route('account.report.finance') }}" class="nav-link">
                    <i data-feather="file-text"></i><span>Finance</span></a>
            </li>
        </ul>
    </li>
</ul>

