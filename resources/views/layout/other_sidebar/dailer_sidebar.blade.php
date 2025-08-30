
<ul class="sidebar-menu">
    <li class="dropdown {{ request()->is('orders') ? 'active' : '' }}" >
        <a href="{{ route('inventory.products.orders') }}" class="nav-link"><i class="fa fa-list-alt" aria-hidden="true"></i><span>Orders</span></a>
    </li>
    @php
        $orders = DB::table('orders')
        ->where('status', '0')
        ->count();

        $dispatched = DB::table('order_dispatched_records')->pluck('order_id');

        $shipments = DB::table('orders')->where('type', 'Normal')->whereIn('status', ['4','5'])
        ->whereNotIn('id', $dispatched)
        ->count();
    @endphp
    <li class="dropdown {{ request()->is('orders') ? 'active' : '' }}" >
        <a href="{{ route('inventory.products.order_record') }}" class="nav-link"><i class="fas fa-save"></i><span>Orders Record</span>
            @if ( $orders > 0)
            <span class="badge headerBadge1"
                style="width:35px; color:white;top: 0px; right: 40px;font-size:14px; font-weight: 700; padding: 7px 0px; background: rgb(102, 119, 239); border-radius: 20px; position: absolute;">
                {{ $orders }}
            </span>
        @endif
        </a>
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

    @if( auth()->user()->role == 'auditor')
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

        @php
            $tickets = DB::table('tickets')
            ->where('status', '!=', 'Closed')
            ->orWhere('status', '=', 'Expired')
            ->count();
        @endphp
     <li class="dropdown" >
         <a href="{{ route('tickets') }}" class="nav-link"><i
                 class="fas fa-ticket-alt"></i><span>Tickets</span>
                 @if ( $tickets > 0)
                 <span class="badge headerBadge1"
                     style="width:35px; color:white;top: 0px; right: 40px;font-size:14px; font-weight: 700; padding: 7px 0px; background: rgb(102, 119, 239); border-radius: 20px; position: absolute;">
                     {{ $tickets }}
                 </span>
             @endif
             </a>
     </li>
    @endif

    <li class="dropdown {{ request()->is('dispatchs') ? 'active' : '' }}" >
        <a href="{{ route('inventory.products.orders.dispatchs') }}" class="nav-link"><i class="fa fa-box" aria-hidden="true"></i><span>Shipments</span>
            @if ( $shipments > 0)
            <span class="badge headerBadge1"
                style="width:35px; color:white;top: 0px; right: 40px;font-size:14px; font-weight: 700; padding: 7px 0px; background: rgb(102, 119, 239); border-radius: 20px; position: absolute;">
                {{ $shipments }}
            </span>
        @endif
        </a>
    </li>

    <li><a class="nav-link" href="{{ route('reports.fis') }}">
        <i data-feather="file-text"></i>FIS</a></li>

</ul>
