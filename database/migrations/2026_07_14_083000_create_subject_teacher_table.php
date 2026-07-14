<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('subject_teacher', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });

        // Pindahkan data dari subjects.teacher_id lama jika ada ke tabel pivot
        if (Schema::hasColumn('subjects', 'teacher_id')) {
            $subjects = DB::table('subjects')->whereNotNull('teacher_id')->get();
            foreach ($subjects as $subject) {
                DB::table('subject_teacher')->insert([
                    'subject_id' => $subject->id,
                    'teacher_id' => $subject->teacher_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subject_teacher');
    }
};
