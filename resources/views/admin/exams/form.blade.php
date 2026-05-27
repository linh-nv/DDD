@extends('admin.layout')
@section('title', isset($exam) ? 'Sửa đề thi' : 'Tạo đề thi mới')

@section('content')
<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('admin.exams.index') }}" class="btn btn-outline btn-sm">← Quay lại</a>
    <div>
        <h2 style="font-size:1.1rem;font-weight:800;">{{ isset($exam) ? 'Sửa: ' . $exam->title : 'Tạo đề thi mới' }}</h2>
    </div>
</div>

<div class="card" style="max-width:680px;">
    <div class="card-body">
        <form method="POST" action="{{ isset($exam) ? route('admin.exams.update', $exam) : route('admin.exams.store') }}">
            @csrf
            @if(isset($exam)) @method('PUT') @endif

            <div class="form-group">
                <label class="form-label">Tên đề thi <span style="color:#ef4444">*</span></label>
                <input type="text" name="title" class="form-input {{ $errors->has('title') ? 'is-invalid' : '' }}"
                       value="{{ old('title', $exam->title ?? '') }}" placeholder="Ví dụ: Kiểm tra PHP cơ bản" required autofocus>
                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Mô tả</label>
                <textarea name="description" class="form-textarea {{ $errors->has('description') ? 'is-invalid' : '' }}"
                          placeholder="Mô tả ngắn về nội dung đề thi...">{{ old('description', $exam->description ?? '') }}</textarea>
                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Thời gian làm bài (phút) <span style="color:#ef4444">*</span></label>
                <input type="number" name="duration_minutes" class="form-input {{ $errors->has('duration_minutes') ? 'is-invalid' : '' }}"
                       value="{{ old('duration_minutes', $exam->duration_minutes ?? 60) }}" min="1" max="480" style="max-width:180px;" required>
                @error('duration_minutes')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <div class="form-check">
                    <input type="checkbox" name="is_active" id="is_active" value="1"
                           {{ old('is_active', $exam->is_active ?? false) ? 'checked' : '' }}>
                    <label for="is_active">Công bố đề thi (cho phép thí sinh làm bài)</label>
                </div>
                <p class="form-hint">Khi công bố, link chia sẻ sẽ hiển thị trên trang quản lý.</p>
            </div>

            <div style="display:flex;gap:10px;margin-top:24px;">
                <button type="submit" class="btn btn-primary">
                    {{ isset($exam) ? '💾 Lưu thay đổi' : '✓ Tạo đề thi' }}
                </button>
                <a href="{{ route('admin.exams.index') }}" class="btn btn-outline">Hủy</a>
            </div>
        </form>
    </div>
</div>

@if(isset($exam))
<div class="card mt-4" style="max-width:680px;">
    <div class="card-header">
        <h2>Link chia sẻ</h2>
        @if(!$exam->is_active)
            <span class="badge badge-gray">Cần công bố để chia sẻ</span>
        @endif
    </div>
    <div class="card-body">
        @if($exam->is_active)
            <p class="text-sm" style="margin-bottom:10px;">Chia sẻ link này với thí sinh:</p>
            <div style="display:flex;gap:8px;align-items:center;">
                <input type="text" id="share-link" value="{{ url('/exam/' . $exam->uuid_str) }}" readonly
                       class="form-input" style="background:#f8fafc;font-family:monospace;font-size:.85rem;">
                <button onclick="copyShareLink()" class="btn btn-outline" style="white-space:nowrap;">📋 Copy</button>
            </div>
        @else
            <p class="text-sm text-muted">Tick vào "Công bố đề thi" và lưu để tạo link chia sẻ.</p>
        @endif
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
function copyShareLink() {
    const input = document.getElementById('share-link');
    input.select();
    document.execCommand('copy');
    event.target.textContent = '✓ Đã copy';
    setTimeout(() => event.target.textContent = '📋 Copy', 1500);
}
</script>
@endpush
