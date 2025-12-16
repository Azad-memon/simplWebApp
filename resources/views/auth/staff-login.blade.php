<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coffee Shop | Staff Login</title>
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/bootstrap.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/style.css') }}">
    <style>
        body {
            background: linear-gradient(135deg, #f3e7e9 0%, #e3eeff 100%);
            background-attachment: fixed;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            font-family: "Poppins", sans-serif;
        }

        .login-wrapper {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.85);
            border-radius: 20px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
            padding: 45px 35px;
            max-width: 420px;
            width: 100%;
            text-align: center;
            position: relative;
        }

        .login-logo img {
            width: 85px;
            margin-bottom: 15px;
        }

        .login-wrapper h4 {
            font-weight: 700;
            font-size: 22px;
            color: #2f2f2f;
        }

        .login-wrapper p {
            font-size: 14px;
            color: #666;
            margin-bottom: 25px;
        }

        .form-label {
            font-weight: 600;
            color: #444;
        }

        .form-control {
            border-radius: 12px;
            padding: 12px 14px;
            border: 1px solid #ddd;
            transition: 0.3s;
        }

        .form-control:focus {
            border-color: #6f4e37;
            box-shadow: 0 0 6px rgba(111, 78, 55, 0.4);
        }

        .btn-coffee {
            background: linear-gradient(135deg, #6f4e37, #d4a373);
            border: none;
            color: #fff;
            font-weight: 600;
            padding: 12px;
            border-radius: 12px;
            transition: 0.3s;
        }

        .btn-coffee:hover {
            background: linear-gradient(135deg, #5a3f2e, #b5835a);
            transform: translateY(-2px);
        }

        .coffee-cup {
            font-size: 40px;
            margin-bottom: 10px;
            color: #6f4e37;
        }

        .alert {
            font-size: 14px;
        }
    </style>
</head>

<body>
    <div class="login-wrapper">
        <div class="coffee-cup">☕</div>
        {{-- <div class="login-logo">
        <img src="{{ asset('assets/images/coffee-logo.png') }}" alt="Coffee Shop Logo">
    </div> --}}
        <h4>Staff Login</h4>
        <p>Welcome back! Please enter your Employee ID</p>
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form action="{{ route('staff.login.post') }}" method="POST">
            @csrf
            @if (session()->has('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button class="btn-close" type="button" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="mb-3 text-start">
                <label for="employee_id" class="form-label">Employee ID</label>
                <input id="employee_id" class="form-control" name="employee_id" type="text"
                    placeholder="Enter Employee ID" required>
            </div>

            <div class="d-grid mt-4">
                <button class="btn btn-coffee" type="submit">
                    Sign In
                </button>
            </div>
        </form>
    </div>

    <script src="{{ asset('assets/js/bootstrap/bootstrap.bundle.min.js') }}"></script>
</body>

</html>
