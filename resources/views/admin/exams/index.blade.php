@extends('admin.layout')
@section('title', 'Quản lý đề thi')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 style="font-size:1.2rem;font-weight:800;">Đề thi</h2>
        <p class="text-sm text-muted">Tạo và quản lý các đề thi</p>
    </div>
    <a href="{{ route('admin.exams.create') }}" class="btn btn-primary">+ Tạo đề thi mới</a>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tên đề thi</th>
                    <th>Câu hỏi</th>
                    <th>Lượt nộp</th>
                    <th>Thời gian</th>
                    <th>Trạng thái</th>
                    <th>Link chia sẻ</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse($exams as $exam)
                <tr>
                    <td class="text-muted text-xs">{{ $exam->uuid_str }}</td>
                    <td>
                        <div style="font-weight:600;">{{ $exam->title }}</div>
                        @if($exam->description)
                            <div class="text-xs text-muted text-truncate" style="max-width:240px;">{{ $exam->description }}</div>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.exams.questions', $exam) }}" class="badge badge-indigo" style="text-decoration:none;">
                            {{ $exam->questions_count }} câu
                        </a>
                    </td>
                    <td>{{ $exam->submissions_count }}</td>
                    <td>{{ $exam->duration_minutes }} phút</td>
                    <td>
                        @if($exam->is_active)
                            <span class="badge badge-green">● Đang mở</span>
                        @else
                            <span class="badge badge-gray">Đã ẩn</span>
                        @endif
                    </td>
                    <td>
                        @if($exam->is_active)
                            <div style="display:flex;align-items:center;gap:6px;">
                                <input type="text" value="{{ url('/exam/' . $exam->uuid_str) }}"
                                       readonly
                                       style="width:180px;font-size:.75rem;padding:4px 8px;border:1px solid #e2e8f0;border-radius:6px;background:#f8fafc;color:#374151;"
                                       id="link-{{ $exam->uuid_str }}">
                                <button onclick="copyLink('{{ $exam->uuid_str }}')" class="btn btn-outline btn-sm btn-icon" title="Copy link">📋</button>
                            </div>
                        @else
                            <span class="text-xs text-muted">—</span>
                        @endif
                    </td>
                    <td>
                        <div style="display:flex;gap:6px;flex-wrap:wrap;">
                            <a href="{{ route('admin.exams.edit', $exam) }}" class="btn btn-outline btn-sm">Sửa</a>
                            <a href="{{ route('admin.exams.questions', $exam) }}" class="btn btn-outline btn-sm">Câu hỏi</a>
                            <form method="POST" action="{{ route('admin.exams.toggle-active', $exam) }}">
                                @csrf
                                <button type="submit" class="btn btn-sm {{ $exam->is_active ? 'btn-outline' : 'btn-success' }}">
                                    {{ $exam->is_active ? 'Ẩn' : 'Công bố' }}
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.exams.destroy', $exam) }}"
                                  onsubmit="return confirm('Xóa đề thi này? Thao tác không thể hoàn tác.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Xóa</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" style="text-align:center;padding:40px;color:#94a3b8;">Chưa có đề thi nào. <a href="{{ route('admin.exams.create') }}" style="color:#4f46e5;">Tạo ngay</a></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($exams->hasPages())
    <div style="padding:16px 20px;border-top:1px solid #f1f5f9;">
        {{ $exams->links() }}
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function copyLink(id) {
    const input = document.getElementById('link-' + id);
    input.select();
    document.execCommand('copy');
    const btn = input.nextElementSibling;
    btn.textContent = '✓';
    setTimeout(() => btn.textContent = '📋', 1500);
}
</script>
@endpush
