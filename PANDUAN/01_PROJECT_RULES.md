# ATURAN PROYEK (PROJECT RULES) - EDUSPHERE LMS

Dokumen ini berisi prinsip inti, ketentuan, dan aturan pengembangan aplikasi Learning Management System (LMS) **Edusphere** untuk **SMP Full Day Al-Muhajirin**. Seluruh kontributor wajib mematuhi aturan di bawah ini.

---

## 1. Prinsip Inti Aplikasi (Core Principles) 
- **LMS Umum & Universal**: Aplikasi dirancang untuk mengelola semua mata pelajaran di sekolah (Matematika, IPA, IPS, Bahasa Inggris, Bahasa Indonesia, Pendidikan Agama Islam, Tahfidz Al-Qur'an, Informatika, dsb.), bukan hanya pelajaran pemrograman/coding.
- **Tiga Peran Pengguna (User Roles)**: Hak akses dibatasi secara ketat berdasarkan peran: **Admin**, **Guru**, dan **Siswa**.
- **Penilaian Manual oleh Guru**: Nilai akhir tugas sepenuhnya ditentukan secara manual oleh Guru pengampu. Tidak ada sistem otomatisasi penilaian (*auto-grading*) yang langsung menerbitkan nilai akhir ke siswa tanpa verifikasi guru.
- **Tugas Coding sebagai Media**: Modul tugas pemrograman (*coding assignment*) hanya digunakan sebagai alat bantu menulis dan mengumpulkan tugas Informatika. Sistem tidak menjalankan autograder otomatis untuk memberi nilai akhir, melainkan mempermudah guru membaca dan menilai kode siswa.
- **Dukungan Penuh Tugas Non-Coding**: Sistem harus mudah digunakan untuk mengumpulkan tugas berbentuk foto/scan tulisan tangan (untuk Matematika/IPA), dokumen teks (untuk Bahasa/IPS), hafalan suara (tahfidz), dan kuis pilihan ganda.

---

## 2. Jenis Tugas yang Didukung (Supported Assignment Types)
Setiap tugas yang dibuat oleh Guru harus mendukung salah satu tipe berikut:
1. **Tugas Essay (Teks Bebas)**: Siswa langsung mengetik jawaban pada editor teks di sistem.
2. **Pengumpulan File (File Submission)**: Siswa mengunggah dokumen (PDF, Word, Excel, Gambar/Scan tulisan tangan).
3. **Tugas Pemrograman (Coding Assignment)**: Siswa menulis kode (HTML, CSS, JS, Python) langsung di web editor sederhana atau mengunggah berkas kode sumber.
4. **Tugas Proyek (Project Assignment)**: Siswa mengumpulkan link proyek eksternal (Google Drive/GitHub) atau file tugas akhir berkelompok/mandiri.
5. **Kuis Pilihan Ganda (Quiz)**: Siswa menjawab daftar pertanyaan kuis. Hasil jawaban dapat dihitung otomatis sebagai nilai rekomendasi/referensi bagi Guru, namun konfirmasi nilai akhir tetap ada di tangan Guru.

---

## 3. Modul Utama Sistem (Main Modules)
- **Autentikasi & Profil**: Registrasi (oleh admin), login, logout, dan manajemen profil mandiri.
- **Manajemen Kelas & Pengguna**: Admin mengelola data kelas, siswa, guru, dan asosiasi mata pelajaran.
- **Manajemen Materi Pembelajaran**: Guru mengunggah materi (teks rich-text, file PDF/slide, embed video YouTube). Siswa dapat membaca dan menandai materi sebagai selesai.
- **Manajemen Tugas**: Guru membuat dan mengatur tenggat waktu tugas.
- **Pengumpulan Tugas (Submission)**: Siswa menyerahkan jawaban tugas. Sistem menyimpan riwayat pengumpulan.
- **Penilaian & Feedback**: Guru memberikan skor angka (0-100), komentar feedback kualitatif, atau meminta revisi.
- **Portofolio Siswa**: Halaman khusus untuk menampilkan tugas terbaik siswa yang telah dinilai dan disetujui untuk dipublikasikan.
- **Notifikasi**: Pemberitahuan secara real-time/in-app mengenai tugas baru, nilai yang keluar, atau permintaan revisi.
- **Progres Belajar (Learning Progress)**: Statistik kelengkapan belajar siswa per kelas dan per mata pelajaran.

---

## 4. Aturan Pengembangan & Teknis (Development Rules)
- **Desain UI yang Premium & Responsif**: Antarmuka harus rapi, modern, dan nyaman diakses dari smartphone, tablet, maupun komputer. Gunakan font modern (seperti Inter atau Outfit) dan layout Tailwind CSS yang bersih.
- **Role-Based Access Control (RBAC)**: Validasi hak akses harus dilakukan di sisi backend (Middleware Laravel) dan disesuaikan di sisi frontend (kondisional Blade/AlpineJS).
- **Jalur Audit (Audit Trail) Pengumpulan**: Sistem harus mencatat dan menyimpan versi pengumpulan siswa sebelum revisi dilakukan. Riwayat ini tidak boleh dihapus agar Guru dan Siswa dapat melihat progres perbaikan tugas.
- **Modular & Clean Code**: Gunakan pola standard Laravel (Controllers, Services/Repositories jika diperlukan, Eloquent Relations, Form Requests untuk validasi, dan Laravel Database Notifications).
- **Lokalisasi Bahasa**: Default bahasa aplikasi menggunakan **Bahasa Indonesia** karena sasaran pengguna adalah siswa dan guru SMP di Indonesia.

