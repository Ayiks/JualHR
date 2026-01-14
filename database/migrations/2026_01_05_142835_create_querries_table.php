<?php

// database/migrations/xxxx_create_queries_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('queries', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number')->unique();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->foreignId('issued_by')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('type', [
                'verbal_warning',
                'written_warning',
                'final_warning',
                'query',
                'suspension',
                'other'
            ]);
            $table->string('subject');
            $table->text('description');
            $table->text('action_required')->nullable();
            $table->enum('severity', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->enum('status', ['open', 'responded', 'closed', 'escalated'])->default('open');
            $table->date('issued_date');
            $table->date('response_deadline')->nullable();
            $table->date('responded_at')->nullable();
            $table->date('closed_at')->nullable();
            $table->foreignId('closed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('closure_notes')->nullable();
            $table->string('document_path')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('employee_id');
            $table->index('status');
            $table->index('type');
            $table->index('issued_date');
            $table->index('reference_number');
        });

        Schema::create('query_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('query_id')->constrained()->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->text('response');
            $table->string('document_path')->nullable();
            $table->timestamps();

            $table->index('query_id');
            $table->index('employee_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('query_responses');
        Schema::dropIfExists('queries');
    }
};