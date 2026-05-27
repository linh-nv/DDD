<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập — TestCenter</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #f0f2f5;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0,0,0,.10);
            width: 100%;
            max-width: 420px;
            overflow: hidden;
        }

        .card-header {
            background: #4f46e5;
            color: #fff;
            padding: 28px 32px 24px;
        }

        .card-header h1 {
            font-size: 1.4rem;
            font-weight: 700;
        }

        .card-header p {
            font-size: .85rem;
            opacity: .8;
            margin-top: 4px;
        }

        .card-body {
            padding: 28px 32px 32px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        label {
            display: block;
            font-size: .85rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 6px;
        }

        input[type="email"],
        input[type="password"],
        input[type="text"] {
            width: 100%;
            padding: 10px 14px;
            border: 1.5px solid #d1d5db;
            border-radius: 8px;
            font-size: .95rem;
            color: #1a1a2e;
            transition: border-color .2s;
            outline: none;
        }

        input:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79,70,229,.12);
        }

        .input-error {
            border-color: #ef4444 !important;
        }

        .error-msg {
            color: #ef4444;
            font-size: .8rem;
            margin-top: 5px;
        }

        .remember-row {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 22px;
        }

        .remember-row input { width: auto; }

        .remember-row label {
            margin: 0;
            font-weight: 400;
            cursor: pointer;
        }

        .btn-primary {
            width: 100%;
            padding: 11px;
            background: #4f46e5;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background .2s, transform .1s;
        }

        .btn-primary:hover { background: #4338ca; }
        .btn-primary:active { transform: scale(.98); }

        .footer-link {
            text-align: center;
            margin-top: 18px;
            font-size: .875rem;
            color: #6b7280;
        }

        .footer-link a {
            color: #4f46e5;
            font-weight: 600;
            text-decoration: none;
        }

        .footer-link a:hover { text-decoration: underline; }

        .alert-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #b91c1c;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: .875rem;
            margin-bottom: 18px;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="card-header">
            <h1>Đăng nhập</h1>
            <p>TestCenter — Nền tảng thi trực tuyến</p>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert-error">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="form-group">
                    <label for="email">Email</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        autocomplete="email"
                        autofocus
                        class="{{ $errors->has('email') ? 'input-error' : '' }}"
                        placeholder="you@example.com"
                        required
                    >
                    @error('email')
                        <div class="error-msg">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">Mật khẩu</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        autocomplete="current-password"
                        placeholder="••••••••"
                        required
                    >
                    @error('password')
                        <div class="error-msg">{{ $message }}</div>
                    @enderror
                </div>

                <div class="remember-row">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">Ghi nhớ đăng nhập</label>
                </div>

                <button type="submit" class="btn-primary">Đăng nhập</button>
            </form>

            <p class="footer-link">
                Chưa có tài khoản? <a href="{{ route('register') }}">Đăng ký ngay</a>
            </p>
        </div>
    </div>
</body>
</html>
