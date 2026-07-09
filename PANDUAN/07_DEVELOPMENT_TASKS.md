# TUGAS PENGEMBANGAN (DEVELOPMENT TASKS) - EDUSPHERE LMS

Dokumen ini membagi pengerjaan proyek Edusphere LMS menjadi beberapa fase terstruktur untuk memudahkan kolaborasi tim developer.

---

## FASE 1: Autentikasi, Role Management, & Inisiasi Database
- [ ] **Desain & Migrasi Database Awal**:
  - Modifikasi skema `users` (tambah kolom `role`).
  - Buat tabel `classes` dan pivot `class_students`.
  - Buat tabel `subjects`.
- [ ] **Seeder Database Awal**:
  - Siapkan data default (Admin, Guru, Siswa, Kelas, dan Mata Pelajaran).
- [ ] **Middleware & Otorisasi**:
  - Buat middleware `RoleMiddleware` untuk validasi role `'admin'`, `'teacher'`, dan `'student'`.
- [ ] **Alur Login & Redirect**:
  - Modifikasi `AuthenticatedSessionController` agar mengarahkan user ke dashboard yang sesuai setelah sukses login.
- [ ] **Halaman Dashboard Statis/Dinamis Awal**:
  - Dashboard Admin, Guru, dan Siswa.

## FASE 2: Manajemen Kelas, Mata Pelajaran, & Pengguna (Admin Panel)
- [ ] **Manajemen Pengguna (CRUD Users)**:
  - Form pendaftaran guru/siswa baru dan pengaturan kelas oleh Admin.
- [ ] **Manajemen Kelas (CRUD Classes)**:
  - Pengelolaan daftar rombongan belajar (rombel) oleh Admin.
- [ ] **Manajemen Mata Pelajaran (CRUD Subjects)**:
  - Form pembuatan mata pelajaran, pemilihan guru pengampu, dan penetapan kelas oleh Admin.

## FASE 3: Modul Materi Pembelajaran (Material Module)
- [ ] **Migrasi & Model Materi**:
  - Buat tabel `materials` beserta relasinya.
- [ ] **Fitur Guru (Kelola Materi)**:
  - Form tambah/edit/hapus materi dengan input teks (wysiwyg/rich text), uploader berkas (PDF/PPTX), dan input link video YouTube.
- [ ] **Fitur Siswa (Akses & Progress Materi)**:
  - Tampilan daftar materi per mata pelajaran.
  - Halaman detail baca materi dengan tombol "Tandai Selesai" (*Mark as Read*) untuk mencatat progress belajar.

## FASE 4: Modul Tugas (Assignment Module)
- [ ] **Migrasi & Model Tugas**:
  - Buat tabel `assignments` dengan kolom tipe tugas.
- [ ] **Fitur Guru (Kelola Tugas)**:
  - Form pembuatan tugas dengan pilihan tipe tugas (Essay, File, Coding, Project, Quiz).
  - Integrasi set deadline waktu.
- [ ] **Fitur Siswa (Daftar Tugas)**:
  - Tampilan list tugas aktif dan tugas lampau di halaman siswa beserta statusnya.

## FASE 5: Modul Pengumpulan Tugas (Submission Module)
- [ ] **Migrasi & Model Pengumpulan**:
  - Buat tabel `submissions` dan tabel audit trail `submission_histories`.
- [ ] **Form Pengumpulan Tugas (Siswa)**:
  - Form dinamis berdasarkan tipe tugas:
    - *Essay*: Textarea/Rich text editor.
    - *File*: File input uploader.
    - *Coding*: Code editor minimalis (textarea dengan styling monospaced).
    - *Project*: Input URL eksternal + deskripsi ringkas.
- [ ] **Jalur Audit (Audit Trail)**:
  - Implementasi logic controller untuk mencatat riwayat versi tugas lama ke `submission_histories` saat siswa mengirim revisi.

## FASE 6: Modul Penilaian & Feedback (Grading Loop)
- [ ] **Migrasi & Model Penilaian**:
  - Buat tabel `grades` relasi one-to-one ke `submissions`.
- [ ] **Fitur Guru (Penilaian)**:
  - Panel pemeriksaan tugas masuk berdasarkan antrean per mata pelajaran.
  - Input skor numerik dan feedback kualitatif.
  - Tombol aksi "Minta Revisi" (mengubah status submission menjadi `need_revision`).
- [ ] **Fitur Siswa (Lihat Hasil)**:
  - Tampilan detail nilai dan komentar guru di halaman tugas siswa.

## FASE 7: Modul Portofolio Siswa (Portfolio Module)
- [ ] **Migrasi & Model Portofolio**:
  - Buat tabel `portfolios`.
- [ ] **Fitur Siswa (Kelola Portofolio)**:
  - Form memilih tugas terbaik yang sudah dinilai untuk dimasukkan ke Portofolio.
  - Pengaturan status `draft` atau `published`.
- [ ] **Halaman Portofolio Publik**:
  - Halaman etalase publik yang menampilkan kumpulan karya terbaik dari siswa tertentu yang bisa dilihat oleh guru atau siswa lainnya.

## FASE 8: Modul Notifikasi (Notification Module)
- [ ] **Database Notification Laravel**:
  - Inisiasi tabel notifikasi bawaan Laravel.
- [ ] **Pemicu Notifikasi (Triggers)**:
  - Kirim notifikasi saat Guru memposting materi/tugas baru (target: siswa kelas terkait).
  - Kirim notifikasi saat Siswa mengumpulkan/merevisi tugas (target: guru mata pelajaran).
  - Kirim notifikasi saat Guru selesai menilai/meminta revisi (target: siswa pengumpul).

## FASE 9: Pengujian, Optimasi, & Deployment
- [ ] **Penulisan Automated Tests**:
  - Uji validasi input, fungsionalitas grading loop, dan keamanan role (RBAC).
- [ ] **Optimasi Performa & Security**:
  - Optimasi query Eloquent (menghindari N+1 query).
  - Validasi ukuran file upload materi/tugas.
- [ ] **Deployment Preparation**:
  - Konfigurasi file `.env` produksi, caching assets, dan pengujian server lokal.

