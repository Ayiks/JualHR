<?php

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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('employee_number', 50)->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('middle_name')->nullable();
            $table->string('email')->unique();
            $table->string('phone', 20)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->string('ssnit_number')->nullable()->unique();
            $table->string('ghana_card_number')->nullable()->unique();
            $table->string('tin_number')->nullable();
            $table->enum('marital_status', ['single', 'married', 'divorced', 'widowed'])->nullable();
            
            
            // Address
            $table->text('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('country', 100)->nullable();
            $table->string('postal_code', 20)->nullable();
            
            // Employment Details
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('line_manager_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->string('job_title')->nullable();
            $table->enum('employment_type', ['full_time', 'part_time', 'contract', 'intern'])->default('full_time');
            $table->enum('employment_status', ['active', 'on_leave', 'suspended', 'terminated', 'resigned'])->default('active');
            $table->date('date_of_joining')->nullable();
            $table->date('date_of_leaving')->nullable();
            $table->string('work_email')->nullable();
            $table->string('work_phone')->nullable();
            
            
            // Emergency Contact
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone', 20)->nullable();
            $table->string('emergency_contact_relationship', 100)->nullable();
            $table->text('emergency_contact_address')->nullable();

            // Bank Details
            $table->string('bank_name')->nullable()->after('emergency_contact_relationship');
            $table->string('bank_branch')->nullable()->after('bank_name');
            $table->string('account_name')->nullable()->after('bank_branch');
            $table->string('account_number')->nullable()->after('account_name');
            
            // Family Information
            $table->string('spouse_name')->nullable();
            $table->string('spouse_contact')->nullable();
            $table->integer('number_of_children')->default(0);
            $table->string('next_of_kin_name')->nullable();
            $table->date('next_of_kin_dob')->nullable();
            $table->enum('next_of_kin_sex', ['male', 'female'])->nullable();
            
            // Other
            $table->string('profile_photo')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->boolean('profile_completed')->default(false);

            
            // Indexes
            $table->index('employee_number');
            $table->index('email');
            $table->index('department_id');
            $table->index('line_manager_id');
            $table->index('employment_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
