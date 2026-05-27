<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $exam->title }}</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #f0f2f5;
            color: #1a1a2e;
            min-height: 100vh;
        }

        /* ── Header ── */
        .exam-header {
            position: sticky;
            top: 0;
            z-index: 100;
            background: #4f46e5;
            color: #fff;
            padding: 14px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 8px rgba(0,0,0,.2);
        }
        .exam-header h1 { font-size: 1.15rem; font-weight: 700; }
        .exam-header p  { font-size: .8rem; opacity: .8; margin-top: 2px; }

        .timer {
            background: rgba(255,255,255,.15);
            border: 1px solid rgba(255,255,255,.3);
            border-radius: 8px;
            padding: 6px 16px;
            font-size: 1.2rem;
            font-weight: 700;
            font-variant-numeric: tabular-nums;
            min-width: 80px;
            text-align: center;
        }
        .timer.warning { background: #ef4444; border-color: #dc2626; }

        /* ── Layout ── */
        .container {
            max-width: 820px;
            margin: 0 auto;
            padding: 24px 16px 120px;
        }

        /* ── Question card ── */
        .question-card {
            background: #fff;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 16px;
            box-shadow: 0 1px 4px rgba(0,0,0,.07);
            border-left: 4px solid #e2e8f0;
            transition: border-color .2s;
        }
        .question-card.answered { border-left-color: #4f46e5; }

        .question-meta {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 14px;
        }
        .q-number {
            background: #4f46e5;
            color: #fff;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: .85rem;
            flex-shrink: 0;
        }
        .q-type {
            font-size: .72rem;
            font-weight: 600;
            letter-spacing: .05em;
            text-transform: uppercase;
            background: #eef2ff;
            color: #4f46e5;
            padding: 2px 8px;
            border-radius: 4px;
        }
        .q-score {
            margin-left: auto;
            font-size: .8rem;
            color: #64748b;
            font-weight: 600;
        }

        .question-text {
            font-size: 1rem;
            line-height: 1.6;
            margin-bottom: 18px;
            color: #0f172a;
        }

        /* ── Option types ── */
        .options { display: flex; flex-direction: column; gap: 10px; }

        .option-label {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 16px;
            border: 1.5px solid #e2e8f0;
            border-radius: 8px;
            cursor: pointer;
            transition: border-color .15s, background .15s;
            font-size: .95rem;
        }
        .option-label:hover { border-color: #a5b4fc; background: #f5f3ff; }
        .option-label input[type=radio]:checked + span,
        .option-label:has(input:checked) { color: #4338ca; font-weight: 600; }
        .option-label:has(input:checked) { border-color: #6366f1; background: #eef2ff; }

        /* fill blank */
        .fill-input {
            width: 100%;
            padding: 11px 14px;
            border: 1.5px solid #e2e8f0;
            border-radius: 8px;
            font-size: .95rem;
            outline: none;
            transition: border-color .15s;
        }
        .fill-input:focus { border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,.12); }

        /* matching */
        .matching-table { width: 100%; border-collapse: collapse; }
        .matching-table td { padding: 8px 10px; vertical-align: middle; }
        .matching-table td:first-child {
            width: 40%;
            font-weight: 600;
            color: #334155;
            background: #f8fafc;
            border-radius: 6px;
        }
        .matching-table td:nth-child(2) {
            width: 10%;
            text-align: center;
            color: #94a3b8;
            font-size: 1.2rem;
        }
        .match-select, .cat-select {
            width: 100%;
            padding: 9px 12px;
            border: 1.5px solid #e2e8f0;
            border-radius: 8px;
            font-size: .9rem;
            background: #fff;
            cursor: pointer;
            outline: none;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%236366f1' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 10px center;
            padding-right: 34px;
        }
        .match-select:focus, .cat-select:focus { border-color: #6366f1; }

        /* ordering */
        .ordering-list { display: flex; flex-direction: column; gap: 8px; }
        .order-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 14px;
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: 8px;
            font-size: .95rem;
            user-select: none;
        }
        .order-item.dragging { opacity: .4; }
        .drag-handle {
            cursor: grab;
            color: #94a3b8;
            font-size: 1.1rem;
            flex-shrink: 0;
        }
        .drag-handle:active { cursor: grabbing; }
        .order-item .item-label { flex: 1; }
        .order-btns { display: flex; flex-direction: column; gap: 2px; }
        .order-btn {
            background: #e2e8f0;
            border: none;
            border-radius: 4px;
            width: 24px;
            height: 22px;
            cursor: pointer;
            font-size: .75rem;
            line-height: 1;
            color: #475569;
            transition: background .1s;
        }
        .order-btn:hover { background: #c7d2fe; color: #4338ca; }

        /* category */
        .category-table { width: 100%; border-collapse: collapse; }
        .category-table td { padding: 8px 10px; vertical-align: middle; }
        .category-table td:first-child {
            width: 50%;
            font-weight: 600;
            color: #334155;
            background: #f8fafc;
            border-radius: 6px;
        }
        .category-table td:nth-child(2) {
            width: 10%;
            text-align: center;
            color: #94a3b8;
        }

        /* ── Submit bar ── */
        .submit-bar {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: #fff;
            border-top: 1px solid #e2e8f0;
            padding: 16px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            z-index: 100;
            box-shadow: 0 -2px 12px rgba(0,0,0,.08);
        }
        .progress-info { font-size: .85rem; color: #64748b; }
        .progress-info strong { color: #4f46e5; }

        .btn-submit {
            background: #4f46e5;
            color: #fff;
            border: none;
            padding: 12px 32px;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: background .15s, transform .1s;
        }
        .btn-submit:hover { background: #4338ca; }
        .btn-submit:active { transform: scale(.98); }
        .btn-submit:disabled { background: #a5b4fc; cursor: not-allowed; }

        /* ── Result overlay ── */
        .overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15,23,42,.6);
            z-index: 200;
            align-items: center;
            justify-content: center;
        }
        .overlay.show { display: flex; }

        .result-card {
            background: #fff;
            border-radius: 20px;
            padding: 48px 56px;
            text-align: center;
            max-width: 440px;
            width: 90%;
            animation: pop .3s cubic-bezier(.175,.885,.32,1.275);
        }
        @keyframes pop {
            from { transform: scale(.85); opacity: 0; }
            to   { transform: scale(1);  opacity: 1; }
        }

        .result-icon { font-size: 4rem; margin-bottom: 16px; }
        .result-score {
            font-size: 3.5rem;
            font-weight: 800;
            color: #4f46e5;
            line-height: 1;
        }
        .result-max { font-size: 1rem; color: #64748b; margin-top: 4px; margin-bottom: 24px; }
        .result-msg { font-size: 1.15rem; font-weight: 600; color: #0f172a; margin-bottom: 8px; }
        .result-sub { font-size: .9rem; color: #64748b; margin-bottom: 32px; }

        .btn-retry {
            background: #f1f5f9;
            color: #334155;
            border: none;
            padding: 11px 28px;
            border-radius: 8px;
            font-size: .95rem;
            font-weight: 600;
            cursor: pointer;
            margin-right: 10px;
        }
        .btn-retry:hover { background: #e2e8f0; }

        /* Responsive */
        @media (max-width: 600px) {
            .result-card { padding: 32px 24px; }
            .result-score { font-size: 2.8rem; }
        }
    </style>
</head>
<body>

{{-- ── Header ── --}}
<header class="exam-header">
    <div>
        <h1>{{ $exam->title }}</h1>
        <p>{{ $exam->description }}</p>
    </div>
    <div style="display:flex;align-items:center;gap:16px;">
        <div class="timer" id="timer">{{ str_pad($exam->duration_minutes, 2, '0', STR_PAD_LEFT) }}:00</div>
        <div style="font-size:.8rem;opacity:.85;white-space:nowrap;">{{ auth()->user()->name }}</div>
        <form method="POST" action="{{ route('logout') }}" style="margin:0;">
            @csrf
            <button type="submit" style="background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.35);color:#fff;padding:5px 12px;border-radius:6px;font-size:.8rem;cursor:pointer;">
                Đăng xuất
            </button>
        </form>
    </div>
</header>

<div class="container">
    @foreach($questions as $index => $question)
    @php
        $payload = $question->payload ?? [];
        $typeLabel = [
            'true_false'      => 'Đúng / Sai',
            'single_choice'   => 'Một đáp án',
            'multiple_choice' => 'Nhiều đáp án',
            'fill_blank'      => 'Điền vào chỗ trống',
            'matching'        => 'Nối cặp',
            'ordering'        => 'Sắp xếp thứ tự',
            'category'        => 'Phân loại',
        ][$question->type] ?? $question->type;
    @endphp

    <div class="question-card"
         data-question-id="{{ $question->uuid_str }}"
         data-type="{{ $question->type }}"
         id="qcard-{{ $question->uuid_str }}">

        <div class="question-meta">
            <span class="q-number">{{ $index + 1 }}</span>
            <span class="q-type">{{ $typeLabel }}</span>
            <span class="q-score">{{ $question->score }} điểm</span>
        </div>

        <p class="question-text">{{ $question->content }}</p>

        {{-- ── TRUE / FALSE ── --}}
        @if($question->type === 'true_false')
        <div class="options">
            <label class="option-label">
                <input type="radio" name="q_{{ $question->uuid_str }}" value="true" onchange="markAnswered('{{ $question->uuid_str }}')">
                <span>✅ Đúng</span>
            </label>
            <label class="option-label">
                <input type="radio" name="q_{{ $question->uuid_str }}" value="false" onchange="markAnswered('{{ $question->uuid_str }}')">
                <span>❌ Sai</span>
            </label>
        </div>

        {{-- ── SINGLE CHOICE ── --}}
        @elseif($question->type === 'single_choice')
        <div class="options">
            @foreach($payload['options'] as $key => $label)
            <label class="option-label">
                <input type="radio" name="q_{{ $question->uuid_str }}" value="{{ $key }}" onchange="markAnswered('{{ $question->uuid_str }}')">
                <span><strong>{{ $key }}.</strong> {{ $label }}</span>
            </label>
            @endforeach
        </div>

        {{-- ── MULTIPLE CHOICE ── --}}
        @elseif($question->type === 'multiple_choice')
        <p style="font-size:.8rem;color:#64748b;margin-bottom:10px;">Chọn tất cả đáp án đúng</p>
        <div class="options">
            @foreach($payload['options'] as $key => $label)
            <label class="option-label">
                <input type="checkbox" name="q_{{ $question->uuid_str }}[]" value="{{ $key }}" onchange="markAnswered('{{ $question->uuid_str }}')">
                <span><strong>{{ $key }}.</strong> {{ $label }}</span>
            </label>
            @endforeach
        </div>

        {{-- ── FILL BLANK ── --}}
        @elseif($question->type === 'fill_blank')
        <input type="text"
               class="fill-input"
               name="q_{{ $question->uuid_str }}"
               placeholder="Nhập câu trả lời..."
               oninput="markAnswered('{{ $question->uuid_str }}')">

        {{-- ── MATCHING ── --}}
        @elseif($question->type === 'matching')
        @php $rightValues = array_values($payload['pairs']); @endphp
        <table class="matching-table">
            @foreach($payload['pairs'] as $left => $right)
            <tr>
                <td>{{ $left }}</td>
                <td>→</td>
                <td>
                    <select class="match-select"
                            data-left="{{ $left }}"
                            data-question="{{ $question->uuid_str }}"
                            onchange="markAnswered('{{ $question->uuid_str }}')">
                        <option value="">-- Chọn --</option>
                        @foreach($rightValues as $rv)
                        <option value="{{ $rv }}">{{ $rv }}</option>
                        @endforeach
                    </select>
                </td>
            </tr>
            @endforeach
        </table>

        {{-- ── ORDERING ── --}}
        @elseif($question->type === 'ordering')
        <p style="font-size:.8rem;color:#64748b;margin-bottom:10px;">Kéo thả hoặc dùng nút ↑↓ để sắp xếp đúng thứ tự</p>
        <div class="ordering-list" id="order-{{ $question->uuid_str }}" data-question="{{ $question->uuid_str }}">
            @foreach($question->display_items as $item)
            <div class="order-item" draggable="true" data-value="{{ $item }}">
                <span class="drag-handle">⠿</span>
                <span class="item-label">{{ $item }}</span>
                <div class="order-btns">
                    <button class="order-btn" onclick="moveItem(this, -1, '{{ $question->uuid_str }}')" title="Lên">▲</button>
                    <button class="order-btn" onclick="moveItem(this,  1, '{{ $question->uuid_str }}')" title="Xuống">▼</button>
                </div>
            </div>
            @endforeach
        </div>

        {{-- ── CATEGORY ── --}}
        @elseif($question->type === 'category')
        <table class="category-table">
            @foreach($question->display_items as $item)
            <tr>
                <td>{{ $item }}</td>
                <td>→</td>
                <td>
                    <select class="cat-select"
                            data-item="{{ $item }}"
                            data-question="{{ $question->uuid_str }}"
                            onchange="markAnswered('{{ $question->uuid_str }}')">
                        <option value="">-- Chọn loại --</option>
                        @foreach($payload['categories'] as $cat)
                        <option value="{{ $cat }}">{{ $cat }}</option>
                        @endforeach
                    </select>
                </td>
            </tr>
            @endforeach
        </table>
        @endif

    </div>{{-- .question-card --}}
    @endforeach
</div>

{{-- ── Submit bar ── --}}
<div class="submit-bar">
    <div class="progress-info">
        Đã trả lời: <strong id="answered-count">0</strong> / {{ $questions->count() }} câu
    </div>
    <button class="btn-submit" id="submit-btn" onclick="submitExam()">
        Nộp bài
    </button>
</div>

{{-- ── Result overlay ── --}}
<div class="overlay" id="result-overlay">
    <div class="result-card">
        <div class="result-icon" id="result-icon">🎉</div>
        <div class="result-score" id="result-score">0</div>
        <div class="result-max" id="result-max">/ {{ $questions->sum('score') }} điểm</div>
        <div class="result-msg" id="result-msg">Hoàn thành!</div>
        <div class="result-sub" id="result-sub"></div>
        <div>
            <button class="btn-retry" onclick="location.reload()">Làm lại</button>
            <button class="btn-submit" onclick="location.href='/'">Trang chủ</button>
        </div>
    </div>
</div>

<script>
// ── Timer ──────────────────────────────────────────────
const DURATION = {{ $exam->duration_minutes }} * 60;
let remaining = DURATION;
let timerEl = document.getElementById('timer');

function formatTime(s) {
    const m = String(Math.floor(s / 60)).padStart(2, '0');
    const ss = String(s % 60).padStart(2, '0');
    return m + ':' + ss;
}

const timerInterval = setInterval(() => {
    remaining--;
    timerEl.textContent = formatTime(remaining);
    if (remaining <= 300) timerEl.classList.add('warning');
    if (remaining <= 0) {
        clearInterval(timerInterval);
        submitExam();
    }
}, 1000);

// ── Answered tracking ──────────────────────────────────
function markAnswered(questionId) {
    document.getElementById('qcard-' + questionId).classList.add('answered');
    updateProgress();
}

function updateProgress() {
    const total = document.querySelectorAll('.question-card').length;
    const done  = document.querySelectorAll('.question-card.answered').length;
    document.getElementById('answered-count').textContent = done;
}

// ── Ordering: drag-and-drop ────────────────────────────
document.querySelectorAll('.ordering-list').forEach(list => {
    let dragged = null;

    list.addEventListener('dragstart', e => {
        dragged = e.target.closest('.order-item');
        dragged.classList.add('dragging');
    });
    list.addEventListener('dragend', e => {
        e.target.closest('.order-item')?.classList.remove('dragging');
        const qid = list.dataset.question;
        markAnswered(qid);
    });
    list.addEventListener('dragover', e => {
        e.preventDefault();
        const target = e.target.closest('.order-item');
        if (target && target !== dragged) {
            const rect = target.getBoundingClientRect();
            const after = e.clientY > rect.top + rect.height / 2;
            list.insertBefore(dragged, after ? target.nextSibling : target);
        }
    });
});

function moveItem(btn, dir, questionId) {
    const item = btn.closest('.order-item');
    const list = item.parentNode;
    if (dir === -1 && item.previousElementSibling) {
        list.insertBefore(item, item.previousElementSibling);
    } else if (dir === 1 && item.nextElementSibling) {
        list.insertBefore(item.nextElementSibling, item);
    }
    markAnswered(questionId);
}

// ── Collect answers ────────────────────────────────────
function collectAnswers() {
    const answers = {};

    document.querySelectorAll('.question-card').forEach(card => {
        const id   = card.dataset.questionId;
        const type = card.dataset.type;

        switch (type) {
            case 'true_false': {
                const el = card.querySelector(`input[name="q_${id}"]:checked`);
                if (el) answers[id] = el.value === 'true';
                break;
            }
            case 'single_choice': {
                const el = card.querySelector(`input[name="q_${id}"]:checked`);
                if (el) answers[id] = el.value;
                break;
            }
            case 'multiple_choice': {
                const checked = [...card.querySelectorAll(`input[name="q_${id}[]"]:checked`)];
                if (checked.length) answers[id] = checked.map(c => c.value);
                break;
            }
            case 'fill_blank': {
                const el = card.querySelector(`input[name="q_${id}"]`);
                if (el && el.value.trim()) answers[id] = el.value.trim();
                break;
            }
            case 'matching': {
                const pairs = {};
                card.querySelectorAll('.match-select').forEach(sel => {
                    if (sel.value) pairs[sel.dataset.left] = sel.value;
                });
                if (Object.keys(pairs).length) answers[id] = pairs;
                break;
            }
            case 'ordering': {
                const items = [...card.querySelectorAll('.order-item')];
                answers[id] = items.map(i => i.dataset.value);
                break;
            }
            case 'category': {
                const map = {};
                card.querySelectorAll('.cat-select').forEach(sel => {
                    if (sel.value) map[sel.dataset.item] = sel.value;
                });
                if (Object.keys(map).length) answers[id] = map;
                break;
            }
        }
    });

    return answers;
}

// ── Submit ─────────────────────────────────────────────
async function submitExam() {
    clearInterval(timerInterval);

    const btn = document.getElementById('submit-btn');
    btn.disabled = true;
    btn.textContent = 'Đang nộp...';

    const answers = collectAnswers();

    try {
        const res = await fetch('/exams/submit-ddd', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                exam_id: '{{ $exam->uuid_str }}',
                answers: answers,
            }),
        });

        const data = await res.json();
        const maxScore = {{ $questions->sum('score') }};

        if (res.ok && data.success) {
            showResult(data.data.score, maxScore);
        } else {
            alert('Lỗi: ' + (data.message || 'Không thể nộp bài.'));
            btn.disabled = false;
            btn.textContent = 'Nộp bài';
        }
    } catch (err) {
        alert('Lỗi kết nối. Vui lòng thử lại.');
        btn.disabled = false;
        btn.textContent = 'Nộp bài';
    }
}

function showResult(score, maxScore) {
    const pct = maxScore > 0 ? (score / maxScore) * 100 : 0;
    const icon = pct >= 80 ? '🏆' : pct >= 60 ? '🎉' : pct >= 40 ? '📝' : '💪';
    const msg  = pct >= 80 ? 'Xuất sắc!' : pct >= 60 ? 'Tốt lắm!' : pct >= 40 ? 'Cần cố gắng thêm' : 'Hãy ôn lại nhé!';
    const sub  = `Bạn đạt ${Math.round(pct)}% số điểm tối đa`;

    document.getElementById('result-icon').textContent = icon;
    document.getElementById('result-score').textContent = score;
    document.getElementById('result-msg').textContent = msg;
    document.getElementById('result-sub').textContent = sub;
    document.getElementById('result-overlay').classList.add('show');
}
</script>
</body>
</html>
