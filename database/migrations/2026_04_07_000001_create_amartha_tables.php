<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('application_code')->unique();
            $table->decimal('amount', 12, 2);
            $table->unsignedTinyInteger('tenor_months');
            $table->decimal('interest_rate', 5, 2)->default(2.50);
            $table->enum('status', ['draft', 'submitted', 'reviewed', 'approved', 'rejected', 'disbursed', 'ongoing', 'completed'])->default('submitted');
            $table->date('submitted_at')->nullable();
            $table->date('approved_at')->nullable();
            $table->date('disbursed_at')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });

        Schema::create('loan_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained('loans')->cascadeOnDelete();
            $table->string('document_type');
            $table->string('file_name');
            $table->string('file_path')->nullable();
            $table->enum('verification_status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->timestamps();
        });

        Schema::create('installments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained('loans')->cascadeOnDelete();
            $table->unsignedTinyInteger('sequence');
            $table->date('due_date');
            $table->decimal('amount', 12, 2);
            $table->enum('status', ['pending', 'paid', 'late'])->default('pending');
            $table->date('paid_at')->nullable();
            $table->timestamps();
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('message');
            $table->string('type')->default('info');
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });

        Schema::create('learning_modules', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('duration_label');
            $table->string('format')->default('video-singkat');
            $table->text('summary');
            $table->unsignedInteger('display_order')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
        });

        Schema::create('learning_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('learning_module_id')->constrained('learning_modules')->cascadeOnDelete();
            $table->unsignedTinyInteger('completion_percent')->default(0);
            $table->boolean('is_completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'learning_module_id']);
        });

        Schema::create('community_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('content');
            $table->enum('category', ['chat', 'tip', 'event'])->default('chat');
            $table->date('event_date')->nullable();
            $table->string('event_location')->nullable();
            $table->timestamps();
        });

        Schema::create('community_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('community_post_id')->constrained('community_posts')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('content');
            $table->timestamps();
        });

        Schema::create('ojk_reports', function (Blueprint $table) {
            $table->id();
            $table->date('report_date');
            $table->unsignedInteger('female_borrowers')->default(0);
            $table->unsignedInteger('male_borrowers')->default(0);
            $table->unsignedInteger('active_loans')->default(0);
            $table->decimal('total_disbursed', 14, 2)->default(0);
            $table->decimal('total_outstanding', 14, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ojk_reports');
        Schema::dropIfExists('community_comments');
        Schema::dropIfExists('community_posts');
        Schema::dropIfExists('learning_progress');
        Schema::dropIfExists('learning_modules');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('installments');
        Schema::dropIfExists('loan_documents');
        Schema::dropIfExists('loans');
    }
};
