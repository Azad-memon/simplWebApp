<style>
.pos-btn {
    display: inline-flex;
    align-items: center;
    background: linear-gradient(135deg, #6c63ff, #5a55e1);
    color: #fff !important;
    font-weight: 500;
    padding: 7px 16px;
    border-radius: 8px;
    text-decoration: none;
    box-shadow: 0 3px 6px rgba(108, 99, 255, 0.25);
    transition: all 0.3s ease;
}
.pos-btn:hover {
    background: linear-gradient(135deg, #5a55e1, #4b47c6);
    box-shadow: 0 4px 10px rgba(90, 85, 225, 0.35);
    transform: translateY(-2px);
}
</style>

<div class="page-header">
    <div class="header-wrapper row m-0">
        <form class="form-inline search-full col" action="#" method="get">
            <div class="mb-3 w-100">
                <div class="Typeahead Typeahead--twitterUsers">
                    <div class="u-posRelative">
                        <input class="demo-input Typeahead-input form-control-plaintext w-100" type="text"
                            placeholder="Search Cuba .." name="q" title="" autofocus>
                        <div class="spinner-border Typeahead-spinner" role="status"><span
                                class="sr-only">Loading...</span></div>
                        <i class="close-search" data-feather="x"></i>
                    </div>
                    <div class="Typeahead-menu"></div>
                </div>
            </div>
        </form>
        <div class="header-logo-wrapper col-auto p-0">
            <div class="logo-wrapper"><a href=""><img class="img-fluid"
                        src="" alt=""></a></div>
            <div class="toggle-sidebar"><i class="status_toggle middle sidebar-toggle" data-feather="align-center"></i>
            </div>
        </div>
        <div class="left-header col horizontal-wrapper ps-0">
            <ul class="horizontal-menu">
            </ul>
        </div>

        <div class="nav-right col-8 pull-right right-header p-0">
            <ul class="nav-menus">
    {{-- @if(auth()->check() && Auth::user()->role->name == 'branchadmin')
    <li class="d-flex align-items-center ms-3">
        <a href="{{ route('pos.index') }}" class="pos-btn">
            <i data-feather="shopping-cart" class="me-1"></i> Go to POS
        </a>
    </li>
@endif --}}


                <li class="maximize"><a class="text-dark" href="#!" onclick="javascript:toggleFullScreen()"><i
                            data-feather="maximize"></i></a></li>
                <li class="profile-nav onhover-dropdown p-0 me-0">
                    <div class="media profile-media">
                        <div class="media-body">
                            <span>{{ auth()->user()->first_name }}</span>
                            <p class="mb-0 font-roboto">{{ auth()->user()->email }} <i
                                    class="middle fa fa-angle-down"></i></p>
                        </div>
                    </div>
                    <ul class="profile-dropdown onhover-show-div">
                        <li><a href="{{ url('/admin/profile/edit/' . \Crypt::encrypt( auth()->user()->id)) }}">
                            <i data-feather="settings"></i><span>Settings</span></a></li>

                        <li><a href="{{ route('logout') }}"><i data-feather="log-in"> </i><span>Log Out</span></a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
        {{-- <script class="result-template" type="text/x-handlebars-template"><div class="ProfileCard u-cf">
              <div class="ProfileCard-avatar"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-airplay m-0"><path d="M5 17H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-1"></path><polygon points="12 15 17 21 7 21 12 15"></polygon></svg></div>
              <div class="ProfileCard-details">
              <div class="ProfileCard-realName">@{{ name }}</div>
              </div>
              </div></script>
        <script class="empty-template" type="text/x-handlebars-template">
            <div class="EmptyMessage">Your search turned up 0 results. This most likely means the backend is down, yikes!</div>
        </script> --}}
    </div>
</div>
