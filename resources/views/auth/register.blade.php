<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="author" content="pixelstrap">

    <link rel="icon" href="{{ asset('front_assets/images/Lacrosse Power Ratings_Logo-01.png') }}"
        type="image/x-icon">
    <link rel="shortcut icon" href="{{ asset('front_assets/images/Lacrosse Power Ratings_Logo-01.png') }}"
        type="image/x-icon">
    <title></title>
    <!-- Google font-->
    <link href="https://fonts.googleapis.com/css?family=Rubik:400,400i,500,500i,700,700i&amp;display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,300i,400,400i,500,500i,700,700i,900&amp;display=swap"
        rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/font-awesome.css') }}">
    <!-- ico-font-->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/icofont.css') }}">
    <!-- Themify icon-->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/themify.css') }}">
    <!-- Flag icon-->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/flag-icon.css') }}">
    <!-- Feather icon-->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/feather-icon.css') }}">
    <!-- Plugins css start-->
    <!-- Plugins css Ends-->
    <!-- Bootstrap css-->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/bootstrap.css') }}">
    <!-- App css-->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/style.css') }}">
    <link id="color" rel="stylesheet" href="{{ asset('assets/css/color-1.css') }}" media="screen">
    <!-- Responsive css-->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/responsive.css') }}">
</head>

<body>
    <!-- login page start-->
    <div class="container-fluid p-0">
        <div class="row m-0">
            <div class="col-12 p-0">
                <div class="login-card">
                    <div>
                        {{-- <div><a class="logo" href="/"><img style="width: 100px" class="img-fluid"
                                    src="{{ asset('front_assets/images/Lacrosse Power Ratings_Logo-01.png') }}"
                                    alt="looginpage"></a></div> --}}
                        <div class="login-main">
                            <h4 class="mb-3 text-center">Crear una cuenta</h4>


                            <form action="{{ route('register_post') }}" method="POST" class="theme-form">
                                @csrf

                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <ul class="mb-0">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                {{-- Tipo oculto --}}
                                <input type="hidden" name="type" value="{{ $type }}">

                                {{-- Campos comunes por tipo --}}

                                    <div class="form-group mb-3">
                                        <label>Nombre completo</label>
                                        <input type="text" class="form-control" name="name" required value="{{ old('name') }}">
                                    </div>

                                    <div class="form-group mb-3">
                                        <label>Correo electrónico</label>
                                        <input type="email" class="form-control" name="email" required value="{{ old('email') }}">
                                    </div>

                                    <div class="form-group mb-3">
                                        <label>Teléfono celular</label>
                                        <input type="text" class="form-control" name="phone" required value="{{ old('phone') }}">
                                    </div>
                                @if ($type == 'influencer')
                                     <div class="form-group mb-3">
                                        <label>Usuario de Instagram</label>
                                        <input type="text" class="form-control" name="ig_handle" required value="{{ old('ig_handle') }}">
                                    </div>
                                @elseif ($type == 'nutrient' ||$type == 'coffee')
                                    <div class="form-group mb-3">
                                        <label>Nombre de Fantasía</label>
                                        <input type="text" class="form-control" name="business_name" required value="{{ old('business_name') }}">
                                    </div>

                                    <div class="form-group mb-3">
                                        <label>Dirección del negocio</label>
                                        <input type="text" class="form-control" name="business_address" required value="{{ old('business_address') }}">
                                    </div>

                                @endif

                                {{-- Password --}}
                                <div class="form-group mb-3">
                                    <label>Contraseña</label>
                                    <input type="password" class="form-control" name="password" required>
                                </div>

                                <div class="form-group mb-4">
                                    <label>Confirmar Contraseña</label>
                                    <input type="password" class="form-control" name="password_confirmation" required>
                                </div>

                                <button type="submit" class="btn btn-primary w-100">Registrarse</button>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- latest jquery-->
        <script src="{{ asset('assets/js/jquery-3.5.1.min.js') }}"></script>
        <!-- Bootstrap js-->
        <script src="{{ asset('assets/js/bootstrap/bootstrap.bundle.min.js') }}"></script>
        <!-- feather icon js-->
        <script src="{{ asset('assets/js/icons/feather-icon/feather.min.js') }}"></script>
        <script src="{{ asset('assets/js/icons/feather-icon/feather-icon.js') }}"></script>
        <!-- scrollbar js-->
        <!-- Sidebar jquery-->
        <script src="{{ asset('assets/js/config.js') }}"></script>
        <!-- Plugins JS start-->
        <!-- Plugins JS Ends-->
        <!-- Theme js-->
        <script src="{{ asset('assets/js/script.js') }}"></script>
        <!-- login js-->
        <!-- Plugin used-->
        <script>
            $(document).ready(function() {

                $('.show-hide').on('click', function() {
                    var passInput = $("input[name=password]");
                    if (passInput.attr('type') === 'password') {
                        passInput.attr('type', 'text');
                    } else {
                        passInput.attr('type', 'password');
                    }
                })
            })
        </script>
    </div>
</body>

</html>
