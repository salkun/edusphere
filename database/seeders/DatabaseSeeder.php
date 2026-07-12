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
            'name' => 'Leonard Putra',
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
            'file_path' => 'https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf',
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

        // 8. Create Announcements
        \App\Models\Announcement::create([
            'class_id' => $classroom->id,
            'teacher_id' => $teacherSiti->id, // Bu Siti A. (Wali Kelas)
            'title' => 'Persiapan Pekan Penilaian Tengah Semester (PTS) Genap',
            'content' => "Halo anak-anak kelas X RPL A,\n\nTidak terasa kita sudah mendekati pekan Penilaian Tengah Semester (PTS) Genap yang akan dimulai minggu depan. Mohon persiapkan diri kalian dengan baik, khususnya untuk mata pelajaran produktif seperti Pemrograman Web dan Desain Grafis.\n\nBeberapa hal penting yang perlu diperhatikan:\n1. Pastikan seluruh tugas dan praktikum mandiri sebelum pekan PTS sudah dikumpulkan di LMS.\n2. Pelajari kembali modul CSS Grid Layout dan Teori Warna Dasar yang telah dibagikan.\n3. Jaga kesehatan dan tetap istirahat yang cukup.\n\nJika ada materi yang masih belum dipahami, silakan hubungi ibu di ruang guru atau tanyakan langsung saat sesi pendampingan kelas.\n\nSelamat belajar dan semoga sukses mendapatkan hasil yang terbaik!\n\nSalam,\nBu Siti A. (Wali Kelas)",
            'image_path' => 'images/announcement_banner.png',
        ]);

        // 9. Create Report Card
        $reportCard = \App\Models\ReportCard::create([
            'student_id' => $student->id,
            'semester' => '1 (Satu) / Ganjil',
            'nis' => '10240951',
            'nisn' => '0084592041',
            'sick_days' => 2,
            'excused_days' => 1,
            'unexcused_days' => 0,
            'homeroom_notes' => 'Pertahankan prestasi belajar Anda. Terus tingkatkan kemampuan praktikum coding dan luangkan waktu untuk membaca dokumentasi terbaru. Sukses selalu untuk Leonard!',
        ]);

        \App\Models\ReportCardItem::create([
            'report_card_id' => $reportCard->id,
            'subject_id' => $subjectWeb->id,
            'final_grade' => 92.50,
            'competence' => 'Menunjukkan penguasaan sangat baik dalam merancang layout halaman web menggunakan HTML, CSS Grid, serta implementasi responsif.',
        ]);

        \App\Models\ReportCardItem::create([
            'report_card_id' => $reportCard->id,
            'subject_id' => $subjectDesign->id,
            'final_grade' => 88.00,
            'competence' => 'Menunjukkan penguasaan yang baik dalam menerapkan prinsip teori warna, tipografi dasar, dan pembuatan aset poster digital.',
        ]);

        \App\Models\ReportCardItem::create([
            'report_card_id' => $reportCard->id,
            'subject_id' => $subjectIndo->id,
            'final_grade' => 85.00,
            'competence' => 'Menunjukkan pemahaman yang baik dalam menganalisis struktur dan aspek kebahasaan dari teks eksplanasi ilmiah.',
        ]);

        \App\Models\ReportCardItem::create([
            'report_card_id' => $reportCard->id,
            'subject_id' => $subjectIpa->id,
            'final_grade' => 80.00,
            'competence' => 'Menunjukkan pemahaman yang cukup dalam mengamati proses fotosintesis pada tumbuhan air dan penyusunan laporan praktikum terstruktur.',
        ]);

        // 10. Create Second Report Card (Semester Genap)
        $reportCardGenap = \App\Models\ReportCard::create([
            'student_id' => $student->id,
            'semester' => '2 (Dua) / Genap',
            'nis' => '10240951',
            'nisn' => '0084592041',
            'sick_days' => 1,
            'excused_days' => 0,
            'unexcused_days' => 0,
            'homeroom_notes' => 'Pertahankan prestasi luar biasa ini, Leonard! Anda telah menunjukkan minat dan bakat yang sangat besar di bidang pemrograman. Selamat naik ke kelas XI!',
        ]);

        \App\Models\ReportCardItem::create([
            'report_card_id' => $reportCardGenap->id,
            'subject_id' => $subjectWeb->id,
            'final_grade' => 95.00,
            'competence' => 'Menunjukkan penguasaan sangat baik dalam membangun aplikasi web dinamis menggunakan framework Laravel, integrasi database, dan pembuatan RESTful API.',
        ]);

        \App\Models\ReportCardItem::create([
            'report_card_id' => $reportCardGenap->id,
            'subject_id' => $subjectDesign->id,
            'final_grade' => 90.00,
            'competence' => 'Menunjukkan penguasaan sangat baik dalam merancang interface (UI/UX) web premium dan membuat prototipe interaktif.',
        ]);

        \App\Models\ReportCardItem::create([
            'report_card_id' => $reportCardGenap->id,
            'subject_id' => $subjectIndo->id,
            'final_grade' => 87.00,
            'competence' => 'Menunjukkan penguasaan yang sangat baik dalam menyusun proposal kegiatan ilmiah dan membawakan presentasi akademis.',
        ]);

        \App\Models\ReportCardItem::create([
            'report_card_id' => $reportCardGenap->id,
            'subject_id' => $subjectIpa->id,
            'final_grade' => 82.00,
            'competence' => 'Menunjukkan pemahaman yang baik dalam menganalisis ekosistem lingkungan hidup dan dampak pencemaran air.',
        ]);
    }
}
