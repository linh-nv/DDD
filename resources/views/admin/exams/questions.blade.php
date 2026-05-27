@extends('admin.layout')
@section('title', 'Câu hỏi: ' . $exam->title)

@section('content')
<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('admin.exams.index') }}" class="btn btn-outline btn-sm">← Đề thi</a>
    <div>
        <h2 style="font-size:1.1rem;font-weight:800;">{{ $exam->title }}</h2>
        <p class="text-xs text-muted">{{ $examQuestions->count() }} câu hỏi · {{ $exam->duration_minutes }} phút</p>
    </div>
    @if($exam->is_active)
        <span class="badge badge-green" style="margin-left:auto;">● Đang mở</span>
    @else
        <span class="badge badge-gray" style="margin-left:auto;">Đã ẩn</span>
    @endif
</div>

<div class="grid-2" style="gap:24px;align-items:start;">

    {{-- Left: Questions in exam --}}
    <div>
        <div class="card">
            <div class="card-header">
                <h2>Câu hỏi trong đề ({{ $examQuestions->count() }})</h2>
                @if($examQuestions->count() > 0)
                <form method="POST" action="{{ route('admin.exams.questions.reorder', $exam) }}" id="reorder-form">
                    @csrf
                    <button type="submit" class="btn btn-outline btn-sm">💾 Lưu thứ tự</button>
                </form>
                @endif
            </div>
            @if($examQuestions->count() > 0)
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th style="width:50px;">#</th>
                            <th>Câu hỏi</th>
                            <th style="width:80px;">Loại</th>
                            <th style="width:60px;">Điểm</th>
                            <th style="width:70px;">Thứ tự</th>
                            <th style="width:60px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($examQuestions as $i => $q)
                        <tr>
                            <td class="text-muted text-xs">{{ $i + 1 }}</td>
                            <td>
                                <div class="text-truncate" style="max-width:200px;font-size:.85rem;">{{ $q->content }}</div>
                            </td>
                            <td>
                                <span class="badge badge-indigo" style="font-size:.7rem;">{{ $q->type }}</span>
                            </td>
                            <td class="text-sm">{{ $q->score }}</td>
                            <td>
                                <input type="number" name="order[{{ $q->id }}]" value="{{ $q->pivot->sort_order }}"
                                       form="reorder-form"
                                       style="width:55px;padding:4px 6px;border:1.5px solid #d1d5db;border-radius:6px;font-size:.85rem;text-align:center;"
                                       min="1">
                            </td>
                            <td>
                                <form method="POST" action="{{ route('admin.exams.questions.remove', [$exam, $q]) }}"
                                      onsubmit="return confirm('Xóa câu hỏi này khỏi đề?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm btn-icon" title="Xóa khỏi đề">✕</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div style="padding:32px;text-align:center;color:#94a3b8;">
                <div style="font-size:2rem;margin-bottom:8px;">📋</div>
                <div class="text-sm">Chưa có câu hỏi nào. Thêm từ ngân hàng bên phải.</div>
            </div>
            @endif
        </div>
    </div>

    {{-- Right: Available question bank --}}
    <div>
        <div class="card">
            <div class="card-header">
                <h2>Ngân hàng câu hỏi ({{ $availableQuestions->count() }})</h2>
                <a href="{{ route('admin.questions.create') }}" class="btn btn-outline btn-sm" target="_blank">+ Tạo mới</a>
            </div>

            @if($availableQuestions->count() > 0)
            <div style="padding:12px 16px;border-bottom:1px solid #f1f5f9;">
                <input type="text" id="search-q" placeholder="🔍 Tìm câu hỏi..." oninput="filterQuestions(this.value)"
                       style="width:100%;padding:7px 11px;border:1.5px solid #d1d5db;border-radius:8px;font-size:.875rem;outline:none;">
            </div>
            <div id="question-bank" style="max-height:520px;overflow-y:auto;">
                <table>
                    <thead>
                        <tr>
                            <th>Câu hỏi</th>
                            <th style="width:80px;">Loại</th>
                            <th style="width:60px;">Điểm</th>
                            <th style="width:60px;"></th>
                        </tr>
                    </thead>
                    <tbody id="bank-tbody">
                        @foreach($availableQuestions as $q)
                        <tr class="bank-row" data-content="{{ strtolower($q->content) }}">
                            <td>
                                <div style="font-size:.85rem;line-height:1.4;max-width:220px;">{{ Str::limit($q->content, 80) }}</div>
                            </td>
                            <td><span class="badge badge-amber" style="font-size:.7rem;">{{ $q->type }}</span></td>
                            <td class="text-sm">{{ $q->score }}</td>
                            <td>
                                <form method="POST" action="{{ route('admin.exams.questions.add', $exam) }}">
                                    @csrf
                                    <input type="hidden" name="question_id" value="{{ $q->uuid_str }}">
                                    <button type="submit" class="btn btn-success btn-sm btn-icon" title="Thêm vào đề">+</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div style="padding:32px;text-align:center;color:#94a3b8;">
                <div style="font-size:2rem;margin-bottom:8px;">✅</div>
                <div class="text-sm">Tất cả câu hỏi đã được thêm vào đề này!</div>
            </div>
            @endif
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function filterQuestions(val) {
    val = val.toLowerCase();
    document.querySelectorAll('.bank-row').forEach(row => {
        row.style.display = row.dataset.content.includes(val) ? '' : 'none';
    });
}
</script>
@endpush
