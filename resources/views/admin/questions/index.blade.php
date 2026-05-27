@extends('admin.layout')
@section('title', 'Ngân hàng câu hỏi')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 style="font-size:1.2rem;font-weight:800;">Câu hỏi</h2>
        <p class="text-sm text-muted">Ngân hàng câu hỏi toàn hệ thống</p>
    </div>
    <a href="{{ route('admin.questions.create') }}" class="btn btn-primary">+ Tạo câu hỏi</a>
</div>

<div class="card">
    <div style="padding:14px 18px;border-bottom:1px solid #f1f5f9;">
        <input type="text" id="search-q" placeholder="🔍 Tìm theo nội dung..." oninput="filterQ(this.value)"
               style="width:100%;max-width:360px;padding:8px 12px;border:1.5px solid #d1d5db;border-radius:8px;font-size:.875rem;outline:none;">
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nội dung câu hỏi</th>
                    <th>Loại</th>
                    <th>Điểm</th>
                    <th>Đáp án đúng</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody id="q-tbody">
                @forelse($questions as $q)
                <tr class="q-row" data-content="{{ strtolower($q->content) }}">
                    <td class="text-muted text-xs">{{ $q->id }}</td>
                    <td>
                        <div style="max-width:340px;font-size:.875rem;line-height:1.5;">{{ Str::limit($q->content, 100) }}</div>
                    </td>
                    <td>
                        @php
                            $typeColors = [
                                'true_false'      => 'badge-green',
                                'single_choice'   => 'badge-indigo',
                                'multiple_choice' => 'badge-indigo',
                                'fill_blank'      => 'badge-amber',
                                'matching'        => 'badge-amber',
                                'ordering'        => 'badge-amber',
                                'category'        => 'badge-amber',
                            ];
                            $typeLabels = [
                                'true_false'      => 'Đúng/Sai',
                                'single_choice'   => 'Một đáp án',
                                'multiple_choice' => 'Nhiều đáp án',
                                'fill_blank'      => 'Điền vào chỗ trống',
                                'matching'        => 'Nối cặp',
                                'ordering'        => 'Sắp xếp',
                                'category'        => 'Phân loại',
                            ];
                        @endphp
                        <span class="badge {{ $typeColors[$q->type] ?? 'badge-gray' }}">{{ $typeLabels[$q->type] ?? $q->type }}</span>
                    </td>
                    <td>{{ $q->score }}</td>
                    <td>
                        <div class="text-xs text-muted text-truncate" style="max-width:160px;">{{ $q->correct_answer ?? '—' }}</div>
                    </td>
                    <td>
                        <div style="display:flex;gap:6px;">
                            <a href="{{ route('admin.questions.edit', $q) }}" class="btn btn-outline btn-sm">Sửa</a>
                            <form method="POST" action="{{ route('admin.questions.destroy', $q) }}"
                                  onsubmit="return confirm('Xóa câu hỏi này?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Xóa</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" style="text-align:center;padding:40px;color:#94a3b8;">Chưa có câu hỏi nào. <a href="{{ route('admin.questions.create') }}" style="color:#4f46e5;">Tạo ngay</a></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($questions->hasPages())
    <div style="padding:16px 20px;border-top:1px solid #f1f5f9;">
        {{ $questions->links() }}
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function filterQ(val) {
    val = val.toLowerCase();
    document.querySelectorAll('.q-row').forEach(row => {
        row.style.display = row.dataset.content.includes(val) ? '' : 'none';
    });
}
</script>
@endpush
