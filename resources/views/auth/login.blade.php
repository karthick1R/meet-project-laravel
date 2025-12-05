<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: "Inter", sans-serif;
            background: linear-gradient(135deg, #eef4ff, #dbe8ff);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Center container */
        .login-wrapper {
            width: 420px;
        }

        /* Card */
        .login-card {
            background: #ffffff;
            padding: 35px;
            border-radius: 18px;
            box-shadow: 0px 10px 35px rgba(0, 0, 0, 0.08);
            animation: fadeIn 0.8s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* Header */
        .login-header {
            text-align: center;
            margin-bottom: 25px;
        }

        .login-header i {
            font-size: 55px;
            color: #3b7df0;
            margin-bottom: 10px;
        }

        .login-header h2 {
            font-weight: 600;
            font-size: 28px;
            color: #2b2b2b;
        }

        /* Labels */
        label {
            font-weight: 500;
            color: #333;
        }

        /* Inputs */
        .form-control {
            border-radius: 12px;
            padding: 12px;
            border: 1px solid #d7d7d7;
            transition: 0.3s ease-in-out;
            font-size: 15px;
        }

        .form-control:focus {
            border-color: #3b7df0;
            box-shadow: 0 0 0 4px rgba(59, 125, 240, 0.15);
        }

        /* Login Button */
        .btn-login {
            width: 100%;
            padding: 12px;
            border-radius: 12px;
            font-size: 17px;
            font-weight: 600;
            border: none;
            color: white;
            background: linear-gradient(135deg, #3b7df0, #5a8bff);
            transition: 0.3s ease;
        }

        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0px 8px 20px rgba(59, 125, 240, 0.25);
        }

        .register-link a {
            color: #3b7df0;
            font-weight: 600;
            text-decoration: none;
        }
        .register-link a:hover {
            text-decoration: underline;
        }

    </style>
</head>

<body>

    <div class="login-wrapper">
        <div class="login-card">

            <div class="login-header">
                <i class="fa-solid fa-door-open"></i>
                <h2>Meeting Room Login</h2>
            </div>

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="mb-3">
                    <label>Email Address</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                           name="email" value="{{ old('email') }}" required />
                    @error('email')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-3">
                    <label>Password</label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                           name="password" required />
                    @error('password')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-check mb-3">
                    <input type="checkbox" id="remember" name="remember" class="form-check-input">
                    <label for="remember" class="form-check-label">Remember Me</label>
                </div>

                <button class="btn-login">Login</button>
            </form>

            <div class="text-center register-link mt-3">
                <p>New here? <a href="{{ route('register') }}">Create an account</a></p>
            </div>

        </div>
    </div>

    <!-- Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
