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
        if (!Schema::hasTable('attendances')) {
            Schema::create('attendances', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->date('date');
                $table->timestamp('clock_in_time')->nullable();
                $table->timestamp('clock_out_time')->nullable();
                $table->decimal('total_hours', 8, 2)->default(0);
                $table->string('status')->default('absent');
                $table->text('notes')->nullable();
                $table->timestamps();

                // Add unique index so a user can only have one attendance record per day
                $table->unique(['user_id', 'date']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
