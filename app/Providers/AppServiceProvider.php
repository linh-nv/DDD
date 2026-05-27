<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Testcenter\Domain\Exam\ExamQuestionRepository;
use Testcenter\Domain\Exam\ExamRepository;
use Testcenter\Domain\Question\QuestionRepository;
use Testcenter\Domain\Shared\DomainEventPublisher;
use Testcenter\Domain\Submission\SubmissionRepository;
use Testcenter\Infrastructure\Exam\MysqlExamQuestionRepository;
use Testcenter\Infrastructure\Exam\MysqlExamRepository;
use Testcenter\Infrastructure\Question\MysqlQuestionRepository;
use Testcenter\Infrastructure\Shared\LaravelEventPublisher;
use Testcenter\Infrastructure\Submission\MysqlSubmissionRepository;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(QuestionRepository::class, MysqlQuestionRepository::class);
        $this->app->singleton(SubmissionRepository::class, MysqlSubmissionRepository::class);
        $this->app->singleton(ExamRepository::class, MysqlExamRepository::class);
        $this->app->singleton(ExamQuestionRepository::class, MysqlExamQuestionRepository::class);
        $this->app->singleton(DomainEventPublisher::class, LaravelEventPublisher::class);
    }
}
