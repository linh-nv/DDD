<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('submission_answers');
        Schema::dropIfExists('submissions');
        Schema::dropIfExists('exam_questions');
        Schema::dropIfExists('exams');
        Schema::dropIfExists('questions');

        DB::statement("
            CREATE TABLE exams (
                id               BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                uuid             BINARY(16)      NOT NULL,
                title            VARCHAR(255)    NOT NULL,
                description      TEXT            NULL,
                duration_minutes INT UNSIGNED    NOT NULL DEFAULT 60,
                started_at       TIMESTAMP       NULL,
                ended_at         TIMESTAMP       NULL,
                is_active        TINYINT(1)      NOT NULL DEFAULT 1,
                created_at       TIMESTAMP       NULL,
                updated_at       TIMESTAMP       NULL,
                PRIMARY KEY (id),
                UNIQUE KEY uq_uuid (uuid)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        DB::statement("
            CREATE TABLE questions (
                id             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                uuid           BINARY(16)      NOT NULL,
                type           VARCHAR(255)    NOT NULL,
                content        TEXT            NOT NULL,
                option_a       VARCHAR(255)    NULL,
                option_b       VARCHAR(255)    NULL,
                option_c       VARCHAR(255)    NULL,
                option_d       VARCHAR(255)    NULL,
                correct_answer TEXT            NULL,
                payload        JSON            NULL,
                score          INT UNSIGNED    NOT NULL DEFAULT 1,
                created_at     TIMESTAMP       NULL,
                updated_at     TIMESTAMP       NULL,
                PRIMARY KEY (id),
                UNIQUE KEY uq_uuid (uuid),
                INDEX idx_type (type)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        DB::statement("
            CREATE TABLE exam_questions (
                id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                exam_id     BINARY(16)      NOT NULL,
                question_id BINARY(16)      NOT NULL,
                sort_order  INT UNSIGNED    NOT NULL DEFAULT 0,
                created_at  TIMESTAMP       NULL,
                updated_at  TIMESTAMP       NULL,
                PRIMARY KEY (id),
                UNIQUE KEY uq_exam_question (exam_id, question_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        DB::statement("
            CREATE TABLE submissions (
                id           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                uuid         BINARY(16)      NOT NULL,
                exam_id      BINARY(16)      NOT NULL,
                user_id      BIGINT UNSIGNED NOT NULL,
                score        INT UNSIGNED    NOT NULL DEFAULT 0,
                status       ENUM('in_progress','submitted','graded') NOT NULL DEFAULT 'submitted',
                submitted_at TIMESTAMP       NULL,
                created_at   TIMESTAMP       NULL,
                updated_at   TIMESTAMP       NULL,
                PRIMARY KEY (id),
                UNIQUE KEY uq_uuid (uuid),
                INDEX idx_exam_user (exam_id, user_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        DB::statement("
            CREATE TABLE submission_answers (
                id            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                submission_id BINARY(16)      NOT NULL,
                question_id   BINARY(16)      NOT NULL,
                answer        JSON            NULL,
                score         INT UNSIGNED    NOT NULL DEFAULT 0,
                created_at    TIMESTAMP       NULL,
                updated_at    TIMESTAMP       NULL,
                PRIMARY KEY (id),
                UNIQUE KEY uq_submission_question (submission_id, question_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('submission_answers');
        Schema::dropIfExists('submissions');
        Schema::dropIfExists('exam_questions');
        Schema::dropIfExists('exams');
        Schema::dropIfExists('questions');
    }
};
