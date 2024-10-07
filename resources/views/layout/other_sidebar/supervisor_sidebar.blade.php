
<ul class="sidebar-menu">


    <li class="dropdown {{ request()->is('products') ? 'active' : '' }}" >
        <a href="{{ route('inventory.products') }}" class="nav-link"><i class="fa fa-box" aria-hidden="true"></i><span>Products</span></a>
    </li>

    <ul class="sidebar-menu">
        <li class="dropdown {{ request()->is('orders') ? 'active' : '' }}" >
            <a href="{{ route('inventory.products.orders') }}" class="nav-link"><i class="fa fa-book" aria-hidden="true"></i><span>Orders</span></a>
        </li>

    </ul>


    <li class="dropdown" >
        <a href="{{ route('tickets') }}" class="nav-link"><i
                class="fas fa-ticket-alt"></i><span>Tickets</span></a>
    </li>


    <li class="dropdown">
        <a href="#" class="menu-toggle nav-link has-dropdown"><i
                class="fa fa-warehouse"></i><span>Inventory</span></a>
                <ul class="dropdown-menu">
                    <li><a class="nav-link" href="{{ route('inventory.products.purchase_orders.requests') }}">
                        <i data-feather="file-text"></i>Purchase Order's</a></li>
                    <li><a class="nav-link" href="{{ route('inventory.products.store.stock') }}">
                        <i class="fas fa-boxes"></i>Stock</a></li>
                </ul>

    </li>


    <li class="dropdown">
    <a href="#" class="menu-toggle nav-link has-dropdown"><i class="fas fa-pencil-alt"></i><span>Account</span></a>
    <ul class="dropdown-menu">
        <li class="dropdown">
            <a href="#" class="has-dropdown"><i class="far fa-user"></i><span>Add Ledger</span></a>
            <ul class="dropdown-menu">
                <li>
                    <a href="{{route('account.group')}}" class="nav-link"><i class="far fa-dot-circle"></i><span>Tier 3/4</span></a
                        >
                </li>
                <li>
                    <a href="{{route('account.head')}}" class="nav-link"><i class="far fa-dot-circle"></i><span>Ledger</span></a
                        >
                </li>
                <li>
                    <a href="{{route('account.head.bank')}}" class="nav-link"><i class="far fa-dot-circle"></i><span>Bank-Ledger</span></a
                        >
                </li>
                <li>
                    <a href="{{route('account.head.cash')}}" class="nav-link"><i class="far fa-dot-circle"></i><span>Cash-Ledger</span></a
                        >
                </li>
            </ul>
        </li>
        <li class="dropdown">
            <a href="#" class="has-dropdown"><i class="far fa-money-bill-alt"></i><span>Transaction</span></a>
            <ul class="dropdown-menu">
                <li>
                    <a href="{{route('account.transaction.bank.transactions')}}" class="nav-link"><i class="far fa-dot-circle"></i><span>Bank Transaction</span></a
                        >
                </li>
                <li>
                    <a href="{{route('account.transaction.cash.transactions')}}" class="nav-link"><i class="far fa-dot-circle"></i><span>Cash Transaction</span></a
                        >
                </li>
                <li>
                    <a href="{{route('account.transaction.journal.transactions')}}" class="nav-link"><i class="far fa-dot-circle"></i><span>Journal Transaction</span></a
                        >
                </li>
            </ul>
        </li>
        <li class="dropdown">
            <a href="#" class="has-dropdown"><i class="far fa-file-alt"></i><span>Reports</span></a>
            <ul class="dropdown-menu">
                <li><a href="{{route('account.report.finance')}}" class="nav-link"><i class="far fa-dot-circle"></i><span>Finance</span></a>
                </li>
            </ul>
        </li>
    </ul>
    </li>

</ul>
