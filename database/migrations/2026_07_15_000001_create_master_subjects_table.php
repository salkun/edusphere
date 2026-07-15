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
        Schema::create('master_subjects', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::create('master_subject_teacher', function (Blueprint $table) {
            $table->id();
            $table->foreignId('master_subject_id')->constrained('master_subjects')->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });

        // Seed dari data mata pelajaran yang sudah ada di database
        $existingSubjects = DB::table('subjects')->get();
        $processedNames = [];

        foreach ($existingSubjects as $sub) {
            if (in_array($sub->name, $processedNames)) {
                continue;
            }

            try {
                $masterId = DB::table('master_subjects')->insertGetId([
                    'name' => $sub->name,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $processedNames[] = $sub->name;

                // Salin guru pengampu lama jika ada di tabel pivot
                $teachers = DB::table('subject_teacher')->where('subject_id', $sub->id)->get();
                foreach ($teachers as $t) {
                    DB::table('master_subject_teacher')->insert([
                        'master_subject_id' => $masterId,
                        'teacher_id' => $t->teacher_id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            } catch (\Exception $e) {
                // Abaikan jika ada duplikat nama
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_subject_teacher');
        Schema::dropIfExists('master_subjects');
    }
};
