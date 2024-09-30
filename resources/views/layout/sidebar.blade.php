<div class="main-sidebar sidebar-style-2">

    <aside id="sidebar-wrapper">
        <div class="sidebar-brand">
            <a href="#"><span
                    class="logo-name">eComm</span>
            </a>
        </div>

        <ul class="sidebar-menu">
            <li class="menu-header">Main</li>
            <li class="dropdown {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <a href="#" class="nav-link"><i
                        data-feather="monitor"></i><span>Dashboard</span></a>
            </li>
        </ul>
        @if( auth()->user()->role == 'admin')
            @include('layout.admin_sidebar.admin_sidebar')
        @endif

        @if( auth()->user()->role == 'dob')
            @include('layout.dob_sidebar.dob_sidebar')
        @endif

        @if( auth()->user()->role == 'order collection manager')
            @include('layout.other_sidebar.dailer_sidebar')
        @endif

    @if( auth()->user()->role == 'inventory manager')
        @include('layout.other_sidebar.dailer_sidebar')
        @include('layout.other_sidebar.inventory_sidebar')
    @endif

    @if( auth()->user()->role == 'qc manager')
        @include('layout.other_sidebar.dailer_sidebar')
    @endif

    @if( auth()->user()->role == 'packing & dispatch manager')
        @include('layout.other_sidebar.dailer_sidebar')
    @endif

    @if( auth()->user()->role == 'gate incharge')
        @include('layout.other_sidebar.gate_sidebar')
    @endif

    </aside>
    <div style="height: 100px"></div>
</div>
