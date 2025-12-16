<style>
    span.lan-3 {
        color: rgb(0, 0, 0) !important;
    }

    a.lan-4 {
        color: black !important;
    }

    ::before {
        color: black;
    }

    .simplebar-content-wrapper {
        background: white;
    }

    /* input.form-control,
    textarea.form-control {
        background: #ec322314 !important;
    } */

    input.btn.btn-primary {
        background: red !important;
        border: none !important;
    }

    .page-wrapper.compact-wrapper .page-body-wrapper div.sidebar-wrapper .sidebar-main .sidebar-links .simplebar-wrapper .simplebar-mask .simplebar-content-wrapper .simplebar-content>li .sidebar-link.active {
        -webkit-transition: all 0.5s ease;
        transition: all 0.5s ease;
        position: relative;
        margin-bottom: 10px;
        background-color: transparent !important;
    }

    a.sidebar-link.sidebar-title.clickable:hover {
        background: rgba(128, 128, 128, 0.317) !important;


    }

    .pac-container {
        z-index: 1051 !important;
    }

    /*. Ensure no nested collapse for Constraint */
    #side-nav .side-nav-item>.collapse {
        display: none !important;
    }

    /* Force top-level styling */
    #side-nav .side-nav-item>.side-nav-link {
        padding-left: 1rem;
    }

    .select2-dropdown {
        z-index: 1050 !important;
        /* Bootstrap modal z-index is usually 1050 */
    }

    .select2-container--classic .select2-dropdown {
        z-index: 9999 !important;
        /* Ensure it's above other modal content */
    }

    .select2-container--classic .select2-dropdown {
        width: 100% !important;
        /* Ensure dropdown width matches select element */
    }
</style>

@php
    $userRole = Auth::user()->role->name;
