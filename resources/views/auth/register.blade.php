<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký — TestCenter</title>
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
            margin-top: 4px;
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

        .hint {
            font-size: .78rem;
            color: #9ca3af;
            margin-top: 4px;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="card-header">
            <h1>Đăng ký tài khoản</h1>
            <p>TestCenter — Nền tảng thi trực tuyến</p>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="form-group">
                    <label for="name">Họ và tên</label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name') }}"
                        autocomplete="name"
                        autofocus
                        class="{{ $errors->has('name') ? 'input-error' : '' }}"
                        placeholder="Nguyễn Văn A"
                        required
                    >
                    @error('name')
                        <div class="error-msg">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        autocomplete="email"
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
                        autocomplete="new-password"
                        placeholder="••••••••"
                        required
                    >
                    <p class="hint">Tối thiểu 8 ký tự</p>
                    @error('password')
                        <div class="error-msg">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Xác nhận mật khẩu</label>
                    <input
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        autocomplete="new-password"
                        placeholder="••••••••"
                        required
                    >
                </div>

                <button type="submit" class="btn-primary">Tạo tài khoản</button>
            </form>

            <p class="footer-link">
                Đã có tài khoản? <a href="{{ route('login') }}">Đăng nhập</a>
            </p>
        </div>
    </div>
</body>
</html>
