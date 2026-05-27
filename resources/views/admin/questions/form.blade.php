@extends('admin.layout')
@section('title', isset($question) ? 'Sửa câu hỏi' : 'Tạo câu hỏi mới')

@push('styles')
<style>
    .type-section { display: none; margin-top: 20px; padding-top: 20px; border-top: 1px solid #f1f5f9; }
    .section-title { font-size: .8rem; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: #64748b; margin-bottom: 12px; }
    .dyn-row { display: flex; align-items: center; gap: 8px; margin-bottom: 8px; }
    .dyn-row .form-input { flex: 1; }
    .key-input { width: 80px !important; flex: none !important; }
    .btn-rm { background: #fef2f2; border: 1px solid #fecaca; color: #ef4444; border-radius: 6px; padding: 6px 10px; cursor: pointer; font-size: .85rem; flex-shrink: 0; }
    .btn-rm:hover { background: #fee2e2; }
    .btn-add { display: inline-flex; align-items: center; gap: 5px; background: #f8fafc; border: 1.5px dashed #d1d5db; color: #64748b; padding: 7px 14px; border-radius: 8px; font-size: .82rem; font-weight: 600; cursor: pointer; margin-top: 4px; transition: all .15s; }
    .btn-add:hover { border-color: #4f46e5; color: #4f46e5; background: #eef2ff; }
    .type-pill {
        display: inline-block; padding: 3px 10px; border-radius: 99px; font-size: .78rem; font-weight: 700;
        background: #eef2ff; color: #4338ca; margin-bottom: 8px;
    }
    .arrow-sep { color: #94a3b8; flex-shrink: 0; font-size: .9rem; }
</style>
@endpush

@section('content')
<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('admin.questions.index') }}" class="btn btn-outline btn-sm">← Quay lại</a>
    <h2 style="font-size:1.1rem;font-weight:800;">{{ isset($question) ? 'Sửa câu hỏi' : 'Tạo câu hỏi mới' }}</h2>
</div>

<div class="card" style="max-width:760px;">
    <div class="card-body">
        <form method="POST"
              action="{{ isset($question) ? route('admin.questions.update', $question) : route('admin.questions.store') }}"
              id="question-form">
            @csrf
            @if(isset($question)) @method('PUT') @endif

            {{-- Common fields --}}
            <div class="grid-2" style="gap:16px;">
                <div class="form-group">
                    <label class="form-label">Loại câu hỏi <span style="color:#ef4444">*</span></label>
                    <select name="type" id="type-select" class="form-select" onchange="onTypeChange(this.value)" required>
                        <option value="true_false"      {{ old('type', $question->type ?? '') === 'true_false'      ? 'selected' : '' }}>Đúng / Sai</option>
                        <option value="single_choice"   {{ old('type', $question->type ?? '') === 'single_choice'   ? 'selected' : '' }}>Một đáp án</option>
                        <option value="multiple_choice" {{ old('type', $question->type ?? '') === 'multiple_choice' ? 'selected' : '' }}>Nhiều đáp án</option>
                        <option value="fill_blank"      {{ old('type', $question->type ?? '') === 'fill_blank'      ? 'selected' : '' }}>Điền vào chỗ trống</option>
                        <option value="matching"        {{ old('type', $question->type ?? '') === 'matching'        ? 'selected' : '' }}>Nối cặp</option>
                        <option value="ordering"        {{ old('type', $question->type ?? '') === 'ordering'        ? 'selected' : '' }}>Sắp xếp thứ tự</option>
                        <option value="category"        {{ old('type', $question->type ?? '') === 'category'        ? 'selected' : '' }}>Phân loại</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Điểm <span style="color:#ef4444">*</span></label>
                    <input type="number" name="score" class="form-input" style="max-width:120px;"
                           value="{{ old('score', $question->score ?? 1) }}" min="1" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Nội dung câu hỏi <span style="color:#ef4444">*</span></label>
                <textarea name="content" class="form-textarea" rows="3"
                          placeholder="Nhập nội dung câu hỏi..." required>{{ old('content', $question->content ?? '') }}</textarea>
            </div>

            {{-- ── TRUE / FALSE ── --}}
            <div class="type-section" id="section-true_false">
                <div class="section-title">Đáp án đúng</div>
                <select name="tf_correct" id="tf_correct" class="form-select" style="max-width:200px;">
                    <option value="1">✅ Đúng</option>
                    <option value="0">❌ Sai</option>
                </select>
            </div>

            {{-- ── SINGLE CHOICE ── --}}
            <div class="type-section" id="section-single_choice">
                <div class="section-title">Các lựa chọn</div>
                <div id="sc-opts"></div>
                <button type="button" class="btn-add" onclick="addOpt('sc')">+ Thêm lựa chọn</button>

                <div class="form-group" style="margin-top:16px;">
                    <label class="form-label">Đáp án đúng (nhập key, ví dụ: A)</label>
                    <input type="text" name="sc_correct" id="sc_correct" class="form-input" style="max-width:120px;" placeholder="A">
                </div>
            </div>

            {{-- ── MULTIPLE CHOICE ── --}}
            <div class="type-section" id="section-multiple_choice">
                <div class="section-title">Các lựa chọn</div>
                <div id="mc-opts"></div>
                <button type="button" class="btn-add" onclick="addOpt('mc')">+ Thêm lựa chọn</button>

                <div class="form-group" style="margin-top:16px;">
                    <label class="form-label">Đáp án đúng (nhập các key, cách nhau bởi dấu phẩy)</label>
                    <input type="text" name="mc_correct" id="mc_correct" class="form-input" style="max-width:240px;" placeholder="A,B">
                    <p class="form-hint">Ví dụ: A,C hoặc A,B,D</p>
                </div>
            </div>

            {{-- ── FILL BLANK ── --}}
            <div class="type-section" id="section-fill_blank">
                <div class="section-title">Các đáp án được chấp nhận</div>
                <p class="form-hint" style="margin-bottom:10px;">Hệ thống so sánh không phân biệt hoa thường và khoảng trắng.</p>
                <div id="fb-answers"></div>
                <button type="button" class="btn-add" onclick="addFbAnswer()">+ Thêm đáp án</button>
            </div>

            {{-- ── MATCHING ── --}}
            <div class="type-section" id="section-matching">
                <div class="section-title">Các cặp nối</div>
                <p class="form-hint" style="margin-bottom:10px;">Nhập vế trái và vế phải tương ứng.</p>
                <div id="match-pairs"></div>
                <button type="button" class="btn-add" onclick="addMatchPair()">+ Thêm cặp</button>
            </div>

            {{-- ── ORDERING ── --}}
            <div class="type-section" id="section-ordering">
                <div class="section-title">Thứ tự đúng</div>
                <p class="form-hint" style="margin-bottom:10px;">Nhập các mục <strong>theo đúng thứ tự</strong> từ trên xuống.</p>
                <div id="ord-items"></div>
                <button type="button" class="btn-add" onclick="addOrdItem()">+ Thêm mục</button>
            </div>

            {{-- ── CATEGORY ── --}}
            <div class="type-section" id="section-category">
                <div class="section-title">Danh sách loại (categories)</div>
                <div id="cat-categories"></div>
                <button type="button" class="btn-add" onclick="addCategory()">+ Thêm loại</button>

                <div style="margin-top:20px;">
                    <div class="section-title">Các mục và loại tương ứng</div>
                    <p class="form-hint" style="margin-bottom:10px;">Chọn loại phù hợp cho mỗi mục.</p>
                    <div id="cat-items"></div>
                    <button type="button" class="btn-add" onclick="addCatItem()">+ Thêm mục</button>
                </div>
            </div>

            <div style="display:flex;gap:10px;margin-top:28px;padding-top:20px;border-top:1px solid #f1f5f9;">
                <button type="submit" class="btn btn-primary">
                    {{ isset($question) ? '💾 Lưu thay đổi' : '✓ Tạo câu hỏi' }}
                </button>
                <a href="{{ route('admin.questions.index') }}" class="btn btn-outline">Hủy</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function esc(s) {
    return String(s ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// ── Type switching ──────────────────────────────────────────────────────────
function onTypeChange(type) {
    document.querySelectorAll('.type-section').forEach(s => s.style.display = 'none');
    const sec = document.getElementById('section-' + type);
    if (sec) sec.style.display = 'block';
}

// ── Single / Multiple Choice options ────────────────────────────────────────
function addOpt(prefix, key = '', label = '') {
    const c = document.getElementById(prefix + '-opts');
    const d = document.createElement('div');
    d.className = 'dyn-row';
    d.innerHTML = `
        <input type="text" name="${prefix}_key[]" value="${esc(key)}" placeholder="Key (A)" class="form-input key-input" maxlength="10">
        <input type="text" name="${prefix}_label[]" value="${esc(label)}" placeholder="Nội dung lựa chọn" class="form-input">
        <button type="button" class="btn-rm" onclick="this.closest('.dyn-row').remove()">✕</button>
    `;
    c.appendChild(d);
}

// ── Fill Blank answers ───────────────────────────────────────────────────────
function addFbAnswer(val = '') {
    const c = document.getElementById('fb-answers');
    const d = document.createElement('div');
    d.className = 'dyn-row';
    d.innerHTML = `
        <input type="text" name="fb_answers[]" value="${esc(val)}" placeholder="Đáp án được chấp nhận" class="form-input">
        <button type="button" class="btn-rm" onclick="this.closest('.dyn-row').remove()">✕</button>
    `;
    c.appendChild(d);
}

// ── Matching pairs ────────────────────────────────────────────────────────────
function addMatchPair(left = '', right = '') {
    const c = document.getElementById('match-pairs');
    const d = document.createElement('div');
    d.className = 'dyn-row';
    d.innerHTML = `
        <input type="text" name="match_left[]" value="${esc(left)}" placeholder="Vế trái" class="form-input">
        <span class="arrow-sep">→</span>
        <input type="text" name="match_right[]" value="${esc(right)}" placeholder="Vế phải" class="form-input">
        <button type="button" class="btn-rm" onclick="this.closest('.dyn-row').remove()">✕</button>
    `;
    c.appendChild(d);
}

// ── Ordering items ────────────────────────────────────────────────────────────
function addOrdItem(val = '') {
    const c = document.getElementById('ord-items');
    const d = document.createElement('div');
    d.className = 'dyn-row';
    const idx = c.children.length + 1;
    d.innerHTML = `
        <span style="width:24px;height:24px;background:#f1f5f9;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.75rem;font-weight:700;color:#64748b;flex-shrink:0;">${idx}</span>
        <input type="text" name="ord_items[]" value="${esc(val)}" placeholder="Mục thứ ${idx}" class="form-input">
        <button type="button" class="btn-rm" onclick="this.closest('.dyn-row').remove();reIndexOrd()">✕</button>
    `;
    c.appendChild(d);
}

function reIndexOrd() {
    document.querySelectorAll('#ord-items .dyn-row').forEach((row, i) => {
        row.querySelector('span').textContent = i + 1;
        row.querySelector('input').placeholder = 'Mục thứ ' + (i + 1);
    });
}

// ── Category ──────────────────────────────────────────────────────────────────
function addCategory(val = '') {
    const c = document.getElementById('cat-categories');
    const d = document.createElement('div');
    d.className = 'dyn-row';
    d.innerHTML = `
        <input type="text" name="cat_categories[]" value="${esc(val)}" placeholder="Tên loại" class="form-input" oninput="refreshCatSelects()">
        <button type="button" class="btn-rm" onclick="this.closest('.dyn-row').remove();refreshCatSelects()">✕</button>
    `;
    c.appendChild(d);
    refreshCatSelects();
}

function addCatItem(item = '', cat = '') {
    const c = document.getElementById('cat-items');
    const d = document.createElement('div');
    d.className = 'dyn-row cat-item-row';
    d.innerHTML = `
        <input type="text" name="cat_items[]" value="${esc(item)}" placeholder="Tên mục" class="form-input">
        <span class="arrow-sep">→</span>
        <select name="cat_item_categories[]" class="form-select" style="flex:1;"></select>
        <button type="button" class="btn-rm" onclick="this.closest('.dyn-row').remove()">✕</button>
    `;
    c.appendChild(d);

    const sel = d.querySelector('select');
    populateCatSelect(sel, cat);
}

function getCategoryNames() {
    return Array.from(document.querySelectorAll('input[name="cat_categories[]"]'))
        .map(i => i.value.trim())
        .filter(Boolean);
}

function populateCatSelect(sel, selectedVal = '') {
    const current = sel.value || selectedVal;
    sel.innerHTML = '<option value="">-- Chọn loại --</option>';
    getCategoryNames().forEach(cat => {
        const opt = document.createElement('option');
        opt.value = cat;
        opt.textContent = cat;
        if (cat === current) opt.selected = true;
        sel.appendChild(opt);
    });
}

function refreshCatSelects() {
    document.querySelectorAll('.cat-item-row select').forEach(sel => {
        populateCatSelect(sel);
    });
}

// ── Default rows for new questions ───────────────────────────────────────────
function initDefaults(type) {
    if ((type === 'single_choice' || type === 'multiple_choice') &&
        document.getElementById((type === 'single_choice' ? 'sc' : 'mc') + '-opts').children.length === 0) {
        const p = type === 'single_choice' ? 'sc' : 'mc';
        ['A', 'B', 'C', 'D'].forEach(k => addOpt(p, k, ''));
    }
    if (type === 'fill_blank' && document.getElementById('fb-answers').children.length === 0) {
        addFbAnswer();
    }
    if (type === 'matching' && document.getElementById('match-pairs').children.length === 0) {
        addMatchPair(); addMatchPair();
    }
    if (type === 'ordering' && document.getElementById('ord-items').children.length === 0) {
        addOrdItem(); addOrdItem(); addOrdItem();
    }
    if (type === 'category') {
        if (document.getElementById('cat-categories').children.length === 0) {
            addCategory('Loại A'); addCategory('Loại B');
        }
        if (document.getElementById('cat-items').children.length === 0) {
            addCatItem(); addCatItem();
        }
    }
}

// ── Edit mode: populate from saved payload ────────────────────────────────────
function initEdit(type, payload) {
    if (type === 'true_false') {
        document.getElementById('tf_correct').value = payload.correct ? '1' : '0';

    } else if (type === 'single_choice') {
        Object.entries(payload.options || {}).forEach(([k, v]) => addOpt('sc', k, v));
        document.querySelector('[name="sc_correct"]').value = payload.correct || '';

    } else if (type === 'multiple_choice') {
        Object.entries(payload.options || {}).forEach(([k, v]) => addOpt('mc', k, v));
        document.querySelector('[name="mc_correct"]').value = (payload.correct || []).join(',');

    } else if (type === 'fill_blank') {
        (payload.answers || []).forEach(a => addFbAnswer(a));

    } else if (type === 'matching') {
        Object.entries(payload.pairs || {}).forEach(([l, r]) => addMatchPair(l, r));

    } else if (type === 'ordering') {
        (payload.correct_order || []).forEach(i => addOrdItem(i));

    } else if (type === 'category') {
        (payload.categories || []).forEach(c => addCategory(c));
        // add items after categories so selects are populated
        Object.entries(payload.correct_map || {}).forEach(([item, cat]) => addCatItem(item, cat));
    }
}

// ── Boot ──────────────────────────────────────────────────────────────────────
const typeSelect = document.getElementById('type-select');
const initialType = typeSelect.value;

onTypeChange(initialType);

@if(isset($question))
    initEdit('{{ $question->type }}', @json($question->payload ?? []));
@else
    initDefaults(initialType);
@endif

typeSelect.addEventListener('change', function () {
    onTypeChange(this.value);
    initDefaults(this.value);
});
</script>
@endpush