@endphp
<div class="sidebar-wrapper">
    <div>
        <div class="logo-wrapper">
            <a style="font-weight:900; text-decoration: none" href="{{ url('/') }}"
                class=" row justify-content-center">{{ env('APP_NAME') }}</a>
            <div class="back-btn"><i class="fa fa-angle-left"></i></div>
            <div class="toggle-sidebar"><i class="status_toggle middle sidebar-toggle" data-feather="grid"> </i></div>
        </div>
        <div class="logo-icon-wrapper"><a style="font-weight:900;color: #ec3223;text-decoration: none"
                href="{{ url('/') }}">{{ env('APP_NAME') }} App</a></div>
        <nav class="sidebar-main">
            <div class="left-arrow" id="left-arrow"><i data-feather="arrow-left"></i></div>
            <div id="sidebar-menu">
                <ul class="sidebar-links" id="simple-bar">
                    <li class="back-btn">
                        <a style="font-weight:900;color: #ec3223;text-decoration: none" href="{{ url('/') }}">{{ env('APP_NAME') }}
                            App</a>
                        <div class="mobile-back text-end"><span>Back</span><i class="fa fa-angle-right ps-2"
                                aria-hidden="true"></i></div>
                    </li>


                    @if ($userRole === 'admin')
                        <li class="sidebar-list">
                            <label class="badge badge-success"></label><a
                                class="sidebar-link sidebar-title {{ request()->route()->uri == 'admin/dashboard' ? 'active1' : '' }}"
                                href="{{ url('/admin/dashboard') }}"><i data-feather="home"></i><span
                                    class="lan-3">&nbsp&nbspDashboard</span>

                            </a>
                        </li>
                        {{-- <li class="sidebar-list">
                            <label class="badge badge-success"></label>
                            <a class="sidebar-link sidebar-title {{ (request()->segment(1) == 'admin' && request()->segment(2) == 'categories') || request()->segment(2) == 'categories' ? 'active1' : '' }}"
                                href="{{ route('admin.categories.index') }}">
                                <i data-feather="grid"></i> {{-- Categories Icon --}}
                        <span class="lan-6">&nbsp;&nbsp;Categories</span>
                        </a>
                        </li>
                        {{-- <li class="sidebar-list">
                            <label class="badge badge-success"></label>
                            <a class="sidebar-link sidebar-title {{ request()->route()->uri == 'admin/languages' ? 'active1' : '' }}"
                                href="{{ url('/admin/languages') }}">
                                <i data-feather="globe"></i>
                                <span class="lan-6">&nbsp;&nbsp;Languages</span>
                            </a>
                        </li> --}}

                        {{-- <li class="sidebar-list">
                        <label class="badge badge-success"></label><a
                            class="sidebar-link sidebar-title {{ request()->route()->uri == 'admin/language-translations' ? 'active1' : '' }}"
                            href="{{ url('/admin/language-translations') }}"><i data-feather="airplay"></i><span class="lan-6">&nbsp&nbspConstraint Translations</span>

                        </a>
                    </li> --}}
                       <li class="sidebar-list">
                            <label class="badge badge-success"></label>
                            <a class="sidebar-link sidebar-title {{ (request()->segment(1) == 'admin' && request()->segment(2) == 'branches') || request()->segment(2) == 'branch' ? 'active1' : '' }}"
                                href="{{ url('/admin/branches') }}">
                                <i data-feather="git-branch"></i> {{-- Branches Icon --}}
                                <span class="lan-6">&nbsp;&nbsp;Branches</span>
                            </a>
                        </li>
                        <li class="sidebar-list">
                            <label class="badge badge-success"></label>
                            <a class="sidebar-link sidebar-title {{ (request()->segment(1) == 'admin' && request()->segment(2) == 'categories') || request()->segment(2) == 'categories' ? 'active1' : '' }}"
                                href="{{ route('admin.categories.index') }}">
                                <i data-feather="grid"></i> {{-- Categories Icon --}}
                                <span class="lan-6">&nbsp;&nbsp;Categories</span>
                            </a>
                        </li>

                        <li class="sidebar-list">
                            <label class="badge badge-success"></label>
                            <a class="sidebar-link sidebar-title {{ request()->segment(1) == 'admin' && request()->segment(2) == 'products' ? 'active1' : '' }}"
                                href="{{ route('admin.products.index') }}">
                                <i data-feather="package"></i><span class="lan-6">&nbsp&nbspProducts</span>
                            </a>
                        </li>
                            <li class="sidebar-list">
                            <label class="badge badge-success"></label>
                            <a class="sidebar-link sidebar-title {{ request()->segment(2) == 'ingredient' && request()->segment(3) == 'categories' ? 'active1' : '' }}"
                                href="{{ route('admin.ingredient.categories') }}">
                                <i data-feather="layers"></i> {{-- Ingredient Icon --}}
                                <span class="lan-6">&nbsp;&nbsp;Ingredients</span>
                            </a>
                        </li>


                        <li class="sidebar-list">
                            <label class="badge badge-success"></label>
                            <a class="sidebar-link sidebar-title {{ request()->segment(1) == 'admin' && request()->segment(2) == 'size' ? 'active1' : '' }}"
                                href="{{ route('admin.size.index') }}">
                                <i data-feather="maximize"></i><span class="lan-6">&nbsp&nbspSize</span>
                            </a>
                        </li>

                        <li class="sidebar-list">
                            <label class="badge badge-success"></label>
                            <a class="sidebar-link sidebar-title {{ request()->segment(1) == 'admin' && request()->segment(2) == 'unit' ? 'active1' : '' }}"
                                href="{{ route('admin.unit.index') }}">
                                <i data-feather="layers"></i><span class="lan-6">&nbsp&nbspUnits</span>
                            </a>
                        </li>
                        @php
                            $isConstraintRoute =
                                request()->is('admin/constraint') || request()->is('admin/language-translations');
                        @endphp

                        {{-- <li class="sidebar-list">
                            <a class="side-nav-link sidebar-link" data-bs-toggle="collapse" href="#sidebarProducts"
                                role="button" aria-expanded="{{ $isConstraintRoute ? 'true' : 'false' }}"
                                aria-controls="sidebarProducts">
                                <i data-feather="sliders"></i>
                                <span class="lan-6">Constraint</span>

                                <div class="according-menu">
                                    <i
                                        class="fa toggle-arrow {{ $isConstraintRoute ? 'fa-angle-down' : 'fa-angle-up' }}"></i>
                                </div>
                            </a>



                            <div class="collapse {{ $isConstraintRoute ? 'show' : '' }}" id="sidebarProducts">
                                <ul class="sidebar-submenu side-nav-second-level">


                                    <li>
                                        <a href="{{ url('/admin/constraint') }}"
                                            class="sidebar-link sidebar-title {{ request()->is('admin/constraint') ? 'active1' : '' }}">
                                            <i class="fas fa-cog"></i> Manage
                                        </a>
                                    </li>


                                     <li>
                                    <a href="{{ url('/admin/language-translations') }}"
                                    class="sidebar-link sidebar-title {{ request()->is('admin/language-translations') ? 'active1' : '' }}">
                                        <i data-feather="globe"></i>Translations
                                    </a>
                                </li>

                                </ul>
                            </div>

                        </li> --}}
                        <li class="sidebar-list">
                            <label class="badge badge-success"></label>
                            <a class="sidebar-link sidebar-title {{ request()->route()->uri == 'admin/customers' ? 'active1' : '' }}"
                                href="{{ route('admin.customer.list') }}">
                                <i data-feather="users"></i>
                                <span class="lan-6">&nbsp;&nbsp;Customers</span>
                            </a>
                        </li>
                        @php
                            $isorderRoute = request()->is('admin/orders');
                        @endphp

                        <li class="sidebar-list">
                            <a class="side-nav-link sidebar-link" data-bs-toggle="collapse"
                                href="#sidebarProductsorders" role="button"
                                aria-expanded="{{ $isorderRoute ? 'true' : 'false' }}"
                                aria-controls="sidebarProductsorders">
                                <i data-feather="shopping-cart"></i>
                                <span class="lan-6">Orders</span>
                                <div class="according-menu">
                                    <i class="fa toggle-arrow {{ $isorderRoute ? 'fa-angle-down' : 'fa-angle-up' }}"></i>
                                </div>
                            </a>
                            <div class="collapse {{ $isorderRoute ? 'show' : '' }}" id="sidebarProductsorders">
                                <ul class="sidebar-submenu side-nav-second-level">

                                    {{-- Manage --}}
                                    <li>
                                        <a href="{{ route('admin.order.liveorders') }}"
                                            class="sidebar-link sidebar-title {{ request()->is('admin/orders/live') ? 'active1' : '' }}">
                                            <i data-feather="shopping-cart"></i>
                                            <span class="lan-6">&nbsp;&nbsp;Live Orders</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('admin.order.index') }}"
                                            class="sidebar-link sidebar-title {{ request()->is('admin/orders') ? 'active1' : '' }}">
                                            <i data-feather="shopping-cart"></i>
                                            <span class="lan-6">&nbsp;&nbsp;Orders</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>

                        {{-- <li class="sidebar-list">
                            <label class="badge badge-success"></label>
                            <a class="sidebar-link sidebar-title
                                      {{ (request()->segment(1) == 'admin' && request()->segment(2) == 'orders') || request()->segment(2) == 'orders' ? 'active1' : '' }}"
                                href="{{ route('admin.order.index') }}">
                                <i data-feather="shopping-cart"></i>
                                <span class="lan-6">&nbsp;&nbsp;Orders</span>
                            </a>
                        </li> --}}
                        @php
                            $isingRoute = request()->is('admin/ingredient');
                        @endphp


                        <li class="sidebar-list">
                            <label class="badge badge-success"></label><a
                                class="sidebar-link sidebar-title {{ request()->segment(1) == 'admin' && request()->segment(2) == 'coupons' ? 'active1' : '' }}"
                                href="{{ route('admin.coupons.index') }}"><i class="fa fa-gift"
                                    aria-hidden="true"></i>&nbsp&nbspCoupons</span>

                            </a>
                        </li>
                        @php
                            $issettingRoute = request()->is('admin/settings');
                        @endphp

                        <li class="sidebar-list">
                            <a class="side-nav-link sidebar-link" data-bs-toggle="collapse"
                                href="#sidebarissettingRoute" role="button"
                                aria-expanded="{{ $issettingRoute ? 'true' : 'false' }}"
                                aria-controls="sidebarissettingRoute">
                                <i data-feather="sliders"></i>
                                <span class="lan-6">Settings</span>

                                <div class="according-menu">
                                    <i
                                        class="fa toggle-arrow {{ $issettingRoute ? 'fa-angle-down' : 'fa-angle-up' }}"></i>
                                </div>
                            </a>
                            <div class="collapse {{ $issettingRoute ? 'show' : '' }}" id="sidebarissettingRoute">
                                <ul class="sidebar-submenu side-nav-second-level">

                                    {{-- Manage --}}
                                    {{-- <li>
                                        <a href="{{ route('admin.loyaltysettings') }}"
                                            class="sidebar-link sidebar-title {{ request()->is('admin/loyaltysettings') ? 'active1' : '' }}">
                                            <i data-feather="award"></i> &nbsp;&nbsp;Loyalty Points
                                        </a>
                                    </li> --}}

                                    {{-- Payment Method --}}
                                    <li>
                                        <a href="{{ route('admin.paymentmethod') }}"
                                            class="sidebar-link sidebar-title {{ request()->is('admin/paymentmethod') ? 'active1' : '' }}">
                                            <i data-feather="credit-card"></i> &nbsp;&nbsp;Payment Method
                                        </a>
                                    </li>

                                </ul>
                            </div>
                        </li>

                        @php
                            $isConstraintRouteapp = request()->is('admin/banner');
                        @endphp

                        {{-- <li class="sidebar-list">
                            <a class="side-nav-link sidebar-link" data-bs-toggle="collapse" href="#sidebarbanner"
                                role="button" aria-expanded="{{ $isConstraintRouteapp ? 'true' : 'false' }}"
                                aria-controls="sidebarbanner">
                                <i class="fa fa-mobile" aria-hidden="true"></i>
                                <span class="lan-6">App</span>

                                <div class="according-menu">
                                    <i
                                        class="fa toggle-arrow {{ $isConstraintRouteapp ? 'fa-angle-down' : 'fa-angle-up' }}"></i>
                                </div>
                            </a>

                            <div class="collapse {{ $isConstraintRouteapp ? 'show' : '' }}" id="sidebarbanner">
                                <ul class="sidebar-submenu side-nav-second-level">
                                    <li
                                        class="{{ request()->segment(2) == 'admin' && request()->segment(3) == 'banners' ? 'active1' : '' }}">
                                        <a href="{{ route('admin.banners.index') }}"
                                            class="sidebar-link sidebar-title {{ request()->is('admin/banners') ? 'active1' : '' }}">
                                            <i class="fas fa-cog"></i> Banner
                                        </a>
                                    </li>
                                    <li
                                        class="{{ request()->segment(1) == 'admin' && request()->segment(2) == 'app' ? 'active1' : '' }}">
                                        <a href="{{ route('admin.home.product.index') }}"
                                            class="sidebar-link sidebar-title">
                                            <i class="fas fa-cog"></i> Home Page Product
                                        </a>
                                    </li>
                                    <li
                                        class="{{ request()->segment(1) == 'admin' && request()->segment(2) == 'cms' ? 'active1' : '' }}">
                                        <a href="{{ route('admin.cms.index') }}" class="sidebar-link sidebar-title">
                                            <i class="fas fa-cog"></i> Cms pages
                                        </a>
                                    </li>
                                    <li
                                        class="{{ request()->segment(1) == 'admin' && request()->segment(2) == 'createpopup' ? 'active1' : '' }}">
                                        <a href="{{ route('admin.createpopup') }}"
                                            class="sidebar-link sidebar-title">
                                            <i class="fas fa-cog"></i> popup Settings
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li> --}}
                    @endif
                    @if ($userRole === 'branchadmin')
                        <li class="sidebar-list">
                            <a class="sidebar-link sidebar-title {{ request()->route()->uri == 'dashboard' ? 'active1' : '' }}"
                                href="{{ route('badmin.dashboard') }}">
                                <i data-feather="home"></i>
                                <span class="menu-label">Dashboard</span>
                            </a>
                        </li>

                        <li class="sidebar-list">
                            <a class="sidebar-link sidebar-title {{ request()->segment(1) == 'ingredients' ? 'active1' : '' }}"
                                href="{{ route('badmin.ingredient.index') }}">
                                <i data-feather="airplay"></i>
                                <span class="menu-label">Ingredient</span>
                            </a>
                        </li>

                        @php
                            $isConstraintRoute = request()->is('staff') || request()->is('shifts');
                        @endphp

                        <li class="sidebar-list">
                            <a class="side-nav-link sidebar-link d-flex align-items-center justify-content-between"
                                data-bs-toggle="collapse" href="#sidebarProducts" role="button"
                                aria-expanded="{{ $isConstraintRoute ? 'true' : 'false' }}"
                                aria-controls="sidebarProducts">
                                <div class="d-flex align-items-center gap-2">
                                    <i data-feather="users"></i>
                                    <span class="menu-label">Staff Management</span>
                                </div>
                                <div class="according-menu">
                                    <i
                                        class="fa toggle-arrow {{ $isConstraintRoute ? 'fa-angle-down' : 'fa-angle-up' }}"></i>
                                </div>
                            </a>
                            <div class="collapse {{ $isConstraintRoute ? 'show' : '' }}" id="sidebarProducts">
                                <ul class="sidebar-submenu side-nav-second-level">

                                    {{-- Manage --}}
                                    <li>
                                        <a href="{{ route('badmin.staff.index') }}"
                                            class="sidebar-link {{ request()->is('staff') ? 'active1' : '' }}">
                                            <i class="fas fa-cog"></i>
                                            <span class="menu-label">Manage</span>
                                        </a>
                                    </li>

                                    {{-- Shift --}}
                                    <li>
                                        <a href="{{ route('badmin.shifts.index') }}"
                                            class="sidebar-link {{ request()->is('shifts') ? 'active1' : '' }}">
                                            <i class="fa fa-clock-o" aria-hidden="true"></i>
                                            <span class="menu-label">Shift</span>
                                        </a>
                                    </li>

                                </ul>
                            </div>
                        </li>

                        <li class="sidebar-list">
                            <label class="badge badge-success"></label><a
                                class="sidebar-link sidebar-title {{ request()->segment(1) == 'products' ? 'active1' : '' }}"
                                href="{{ route('badmin.products.index') }}">
                                <i data-feather="box"></i>
                                <span class="lan-6">&nbsp&nbspProducts</span>

                            </a>
                        </li>
                        @php
                            $isorderRoute = request()->is('badminorders');
                        @endphp

                        <li class="sidebar-list">
                            <a class="side-nav-link sidebar-link" data-bs-toggle="collapse"
                                href="#sidebarProductsorders" role="button"
                                aria-expanded="{{ $isorderRoute ? 'true' : 'false' }}"
                                aria-controls="sidebarProductsorders">
                                <i data-feather="shopping-cart"></i>
                                <span class="lan-6">Orders</span>

                                <div class="according-menu">
                                    <i
                                        class="fa toggle-arrow {{ $isorderRoute ? 'fa-angle-down' : 'fa-angle-up' }}"></i>
                                </div>
                            </a>
                            <div class="collapse {{ $isorderRoute ? 'show' : '' }}" id="sidebarProductsorders">
                                <ul class="sidebar-submenu side-nav-second-level">

                                    {{-- Manage --}}
                                    <li>
                                        <a href="{{ route('badmin.order.liveorders') }}"
                                            class="sidebar-link sidebar-title {{ request()->is('admin/orders/live') ? 'active1' : '' }}">
                                            <i data-feather="shopping-cart"></i>
                                            <span class="lan-6">&nbsp;&nbsp;Live Orders</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('badmin.order.index') }}"
                                            class="sidebar-link sidebar-title {{ request()->is('admin/orders') ? 'active1' : '' }}">
                                            <i data-feather="shopping-cart"></i>
                                            <span class="lan-6">&nbsp;&nbsp;Orders</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>

                        <li class="sidebar-list">
                            <a class="sidebar-link sidebar-title {{ request()->segment(1) == 'stations' ? 'active1' : '' }}"
                                href="{{ route('badmin.station.list') }}">
                                <i data-feather="airplay"></i>
                                <span class="menu-label">Stations</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('badmin.settings') }}"
                                            class="sidebar-link sidebar-title {{ request()->is('settings') ? 'active1' : '' }}">
                                            <i class="fas fa-cog"></i>
                                            <span class="menu-label">Branch Settings</span>
                                        </a>
                        </li>
                    @endif


                    {{-- <li class="sidebar-list">
                        <label class="badge badge-success"></label><a
                            class="sidebar-link sidebar-title {{ request()->route()->uri == 'admin/users' ? 'active1' : '' }}"
                            href="{{ url('/admin/users') }}"> <span class="lan-3">&nbsp&nbspusers</span>

                        </a>
                    </li> --}}

                    {{-- <li class="sidebar-list">
                        <label class="badge badge-success"></label><a
                            class="sidebar-link sidebar-title clickable {{ request()->route()->uri == 'admin/car_brands' ? 'active' : ' ' }}"
                            href="#" data-bs-original-title="" title=""><i
                                style="font-size: 18px ; color: #2c323f;" class="fa-solid  fa-users "></i><span
                                class="lan-3">&nbsp&nbspCar Brands</span>
                            <div class="according-menu"><i class="fa fa-angle-down"></i></div>
                        </a>
                        <ul class="sidebar-submenu"
                            style="display:{{ request()->route()->uri == 'admin/car_brands' ? 'block' : 'none' }}">

                            <li><a class="lan-4 " href="{{ url('admin/car_brands') }}" data-bs-original-title=""
                                    title="">Car Brands</a>
                            </li>
                            <li><a class="lan-4" href="{{ url('admin/car_brands/add') }}" data-bs-original-title=""
                                    title="">Add Car Brands</a>
                            </li>
                        </ul>
                    </li> --}}

                </ul>
            </div>
            <div class="right-arrow" id="right-arrow"><i data-feather="arrow-right"></i></div>
        </nav>
    </div>
</div>
