<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Question;
use App\Models\Submission;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'exams'         => Exam::count(),
            'active_exams'  => Exam::where('is_active', true)->count(),
            'questions'     => Question::count(),
            'submissions'   => Submission::count(),
        ];

        $recentExams = Exam::withCount('questions')->latest()->limit(5)->get();

        return view('admin.dashboard', compact('stats', 'recentExams'));
    }
}
