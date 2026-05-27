<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TestCenter — Nền tảng thi trực tuyến</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #f0f2f5;
            color: #1a1a2e;
            min-height: 100vh;
        }

        /* ── Navbar ── */
        .navbar {
            background: #4f46e5;
            color: #fff;
            padding: 0 32px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 8px rgba(0,0,0,.2);
        }
        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.2rem;
            font-weight: 800;
            letter-spacing: -.5px;
            text-decoration: none;
            color: #fff;
        }
        .navbar-brand svg { flex-shrink: 0; }

        .navbar-links {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .nav-user {
            font-size: .9rem;
            opacity: .85;
        }
        .nav-btn {
            padding: 7px 18px;
            border-radius: 7px;
            font-size: .88rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: background .15s, opacity .15s;
        }
        .nav-btn-outline {
            background: rgba(255,255,255,.15);
            border: 1px solid rgba(255,255,255,.35);
            color: #fff;
        }
        .nav-btn-outline:hover { background: rgba(255,255,255,.25); }
        .nav-btn-solid {
            background: #fff;
            border: 1px solid #fff;
            color: #4f46e5;
        }
        .nav-btn-solid:hover { opacity: .9; }
        .nav-form { margin: 0; }

        /* ── Hero ── */
        .hero {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: #fff;
            padding: 72px 32px;
            text-align: center;
        }
        .hero-badge {
            display: inline-block;
            background: rgba(255,255,255,.15);
            border: 1px solid rgba(255,255,255,.3);
            border-radius: 20px;
            padding: 4px 16px;
            font-size: .8rem;
            font-weight: 600;
            letter-spacing: .06em;
            text-transform: uppercase;
            margin-bottom: 20px;
        }
        .hero h1 {
            font-size: 2.8rem;
            font-weight: 800;
            line-height: 1.15;
            margin-bottom: 16px;
            letter-spacing: -.5px;
        }
        .hero p {
            font-size: 1.1rem;
            opacity: .85;
            max-width: 520px;
            margin: 0 auto 36px;
            line-height: 1.6;
        }
        .hero-actions {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .hero-btn {
            padding: 13px 32px;
            border-radius: 9px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            transition: transform .1s, opacity .15s;
            border: none;
        }
        .hero-btn:hover { transform: translateY(-1px); opacity: .93; }
        .hero-btn-primary {
            background: #fff;
            color: #4f46e5;
        }
        .hero-btn-secondary {
            background: rgba(255,255,255,.15);
            border: 1px solid rgba(255,255,255,.4);
            color: #fff;
        }

        /* ── Stats strip ── */
        .stats-strip {
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            padding: 20px 32px;
            display: flex;
            justify-content: center;
            gap: 48px;
            flex-wrap: wrap;
        }
        .stat-item { text-align: center; }
        .stat-number {
            font-size: 1.6rem;
            font-weight: 800;
            color: #4f46e5;
            line-height: 1;
        }
        .stat-label {
            font-size: .78rem;
            color: #64748b;
            font-weight: 500;
            margin-top: 4px;
            text-transform: uppercase;
            letter-spacing: .05em;
        }

        /* ── Main layout ── */
        .main {
            max-width: 900px;
            margin: 0 auto;
            padding: 48px 16px 80px;
        }

        .section-header {
            display: flex;
            align-items: baseline;
            justify-content: space-between;
            margin-bottom: 24px;
        }
        .section-title {
            font-size: 1.4rem;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -.3px;
        }
        .section-subtitle {
            font-size: .85rem;
            color: #64748b;
            margin-top: 4px;
        }

        /* ── Exam cards ── */
        .exam-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 20px;
        }

        .exam-card {
            background: #fff;
            border-radius: 14px;
            padding: 24px;
            box-shadow: 0 1px 4px rgba(0,0,0,.07);
            border: 1.5px solid #e2e8f0;
            display: flex;
            flex-direction: column;
            transition: box-shadow .2s, transform .2s, border-color .2s;
            text-decoration: none;
            color: inherit;
        }
        .exam-card:hover {
            box-shadow: 0 8px 24px rgba(79,70,229,.12);
            border-color: #a5b4fc;
            transform: translateY(-2px);
        }

        .exam-card-icon {
            width: 46px;
            height: 46px;
            background: #eef2ff;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
            font-size: 1.4rem;
        }

        .exam-card-title {
            font-size: 1rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 8px;
            line-height: 1.4;
        }

        .exam-card-desc {
            font-size: .85rem;
            color: #64748b;
            line-height: 1.55;
            flex: 1;
            margin-bottom: 20px;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .exam-card-meta {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .meta-chip {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: .75rem;
            font-weight: 600;
            color: #475569;
            background: #f1f5f9;
            padding: 4px 10px;
            border-radius: 6px;
        }

        .exam-card-footer {
            margin-top: 20px;
            padding-top: 16px;
            border-top: 1px solid #f1f5f9;
        }

        .btn-take-exam {
            display: block;
            width: 100%;
            text-align: center;
            background: #4f46e5;
            color: #fff;
            padding: 10px;
            border-radius: 8px;
            font-size: .9rem;
            font-weight: 700;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: background .15s;
        }
        .btn-take-exam:hover { background: #4338ca; }

        .btn-login-to-take {
            display: block;
            width: 100%;
            text-align: center;
            background: #f1f5f9;
            color: #4f46e5;
            padding: 10px;
            border-radius: 8px;
            font-size: .9rem;
            font-weight: 700;
            text-decoration: none;
            border: 1.5px solid #c7d2fe;
            transition: background .15s;
        }
        .btn-login-to-take:hover { background: #eef2ff; }

        /* ── Empty state ── */
        .empty-state {
            text-align: center;
            padding: 64px 24px;
            color: #94a3b8;
        }
        .empty-state-icon { font-size: 3.5rem; margin-bottom: 16px; }
        .empty-state-title { font-size: 1.1rem; font-weight: 600; color: #475569; margin-bottom: 8px; }
        .empty-state-text { font-size: .9rem; }

        /* ── Features section ── */
        .features {
            margin-top: 56px;
        }
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 16px;
            margin-top: 24px;
        }
        .feature-item {
            background: #fff;
            border-radius: 12px;
            padding: 22px;
            border: 1.5px solid #e2e8f0;
            box-shadow: 0 1px 3px rgba(0,0,0,.05);
        }
        .feature-icon {
            font-size: 1.6rem;
            margin-bottom: 12px;
        }
        .feature-title {
            font-size: .95rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 6px;
        }
        .feature-text {
            font-size: .82rem;
            color: #64748b;
            line-height: 1.55;
        }

        /* ── Footer ── */
        .footer {
            text-align: center;
            padding: 24px;
            font-size: .8rem;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            background: #fff;
        }

        @media (max-width: 600px) {
            .hero h1 { font-size: 2rem; }
            .stats-strip { gap: 28px; }
            .navbar { padding: 0 16px; }
            .main { padding: 32px 12px 60px; }
        }
    </style>
</head>
<body>

{{-- ── Navbar ── --}}
<nav class="navbar">
    <a href="/" class="navbar-brand">
        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <rect x="9" y="3" width="6" height="4" rx="1" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M9 12h6M9 16h4" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        TestCenter
    </a>

    <div class="navbar-links">
        @auth
            <span class="nav-user">Xin chào, <strong>{{ auth()->user()->name }}</strong></span>
            <form method="POST" action="{{ route('logout') }}" class="nav-form">
                @csrf
                <button type="submit" class="nav-btn nav-btn-outline">Đăng xuất</button>
            </form>
        @else
            <a href="{{ route('login') }}" class="nav-btn nav-btn-outline">Đăng nhập</a>
            <a href="{{ route('register') }}" class="nav-btn nav-btn-solid">Đăng ký</a>
        @endauth
    </div>
</nav>

{{-- ── Hero ── --}}
<section class="hero">
    <div class="hero-badge">Nền tảng thi trực tuyến</div>
    <h1>Kiểm tra kiến thức<br>mọi lúc, mọi nơi</h1>
    <p>TestCenter cung cấp các bài thi đa dạng với nhiều loại câu hỏi — từ trắc nghiệm, điền từ đến nối cặp và sắp xếp. Trải nghiệm thi thử chuyên nghiệp ngay hôm nay.</p>

    <div class="hero-actions">
        @auth
            <a href="#exams" class="hero-btn hero-btn-primary">Xem bài thi</a>
        @else
            <a href="{{ route('register') }}" class="hero-btn hero-btn-primary">Bắt đầu miễn phí</a>
            <a href="{{ route('login') }}" class="hero-btn hero-btn-secondary">Đăng nhập</a>
        @endauth
    </div>
</section>

{{-- ── Stats strip ── --}}
<div class="stats-strip">
    <div class="stat-item">
        <div class="stat-number">{{ $exams->count() }}</div>
        <div class="stat-label">Bài thi đang mở</div>
    </div>
    <div class="stat-item">
        <div class="stat-number">6</div>
        <div class="stat-label">Loại câu hỏi</div>
    </div>
    <div class="stat-item">
        <div class="stat-number">DDD</div>
        <div class="stat-label">Kiến trúc</div>
    </div>
</div>

{{-- ── Exam list ── --}}
<main class="main">
    <div id="exams">
        <div class="section-header">
            <div>
                <div class="section-title">Bài thi đang mở</div>
                <div class="section-subtitle">Chọn bài thi và bắt đầu ngay</div>
            </div>
        </div>

        @if($exams->isEmpty())
            <div class="empty-state">
                <div class="empty-state-icon">📋</div>
                <div class="empty-state-title">Chưa có bài thi nào</div>
                <div class="empty-state-text">Quay lại sau hoặc liên hệ quản trị viên để biết thêm thông tin.</div>
            </div>
        @else
            <div class="exam-grid">
                @foreach($exams as $exam)
                <div class="exam-card">
                    <div class="exam-card-icon">📝</div>
                    <div class="exam-card-title">{{ $exam->title }}</div>
                    @if($exam->description)
                        <div class="exam-card-desc">{{ $exam->description }}</div>
                    @else
                        <div class="exam-card-desc" style="color:#cbd5e1;font-style:italic;">Không có mô tả</div>
                    @endif

                    <div class="exam-card-meta">
                        <span class="meta-chip">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                            {{ $exam->duration_minutes }} phút
                        </span>
                        <span class="meta-chip">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/></svg>
                            {{ $exam->questions_count }} câu
                        </span>
                    </div>

                    <div class="exam-card-footer">
                        @auth
                            <a href="{{ route('exam.show', $exam->uuid_str) }}" class="btn-take-exam">
                                Bắt đầu thi →
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn-login-to-take">
                                Đăng nhập để thi
                            </a>
                        @endauth
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- ── Features ── --}}
    <div class="features">
        <div class="section-header">
            <div>
                <div class="section-title">Tính năng nổi bật</div>
                <div class="section-subtitle">Đa dạng loại câu hỏi, chấm điểm tự động</div>
            </div>
        </div>

        <div class="features-grid">
            <div class="feature-item">
                <div class="feature-icon">✅</div>
                <div class="feature-title">Trắc nghiệm một đáp án</div>
                <div class="feature-text">Câu hỏi lựa chọn với một đáp án duy nhất đúng.</div>
            </div>
            <div class="feature-item">
                <div class="feature-icon">☑️</div>
                <div class="feature-title">Nhiều đáp án đúng</div>
                <div class="feature-text">Chọn tất cả các đáp án đúng trong câu hỏi đa lựa chọn.</div>
            </div>
            <div class="feature-item">
                <div class="feature-icon">✍️</div>
                <div class="feature-title">Điền vào chỗ trống</div>
                <div class="feature-text">Tự nhập câu trả lời, hệ thống tự động so sánh.</div>
            </div>
            <div class="feature-item">
                <div class="feature-icon">🔗</div>
                <div class="feature-title">Nối cặp</div>
                <div class="feature-text">Kéo thả để ghép đôi các khái niệm tương ứng nhau.</div>
            </div>
            <div class="feature-item">
                <div class="feature-icon">🔢</div>
                <div class="feature-title">Sắp xếp thứ tự</div>
                <div class="feature-text">Kéo thả để đặt các mục vào đúng vị trí theo thứ tự.</div>
            </div>
            <div class="feature-item">
                <div class="feature-icon">⚡</div>
                <div class="feature-title">Chấm điểm tức thì</div>
                <div class="feature-text">Kết quả hiển thị ngay sau khi nộp bài với điểm chi tiết.</div>
            </div>
        </div>
    </div>
</main>

<footer class="footer">
    TestCenter &mdash; Nền tảng thi trực tuyến &bull; Được xây dựng với Laravel 10 &amp; DDD
</footer>

</body>
</html>
