<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            // Add head of department (points to employee)
            $table->foreignId('head_of_department_id')
                  ->nullable()
                  ->after('description')
                  ->constrained('employees')
                  ->onDelete('set null');
            
            $table->index('head_of_department_id');
        });
    }

    public function down(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->dropForeign(['head_of_department_id']);
            $table->dropColumn('head_of_department_id');
        });
    }
};