<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'worker'])->default('worker')->after('password');
            $table->string('employee_id')->unique()->after('role');
            $table->string('department')->nullable()->after('employee_id');
            $table->string('phone')->nullable()->after('department');
            $table->enum('status', ['active', 'inactive'])->default('active')->after('phone');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'employee_id', 'department', 'phone', 'status']);
        });
    }
};