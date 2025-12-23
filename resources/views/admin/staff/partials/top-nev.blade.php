<style>
    .navbar-nav .nav-link {
        font-weight: 500;
        color: #333;
        transition: 0.3s;
    }

    .navbar-nav .nav-link:hover,
    .navbar-nav .nav-link.active {
        color: #0d6efd;
        text-decoration: underline;
    }

    .navbar-brand i {
        color: #0d6efd;
    }
</style>

<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm mb-4">
    <div class="container-fluid d-flex align-items-center">
        <!-- 🔙 Back Button -->
        @if (!request()->routeIs('pos.index'))
            <button type="button" class="btn btn-outline-secondary me-3" onclick="history.back()">
                <i class="fas fa-arrow-left"></i> Back
            </button>
        @endif

        <!-- Brand -->
        <a class="navbar-brand fw-bold text-primary" href="{{ route('pos.index') }}">
            <i class="fas fa-store me-2"></i>Simpl POS
        </a>

        <!-- Toggle Button for Mobile -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <!-- waiter   -->

        @if (Auth::user()->role_id == 4)
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item mx-2">
                        <a class="nav-link  @if (request()->routeIs('pos.index')) active @endif"
                            href="{{ route('pos.index') }}">
                            <i class="fas fa-coffee me-1 text-primary"></i> Menu
                        </a>
                    </li>
                    <li class="nav-item mx-2">
                        <a class="nav-link  @if (request()->routeIs('pos.orders')) active @endif"
                            href="{{ route('pos.orders') }}">
                            <i class="fas fa-receipt me-1 text-primary"></i> Orders
                        </a>
                    </li>


                    @php $branchId = getBranchId(); @endphp
                    <li class="nav-item mx-2">
                        <a class="nav-link @if (request()->routeIs('staff.kds')) active @endif"
                            href="{{ route('staff.kds', ['id' => $branchId]) }}" target="_blank">
                            <i class="fas fa-tv me-1 text-info"></i> KDS
                        </a>
                    </li>


                </ul>

                <!-- Right Side: Logout -->
                <ul class="navbar-nav align-items-center">

                    <li class="nav-item">
                        <form id="logoutForm" action="{{ route('pos.logout') }}" method="GET" class="d-inline">
                            @csrf
                            <button type="button" class="btn btn-outline-danger btn-sm" id="logoutBtn">
                                <i class="fas fa-sign-out-alt me-1"></i> Logout
                            </button>
                        </form>
                    </li>
                </ul>

            </div>
        @endif

        <!-- DispatchER -->
        @if (Auth::user()->role_id == 6)
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">


                    @php $branchId = getBranchId(); @endphp
                    <li class="nav-item mx-2">
                        <a class="nav-link @if (request()->routeIs('staff.kds')) active @endif"
                            href="{{ route('staff.kds', ['id' => $branchId]) }}" target="_blank">
                            <i class="fas fa-tv me-1 text-info"></i> KDS
                        </a>
                    </li>
                    <li class="nav-item mx-2">
                        <a class="nav-link  @if (request()->routeIs('staff.dispach-view')) active @endif"
                            href="{{ route('staff.dispach-view', ['id' => $branchId]) }}" target="_blank">
                            <i class="fas fa-truck me-1 text-warning"></i> Dispatch
                        </a>
                    </li>


                </ul>

                <!-- Right Side: Logout -->
                <ul class="navbar-nav align-items-center">

                    <li class="nav-item">
                        <form id="logoutForm" action="{{ route('pos.logout') }}" method="GET" class="d-inline">
                            @csrf
                            <button type="button" class="btn btn-outline-danger btn-sm" id="logoutBtn">
                                <i class="fas fa-sign-out-alt me-1"></i> Logout
                            </button>
                        </form>
                    </li>
                </ul>

            </div>
        @endif

        <!-- Accountant -->
        @if (Auth::user()->role_id == 5)
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item mx-2">
                        <a class="nav-link  @if (request()->routeIs('pos.index')) active @endif"
                            href="{{ route('pos.index') }}">
                            <i class="fas fa-coffee me-1 text-primary"></i> Menu
                        </a>
                    </li>
                    <li class="nav-item mx-2 position-relative">
                        <a class="nav-link @if(request()->routeIs('pos.orders')) active @endif"
                            href="{{ route('pos.orders') }}">
                            <i class="fas fa-receipt me-1 text-primary"></i> Orders
                        </a>
                        <span class="notif-badge animate-badge countnum" >

                        </span>

                    </li>
                    <li class="nav-item mx-2">
                        <a class="nav-link  @if (request()->routeIs('pos.inventories')) active @endif"
                            href="{{ route('pos.inventories') }}">
                            <i class="fas fa-boxes me-1 text-success"></i> Inventory
                        </a>
                    </li>

                    @php $branchId = getBranchId(); @endphp
                    <li class="nav-item mx-2">
                        <a class="nav-link @if (request()->routeIs('staff.kds')) active @endif"
                            href="{{ route('staff.kds', ['id' => $branchId]) }}" target="_blank">
                            <i class="fas fa-tv me-1 text-info"></i> KDS
                        </a>
                    </li>
                    <li class="nav-item mx-2">
                        <a class="nav-link  @if (request()->routeIs('staff.dispach-view')) active @endif"
                            href="{{ route('staff.dispach-view', ['id' => $branchId]) }}" target="_blank">
                            <i class="fas fa-truck me-1 text-warning"></i> Dispatch
                        </a>
                    </li>
                    <li class="nav-item mx-2">
                        <a class="nav-link  @if (request()->routeIs('pos.cashout')) active @endif"
                            href="{{ route('pos.cashout') }}">
                            <i class="fas fa-money-check-alt me-1"></i> Cashout & Refund
                        </a>
                    </li>
                    <li class="nav-item mx-2">
                        <a class="nav-link" href="javascript:void(0);" id="cashCountLink">
                            <i class="fas fa-chart-line me-1 text-info"></i> Cash Count
                        </a>
                    </li>
                    <li class="nav-item mx-2">
                        <a class="nav-link" href="#" id="stockCountLink">
                            <i class="fas fa-box me-1 text-warning"></i> Stock Count
                        </a>
                    </li>
                </ul>

                <!-- Right Side: Logout -->
                <ul class="navbar-nav align-items-center">

                    <li class="nav-item me-3 d-flex align-items-center">
                        <form id="closeBranchForm" action="{{ route('pos.close.branch') }}" method="POST"
                            class="d-inline">
                            @csrf
                            <input type="hidden" name="branch_id" value="{{ $branchId }}">
                            <button type="submit" class="btn btn-danger btn-sm fw-bold" id="closeBranchBtn">
                                <i class="fas fa-ban me-1"></i> Close Branch
                            </button>
                        </form>
                    </li>

                    <script>
                        document.getElementById('closeBranchBtn').addEventListener('click', function(event) {
                            if (!confirm("Are you sure you want to CLOSE the branch queue?")) {
                                event.preventDefault();
                            }
                        });
                    </script>
                    <li class="nav-item me-3 d-flex align-items-center">
                        <form id="closeShiftForm" action="{{ route('shift.inventory') }}" method="GET"
                            class="d-inline">
                            <button type="submit" class="btn btn-warning btn-sm" id="closeShiftBtn">
                                <i class="fas fa-door-closed me-1"></i> Close Shift
                            </button>
                        </form>
                    </li>

                    <li class="nav-item">
                        <form id="logoutForm" action="{{ route('pos.logout') }}" method="GET" class="d-inline">
                            @csrf
                            <button type="button" class="btn btn-outline-danger btn-sm" id="logoutBtn">
                                <i class="fas fa-sign-out-alt me-1"></i> Logout
                            </button>
                        </form>
                    </li>
                </ul>

            </div>
        @endif


    </div>
</nav>
