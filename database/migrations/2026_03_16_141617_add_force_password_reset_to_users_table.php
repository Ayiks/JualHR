<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('force_password_reset')->default(false);
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->string('cell_phone', 20)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('force_password_reset');
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn('cell_phone');
        });
    }
};
