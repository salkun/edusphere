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
        // 1. Add homeroom_teacher_id to classes table
        Schema::table('classes', function (Blueprint $table) {
            $table->foreignId('homeroom_teacher_id')->nullable()->after('name')->constrained('users')->onDelete('set null');
        });

        // 2. Add day, start_time, and end_time to subjects table
        Schema::table('subjects', function (Blueprint $table) {
            $table->string('day')->nullable()->after('teacher_id'); // e.g. 'Senin', 'Selasa'
            $table->time('start_time')->nullable()->after('day');
            $table->time('end_time')->nullable()->after('start_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropColumn(['day', 'start_time', 'end_time']);
        });

        Schema::table('classes', function (Blueprint $table) {
            $table->dropForeign(['homeroom_teacher_id']);
            $table->dropColumn('homeroom_teacher_id');
        });
    }
};
