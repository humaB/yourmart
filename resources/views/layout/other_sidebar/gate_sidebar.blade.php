
<ul class="sidebar-menu">
    <li class="dropdown {{ request()->is('orders') ? 'active' : '' }}" >
        <a href="{{ route('inventory.products.gate.purchase_orders') }}" class="nav-link"><i class="fas fa-file-powerpoint" aria-hidden="true"></i><span>Purchase Order's</span></a>
    </li>

    <li class="dropdown {{ request()->is('orders') ? 'active' : '' }}" >
        <a href="{{ route('inventory.products.gate.record') }}" class="nav-link"><i class="fas fa-door-open" aria-hidden="true"></i><span>Gate Inward Record</span></a>
    </li>

</ul>
