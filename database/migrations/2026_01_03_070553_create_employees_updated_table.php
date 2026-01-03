<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            // Personal Information - New Fields
            $table->string('full_name')->nullable()->after('middle_name');
            $table->string('ssnit_number')->nullable()->unique()->after('phone');
            $table->string('ghana_card_number')->nullable()->unique()->after('ssnit_number');
            $table->string('tin_number')->nullable()->after('ghana_card_number');
            $table->enum('marital_status', ['single', 'married', 'divorced', 'widowed'])->nullable()->after('gender');
            
            // Job Information - Rename/Add Fields
            $table->string('work_email')->nullable()->after('email');
            $table->string('work_phone')->nullable()->after('work_email');
            $table->string('cell_phone')->nullable()->after('work_phone');
            
            // Bank Details
            $table->string('bank_name')->nullable()->after('emergency_contact_relationship');
            $table->string('bank_branch')->nullable()->after('bank_name');
            $table->string('account_name')->nullable()->after('bank_branch');
            $table->string('account_number')->nullable()->after('account_name');
            
            // Family Information
            $table->string('spouse_name')->nullable()->after('account_number');
            $table->string('spouse_contact')->nullable()->after('spouse_name');
            $table->integer('number_of_children')->default(0)->after('spouse_contact');
            $table->string('next_of_kin_name')->nullable()->after('number_of_children');
            $table->date('next_of_kin_dob')->nullable()->after('next_of_kin_name');
            $table->enum('next_of_kin_sex', ['male', 'female'])->nullable()->after('next_of_kin_dob');
            
            // Emergency Contact - Add Address
            $table->text('emergency_contact_address')->nullable()->after('emergency_contact_name');
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn([
                'full_name',
                'ssnit_number',
                'ghana_card_number',
                'tin_number',
                'marital_status',
                'work_email',
                'work_phone',
                'cell_phone',
                'bank_name',
                'bank_branch',
                'account_name',
                'account_number',
                'spouse_name',
                'spouse_contact',
                'number_of_children',
                'next_of_kin_name',
                'next_of_kin_dob',
                'next_of_kin_sex',
                'emergency_contact_address',
            ]);
        });
    }
};