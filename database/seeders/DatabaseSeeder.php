<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Classroom;
use App\Models\Subject;
use App\Models\Material;
use App\Models\Assignment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Student
        $student = User::create([
            'name' => 'Budi Santoso',
            'email' => 'siswa@almuhajirin.sch.id',
            'password' => Hash::make('password'),
            'role' => 'student',
        ]);

        // 2. Create Teachers
        $teacherAhmad = User::create([
            'name' => 'Pak Ahmad F.',
            'email' => 'ahmad@almuhajirin.sch.id',
            'password' => Hash::make('password'),
            'role' => 'teacher',
        ]);

        $teacherSiti = User::create([
            'name' => 'Bu Siti A.',
            'email' => 'siti@almuhajirin.sch.id',
            'password' => Hash::make('password'),
            'role' => 'teacher',
        ]);

        $teacherDedi = User::create([
            'name' => 'Pak Dedi K.',
            'email' => 'dedi@almuhajirin.sch.id',
            'password' => Hash::make('password'),
            'role' => 'teacher',
        ]);

        $teacherRina = User::create([
            'name' => 'Bu Rina H.',
            'email' => 'rina@almuhajirin.sch.id',
            'password' => Hash::make('password'),
            'role' => 'teacher',
        ]);

        // 3. Create Classroom (Wali kelas: Bu Siti A.)
        $classroom = Classroom::create([
            'name' => 'X RPL A',
            'homeroom_teacher_id' => $teacherSiti->id,
        ]);

        // 4. Attach Student to Classroom
        $classroom->students()->attach($student->id);

        // 5. Create Subjects linked to Class and Teachers (with schedules)
        $subjectWeb = Subject::create([
            'name' => 'Pemrograman Web',
            'class_id' => $classroom->id,
            'teacher_id' => $teacherAhmad->id,
            'day' => 'Senin',
            'start_time' => '07:30:00',
            'end_time' => '09:30:00',
        ]);

        $subjectDesign = Subject::create([
            'name' => 'Desain Grafis',
            'class_id' => $classroom->id,
            'teacher_id' => $teacherSiti->id,
            'day' => 'Selasa',
            'start_time' => '09:45:00',
            'end_time' => '11:45:00',
        ]);

        $subjectIndo = Subject::create([
            'name' => 'Bahasa Indonesia',
            'class_id' => $classroom->id,
            'teacher_id' => $teacherDedi->id,
            'day' => 'Rabu',
            'start_time' => '07:30:00',
            'end_time' => '09:00:00',
        ]);

        $subjectIpa = Subject::create([
            'name' => 'IPA Terpadu',
            'class_id' => $classroom->id,
            'teacher_id' => $teacherRina->id,
            'day' => 'Kamis',
            'start_time' => '10:00:00',
            'end_time' => '12:00:00',
        ]);

        // 6. Create Materials
        Material::create([
            'subject_id' => $subjectWeb->id,
            'title' => 'CSS Grid Layout',
            'content' => 'Materi tentang CSS Grid Layout untuk merancang tata letak halaman web secara dua dimensi.',
            'file_path' => 'https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf',
        ]);

        Material::create([
            'subject_id' => $subjectDesign->id,
            'title' => 'Color Theory Basics',
            'content' => 'Dasar-dasar teori warna termasuk warna primer, sekunder, komplementer, dan psikologi warna dalam desain.',
            'file_path' => 'https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf',
        ]);

        Material::create([
            'subject_id' => $subjectIndo->id,
            'title' => 'Struktur Teks Eksplanasi',
            'content' => 'Mempelajari struktur penulisan teks eksplanasi yang terdiri dari pernyataan umum, deretan penjelas, dan interpretasi.',
            'file_path' => 'https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf',
        ]);

        // 7. Create Assignments
        Assignment::create([
            'subject_id' => $subjectWeb->id,
            'title' => 'Membuat Landing Page Sederhana',
            'description' => 'Tugas membuat landing page sederhana menggunakan HTML dan CSS Grid. Kumpulkan dalam bentuk file source code.',
            'type' => 'coding',
            'deadline' => Carbon::tomorrow()->setTime(23, 59, 0),
        ]);

        Assignment::create([
            'subject_id' => $subjectDesign->id,
            'title' => 'Poster Edukasi Lingkungan',
            'description' => 'Buat poster digital bertema edukasi lingkungan hidup menggunakan aplikasi desain pilihan Anda. Kumpulkan dalam format PDF/PNG.',
            'type' => 'file',
            'deadline' => Carbon::now()->addDays(3)->setTime(23, 59, 0),
        ]);

        Assignment::create([
            'subject_id' => $subjectIndo->id,
            'title' => 'Teks Eksplanasi',
            'description' => 'Tulis sebuah teks eksplanasi mengenai proses terjadinya fenomena alam (misal: hujan atau gempa bumi) langsung di editor.',
            'type' => 'essay',
            'deadline' => Carbon::now()->addDays(5)->setTime(23, 59, 0),
        ]);

        Assignment::create([
            'subject_id' => $subjectIpa->id,
            'title' => 'Laporan Praktikum',
            'description' => 'Buat laporan tertulis tentang praktikum fotosintesis tumbuhan air. Scan hasil kerja tangan dan upload dalam format PDF.',
            'type' => 'file',
            'deadline' => Carbon::now()->addDays(7)->setTime(23, 59, 0),
        ]);
    }
}
