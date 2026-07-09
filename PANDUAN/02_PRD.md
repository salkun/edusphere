# PRODUCT REQUIREMENTS DOCUMENT (PRD) - EDUSPHERE LMS

## 1. Identitas Produk
- **Nama Produk**: Edusphere
- **Jenis Produk**: Learning Management System (LMS)
- **Target Pengguna**: SMP Full Day Al-Muhajirin (Siswa, Guru, Staf Admin)
- **Basis Teknologi**: Laravel 10.x, PHP 8.1+, Blade, Tailwind CSS, AlpineJS.

---

## 2. Visi & Tujuan Utama (Vision & Goals)
### Visi
Menjadi platform pembelajaran terintegrasi yang modern, estetik, dan responsif, guna mendukung kurikulum akademik, pendidikan agama (Islamic Full Day), serta keterampilan teknologi digital di SMP Full Day Al-Muhajirin.

### Tujuan Utama
1. **Pusat Pembelajaran Digital**: Mempermudah guru mendistribusikan materi ajar dalam berbagai format (teks, dokumen, video) secara terstruktur.
2. **Efisiensi Manajemen Tugas**: Memfasilitasi pengumpulan berbagai tipe tugas (tulisan tangan, esai, kuis, proyek, pemrograman/coding) dalam satu platform.
3. **Peningkatan Kualitas Feedback**: Memungkinkan interaksi penilaian dua arah antara guru dan siswa melalui fitur evaluasi, revisi tugas, dan pencatatan riwayat (audit trail).
4. **Pemantauan Progres Real-time**: Membantu siswa dan orang tua memantau ketercapaian belajar (materi yang dibaca & tugas yang dikerjakan) secara visual dan transparan.
5. **Showcase Prestasi**: Menyediakan ruang portofolio untuk mengapresiasi dan mempublikasikan karya terbaik siswa.

---

## 3. Hak Akses & Peran Pengguna (User Roles)
### A. Administrator (Admin)
Admin bertanggung jawab atas manajemen basis data awal aplikasi:
- **Manajemen Pengguna**: Melakukan registrasi, pembaruan, dan penonaktifan akun Guru, Siswa, dan sesama Admin.
- **Manajemen Kelas**: Membuat data kelas baru (misalnya: 7-A, 7-B, 8-A, dsb.) dan mendaftarkan siswa ke dalam kelas tersebut.
- **Manajemen Mata Pelajaran**: Mendaftarkan mata pelajaran (Matematika, Informatika, IPA, Tahfidz, dsb.) dan menetapkan kelas serta guru pengampunya.

### B. Guru (Teacher)
Guru adalah pengelola utama aktivitas kelas:
- **Manajemen Materi**: Membuat bab/topik baru, mempublikasikan materi (teks rich-text, file PDF/slide presentasi, link video pembelajaran YouTube).
- **Manajemen Tugas**: Membuat tugas baru dengan menentukan tipe tugas, tenggat waktu (*deadline*), bobot nilai, serta deskripsi instruksi.
- **Manajemen Penilaian**: Melihat daftar pengumpulan tugas siswa per kelas, memeriksa jawaban/file tugas, menginput skor (0-100), memberikan feedback tulisan, serta meminta revisi pengerjaan tugas jika diperlukan.

### C. Siswa (Student)
Siswa adalah subjek pembelajar dalam sistem:
- **Akses Pembelajaran**: Melihat mata pelajaran yang diikuti dan membuka materi-materi pembelajaran yang disediakan oleh Guru.
- **Pengumpulan Tugas**: Mengerjakan kuis, mengetik jawaban esai, mengunggah file (PDF/gambar/file pemrograman), atau menempelkan link proyek sesuai instruksi tugas sebelum tenggat waktu berakhir.
- **Evaluasi Mandiri**: Melihat nilai, membaca komentar umpan balik dari Guru, dan mengirimkan revisi tugas jika diminta.
- **Portofolio**: Mengajukan tugas-tugas terbaiknya yang telah dinilai oleh Guru untuk ditampilkan di laman portofolio profil publiknya.

---

## 4. Spesifikasi Fitur Utama (Features Specification)

### A. Dashboard Utama
- **Siswa**:
  - Widget ringkasan tugas aktif dengan tenggat waktu terdekat.
  - Grafik lingkaran (*donut chart*) atau progress bar ketercapaian materi pelajaran yang sudah dibaca.
  - Kartu aktivitas terbaru (misal: "Guru IPA baru saja memposting materi Bab 2").
- **Guru**:
  - Statistik cepat kelas yang diampu (jumlah siswa aktif, total tugas yang belum dinilai).
  - Daftar antrean pengumpulan tugas siswa (*grading queue*) yang membutuhkan penilaian segera.
- **Admin**:
  - Panel statistik sistem: Total Siswa, Total Guru, Total Kelas, dan Log sistem aktivitas terbaru.

### B. Modul Kelas & Mata Pelajaran (Subjects)
- Struktur navigasi yang rapi: Dashboard → Pilih Kelas → Daftar Mata Pelajaran → Detail Mata Pelajaran (terbagi menjadi tab: **Materi**, **Tugas**, dan **Siswa**).

### C. Modul Materi Pembelajaran
- Guru dapat mengunggah file attachment berkapasitas tertentu (PDF, PowerPoint, Word) ke penyimpanan internal server.
- Integrasi video: Input URL YouTube/Google Drive yang akan dirender secara otomatis sebagai video player tersemat (*embedded player*).
- Sistem "Mark as Read" bagi siswa untuk menandai materi yang sudah dipelajari.

### D. Modul Tugas (Assignments)
Mendukung lima jenis format pengumpulan:
1. **Essay**: Input teks rich-text langsung di halaman tugas.
2. **File**: Uploader file serbaguna (mendukung tipe dokumen `.docx, .pdf` dan gambar `.png, .jpg`).
3. **Coding**: Editor kode minimalis yang mendukung syntax highlighting dasar untuk bahasa web (HTML, CSS, JS) dan Python.
4. **Project**: Input URL (misal: Google Drive link, GitHub repository, dsb.) dan deskripsi proyek kelompok/mandiri.
5. **Quiz**: Halaman interaktif pengerjaan kuis pilihan ganda yang disiapkan oleh Guru.

### E. Penilaian, Feedback, & Revisi (Loop Penilaian)
- Guru dapat memicu status revisi pada submission siswa.
- Audit trail menyimpan versi tugas lama agar guru dapat membandingkan perbaikan tugas siswa dari waktu ke waktu.
- Riwayat status pengumpulan: `Draft` (belum dikirim) → `Submitted` (terkirim) → `Need Revision` (butuh revisi) → `Graded` (telah dinilai).

### F. Portofolio Siswa
- Halaman etalase portofolio digital siswa. Karya-karya terbaik (misalnya desain presentasi, esai terbaik, atau source code website) dapat ditampilkan setelah mendapat validasi guru.

### G. Notifikasi & Pengingat
- Notifikasi di dalam aplikasi (in-app notifications) untuk memicu pemberitahuan jika ada postingan materi baru, tugas baru, atau umpan balik tugas dari guru.
- Menampilkan indikator titik merah (*badge count*) untuk notifikasi belum dibaca.

