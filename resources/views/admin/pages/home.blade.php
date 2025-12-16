<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('front_assets/css/style_new.css') }}">
    <link rel="icon" href="{{ asset('front_assets/images/Lacrosse Power Ratings_Logo-01.png') }}"
        type="image/x-icon">
    <link rel="shortcut icon" href="{{ asset('front_assets/images/Lacrosse Power Ratings_Logo-01.png') }}"
        type="image/x-icon">

    <title></title>
</head>

<body>
    <div class="main-wrap_">
        <nav class="position-relative d-none d-md-block">
            <img src="{{ asset('front_assets/images/header.png') }}" class="img-fluid w-100" alt="">
            <div class="row align-items-center position-absolute top-0 w-100">
                <div class="col-md-4"></div>
                <div class="col-md-4 text-center">
                    <div class="wrap text-center">
                        <img src="" class="img-fluid w-75"
                            alt="">
                        <div class="wrap d-flex justify-content-center align-items-center gap-3">
                            <a href="#" class="text-decoration-none text-white">Men's Rating</a>
                            <a href="#" class="text-decoration-none text-white">Women's Rating</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-center">
                    <div class="img-wrap w-50 mx-auto">
                        <img src="{{ asset('front_assets/images/usa.png') }}" class="img-fluid" alt="">
                    </div>
                </div>
            </div>
            {{-- <div class="align-items-center d-flex justify-content-between position-absolute px-3 start-0 top-0 mt-4">
                <div></div>


            </div> --}}
        </nav>
        <nav class="position-relative bg-green text-center d-md-none">
            <img src="{{ asset('front_assets/images/Lacrosse Power Ratings_Logo-01.png') }}" class="img-fluid w-25"
                alt="">
            <div class="wrap text-center">
                <img src="" class="img-fluid w-50" alt="">
                <div class="wrap d-flex justify-content-center align-items-center gap-3">
                    <a href="#" class="text-decoration-none text-white">Men's Rating</a>
                    <a href="#" class="text-decoration-none text-white">Women's Rating</a>
                </div>
            </div>
            <img src="{{ asset('front_assets/images/usa.png') }}" class="img-fluid w-25 mt-3" alt="">
        </nav>
        <h3 class="fw-bold text-center d-none d-md-block" style="margin-top: -30px;">Schedules, Scores & Stories</h3>
        <div class="row mt-3 mx-0">
            <div class="col-md-3">
                <ul class="list-unstyled cat-list">
                    <li>
                        <p class="m-0 p-3">Computer Ratings</p>
                        <ul class="list-group list-group-flush">
                            @foreach ($crs as $cr)
                                <li class="list-group-item">
                                    <a href="{{ $cr->link ?? '#' }}"
                                        class="m-0 text-dark text-decoration-none">{{ $cr->title }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </li>

                </ul>
                <img src="{{ asset('front_assets/images/stick.jpg') }}" class="img-fluid d-none d-md-block"
                    alt="">

            </div>
            <div class="col-md-6">
                <div class="middle-links bg-gray p-3 border border-secondary">
                    <h3 class="fw-bold text-center d-md-none"></h3>
                    <div class="top d-md-flex gap-3 align-items-center justify-content-between">
                        <div class="d-md-flex align-items-center gap-1 text-center">
                            <img src="{{ asset('front_assets/images/Lacrosse Power Ratings_Logo-01.png') }}"
                                class="img-fluid col-2 col-md-3" alt="">
                            <div class="wrap text-center text-md-start">
                                <h4 class="m-0 fw-bold"></h4>
                                <div class="radio-group justify-content-center justify-content-center my-3 my-md-0">
                                    <input type="radio" id="all" name="filter" value="all" checked>
                                    <label for="all">All</label>
                                    @foreach ($categories as $category)
                                        <input type="radio" id="category_{{ $category->id }}" name="filter"
                                            value="{{ $category->id }}">
                                        <label for="category_{{ $category->id }}">{{ $category->name }}</label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="border border-dark bg-white p-2 text-dark clock">
                            <p class="m-0 small">{{ \Carbon\Carbon::now()->format('D, M j') }}</p>
                            <p class="m-0 small">
                                <span class="fw-bold fs-3">{{ \Carbon\Carbon::now()->format('h:i a') }}</span>
                                {{-- {{ \Carbon\Carbon::now()->timezoneName }} --}}
                            </p>
                        </div>
                    </div>
                    <div class="links mt-3">

                        <div id="scroll-container" style="height: 450px; overflow-y: auto;">
                            <ul class="list-group" id="news-list">
                            </ul>
                            <div class="list-group-item mb-2 loader">===</div>

                            {{-- <div id="pagination-controls" class="mt-3"> --}}
                            <input type="hidden" value="" id="nextBtn">
                            <a href="" class="jscroll-next" style="display:none;"></a>
                            {{-- </div> --}}
                        </div>

                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <ul class="list-unstyled cat-list">
                    <li>
                        <p class="m-0 p-3">Archived Data</p>
                        <ul class="list-group list-group-flush">
                            @foreach ($ads as $ad)
                                <li class="list-group-item">
                                    <a href="{{ $ad->link ?? '#' }}"
                                        class="m-0 text-dark text-decoration-none">{{ $ad->title }}</a>
                                </li>
                            @endforeach

                        </ul>

                        <p class="m-0 p-3">Followed By</p>
                        <ul class="list-group list-group-flush">
                            @foreach ($fbs as $fb)
                                <li class="list-group-item">
                                    <a href="{{ $fb->link ?? '#' }}"
                                        class="m-0 text-dark text-decoration-none">{{ $fb->title }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </li>

                </ul>
                <img src="{{ asset('front_assets/images/stick.jpg') }}" class="img-fluid d-none d-md-block"
                    alt="">

            </div>
        </div>
        <footer class="bg-green">
            <p class="text-center text-white small py-2 m-0">Copyright © 1997-2006</p>
        </footer>
    </div>
</body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
</script>

</html>
