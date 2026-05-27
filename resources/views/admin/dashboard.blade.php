@extends('admin.layout')
@section('title', 'Dashboard')

@section('content')
<div class="grid-4 mb-6">
    <div class="stat-card">
        <div class="stat-label">Tổng đề thi</div>
        <div class="stat-value">{{ $stats['exams'] }}</div>
        <div class="stat-sub">{{ $stats['active_exams'] }} đang hoạt động</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Đề đang mở</div>
        <div class="stat-value" style="color:#10b981;">{{ $stats['active_exams'] }}</div>
        <div class="stat-sub">Đang nhận bài làm</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Ngân hàng câu hỏi</div>
        <div class="stat-value">{{ $stats['questions'] }}</div>
        <div class="stat-sub">Tổng câu hỏi</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Lượt nộp bài</div>
        <div class="stat-value">{{ $stats['submissions'] }}</div>
        <div class="stat-sub">Toàn hệ thống</div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2>Đề thi gần đây</h2>
        <a href="{{ route('admin.exams.create') }}" class="btn btn-primary btn-sm">+ Tạo đề thi</a>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Tên đề thi</th>
                    <th>Câu hỏi</th>
                    <th>Thời gian</th>
                    <th>Trạng thái</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentExams as $exam)
                <tr>
                    <td class="text-truncate">{{ $exam->title }}</td>
                    <td>{{ $exam->questions_count }}</td>
                    <td>{{ $exam->duration_minutes }} phút</td>
                    <td>
                        @if($exam->is_active)
                            <span class="badge badge-green">Đang mở</span>
                        @else
                            <span class="badge badge-gray">Đã ẩn</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.exams.edit', $exam) }}" class="btn btn-outline btn-sm">Sửa</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-muted" style="text-align:center;padding:30px;">Chưa có đề thi nào.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
