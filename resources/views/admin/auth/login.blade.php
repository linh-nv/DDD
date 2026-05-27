<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login — TestCenter</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #1e1b4b; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .card { background: #fff; border-radius: 14px; width: 100%; max-width: 400px; overflow: hidden; box-shadow: 0 25px 60px rgba(0,0,0,.35); }
        .card-header { padding: 28px 32px 22px; }
        .card-header .logo { font-size: 1.5rem; font-weight: 800; color: #1e1b4b; }
        .card-header .logo span { color: #4f46e5; }
        .card-header p { color: #64748b; font-size: .875rem; margin-top: 6px; }
        .card-body { padding: 8px 32px 32px; }
        .form-group { margin-bottom: 16px; }
        label { display: block; font-size: .82rem; font-weight: 700; color: #374151; margin-bottom: 5px; text-transform: uppercase; letter-spacing: .04em; }
        input { width: 100%; padding: 10px 13px; border: 1.5px solid #d1d5db; border-radius: 8px; font-size: .9rem; outline: none; transition: border-color .2s; }
        input:focus { border-color: #4f46e5; box-shadow: 0 0 0 3px rgba(79,70,229,.1); }
        .err { border-color: #ef4444 !important; }
        .error-msg { color: #ef4444; font-size: .8rem; margin-top: 5px; }
        .alert { background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; border-radius: 8px; padding: 10px 14px; font-size: .875rem; margin-bottom: 16px; }
        .btn { width: 100%; padding: 11px; background: #4f46e5; color: #fff; border: none; border-radius: 8px; font-size: .95rem; font-weight: 700; cursor: pointer; transition: background .2s; }
        .btn:hover { background: #4338ca; }
        .back-link { text-align: center; margin-top: 18px; font-size: .82rem; color: #94a3b8; }
        .back-link a { color: #4f46e5; text-decoration: none; font-weight: 600; }
        .admin-badge { display: inline-flex; align-items: center; gap: 5px; background: #eef2ff; color: #4338ca; border-radius: 6px; padding: 4px 10px; font-size: .75rem; font-weight: 700; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="card">
        <div class="card-header">
            <div class="logo">Test<span>Center</span></div>
            <p>Đăng nhập trang quản trị</p>
        </div>
        <div class="card-body">
            <div class="admin-badge">🔐 Khu vực Admin</div>

            @if($errors->any())
                <div class="alert">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('admin.login') }}">
                @csrf
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" autofocus placeholder="admin@example.com"
                           class="{{ $errors->has('email') ? 'err' : '' }}" required>
                </div>
                <div class="form-group">
                    <label>Mật khẩu</label>
                    <input type="password" name="password" placeholder="••••••••" required>
                </div>
                <button type="submit" class="btn">Đăng nhập Admin</button>
            </form>

            <p class="back-link"><a href="{{ route('login') }}">← Quay lại trang người dùng</a></p>
        </div>
    </div>
</body>
</html>
