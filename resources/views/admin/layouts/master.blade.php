<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="" type="image/x-icon">
    <link rel="shortcut icon" href=""
        type="image/x-icon">
    <title>{{ env('APP_NAME') }}</title>


    <link href="https://fonts.googleapis.com/css?family=Rubik:400,400i,500,500i,700,700i&amp;display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,300i,400,400i,500,500i,700,700i,900&amp;display=swap"
        rel="stylesheet">

    @include('admin.layouts.css')
    @yield('style')
</head>

<body>

    <div class="loader-wrapper">
        <div class="loader-index"><span></span></div>
        <svg>
            <defs></defs>
            <filter id="goo">
                <fegaussianblur in="SourceGraphic" stddeviation="11" result="blur"></fegaussianblur>
                <fecolormatrix in="blur" values="1 0 0 0 0  0 1 0 0 0  0 0 1 0 0  0 0 0 19 -9" result="goo">
                </fecolormatrix>
            </filter>
        </svg>
    </div>
  <!-- Root element of PhotoSwipe. Must have class pswp.-->
                            <div class="pswp" tabindex="-1" role="dialog" aria-hidden="true">
                                <div class="pswp__bg"></div>
                                <div class="pswp__scroll-wrap">
                                    <div class="pswp__container">
                                        <div class="pswp__item"></div>
                                        <div class="pswp__item"></div>
                                        <div class="pswp__item"></div>
                                    </div>
                                    <div class="pswp__ui pswp__ui--hidden">
                                        <div class="pswp__top-bar">
                                            <div class="pswp__counter"></div>
                                            <button class="pswp__button pswp__button--close"
                                                title="Close (Esc)"></button>

                                            <button class="pswp__button pswp__button--fs"
                                                title="Toggle fullscreen"></button>
                                            <button class="pswp__button pswp__button--zoom"
                                                title="Zoom in/out"></button>
                                            <div class="pswp__preloader">
                                                <div class="pswp__preloader__icn">
                                                    <div class="pswp__preloader__cut">
                                                        <div class="pswp__preloader__donut"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="pswp__share-modal pswp__share-modal--hidden pswp__single-tap">
                                            <div class="pswp__share-tooltip"></div>
                                        </div>
                                        <button class="pswp__button pswp__button--arrow--left"
                                            title="Previous (arrow left)"></button>
                                        <button class="pswp__button pswp__button--arrow--right"
                                            title="Next (arrow right)"></button>
                                        <div class="pswp__caption">
                                            <div class="pswp__caption__center"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
    <!-- tap on top starts-->
    <div class="tap-top"><i data-feather="chevrons-up"></i></div>
    <!-- tap on tap ends-->
    <!-- page-wrapper Start-->
    <div class="page-wrapper compact-wrapper" id="pageWrapper">
        <!-- Page Header Start-->
        @include('admin.layouts.header')
        <!-- Page Header Ends  -->
        <!-- Page Body Start-->
        <div class="page-body-wrapper">
            <!-- Page Sidebar Start-->
            @include('admin.layouts.sidebar')
            <!-- Page Sidebar Ends-->
            <div class="page-body">
                <div class="container-fluid">
                    <div class="page-title">
                        <div class="row">
                            <div class="col-6">
                                @yield('breadcrumb-title')
                            </div>
                            <div class="col-6">
                                <ol class="breadcrumb">
                                    {{-- <li class="breadcrumb-item"><a href=""> <i data-feather="home"></i></a></li> --}}
                                    @yield('breadcrumb-items')
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Container-fluid starts-->
                @yield('content')
                <!-- Modal Component -->
                <div id="addtranslationModal"></div>
                <!-- Container-fluid Ends-->
            </div>
            <!-- footer start-->
            @include('admin.layouts.footer')

        </div>
    </div>

    <!-- latest jquery-->
    @include('admin.layouts.script')
     @include('admin.layouts.firebase')
    <!-- Plugin used-->

    <script type="text/javascript">
        if ($(".page-wrapper").hasClass("horizontal-wrapper")) {
            $(".according-menu.other").css("display", "none");
            $(".sidebar-submenu").css("display", "block");
        }
        $(document).ready(function () {
    $('.summernote').summernote({
        placeholder: 'Write description...',
        height: 200,
        toolbar: [
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['font', ['fontsize', 'color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['insert', ['link', 'picture']],
            ['view', ['fullscreen', 'codeview']]
        ]
    });

});

    </script>

    <script>
class CollapseToggler {
    constructor(selector) {
        this.selector = selector;
        this.init(); // Initialize when class is instantiated
    }

    init() {
        $(this.selector).on('shown.bs.collapse', (e) => {
            $(e.target).prev('a').find('.toggle-arrow')
                .removeClass('fa-angle-up')
                .addClass('fa-angle-down');
        });

        $(this.selector).on('hidden.bs.collapse', (e) => {
            $(e.target).prev('a').find('.toggle-arrow')
                .removeClass('fa-angle-down')
                .addClass('fa-angle-up');
        });
    }
}
    $(document).ready(function () {
            // Initialize CollapseToggler for sidebarProducts
    new CollapseToggler('#sidebarProducts');

    // Initialize CollapseToggler for another collapse element
   // new CollapseToggler('#anotherCollapse');
        // $('#sidebarProducts').on('shown.bs.collapse', function () {
        //     $(this).prev('a').find('.toggle-arrow')
        //         .removeClass('fa-angle-up')
        //         .addClass('fa-angle-down');
        // });

        // $('#sidebarProducts').on('hidden.bs.collapse', function () {
        //     $(this).prev('a').find('.toggle-arrow')
        //         .removeClass('fa-angle-down')
        //         .addClass('fa-angle-up');
        // });

    // Function to initialize Select2 with modal-safe options
    function initSelect2InsideModal(modal) {
        $(modal).find('select').select2({
            placeholder: "-- Choose Option --",
            allowClear: true,
            minimumResultsForSearch: 0,
            dropdownParent: $(modal) // This ensures dropdown renders correctly inside modal
        });
    }

    // On modal shown event for any modal
    $(document).on('shown.bs.modal', '.modal', function () {
        initSelect2InsideModal(this);
    });

    // Optional: Also init on page load if modals already visible
    $('.modal:visible').each(function () {
        initSelect2InsideModal(this);
    });
});
</script>

    @yield('script')
    @stack('scripts')
</body>

</html>
