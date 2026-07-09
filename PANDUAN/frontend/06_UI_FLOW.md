# ALUR ANTARMUKA PENGGUNA (UI FLOW) - EDUSPHERE LMS

Dokumen ini menjelaskan alur navigasi halaman dan tata letak visual untuk ketiga peran pengguna: Admin, Guru, dan Siswa.

---

## 1. Alur Masuk (Auth Flow)
```
Halaman Welcome (Landing Page)
        ↓
  Laman Login (Input Email & Password)
        ↓ (Autentikasi & Cek Role Middleware)
┌───────────────────────┼────────────────────────┐
↓ (Role: Admin)         ↓ (Role: Teacher)        ↓ (Role: Student)
Dashboard Admin         Dashboard Guru           Dashboard Siswa
```

---

## 2. Alur Pengguna berdasarkan Peran

### A. Alur Admin (Administrator Flow)
Admin berfokus pada konfigurasi data master sekolah.
```
Dashboard Admin
   ├── > Menu Pengguna (List Guru, List Siswa, Tambah/Edit/Hapus User)
   ├── > Menu Kelas (List Kelas, Tambah Kelas, Daftarkan Siswa ke Kelas)
   └── > Menu Mata Pelajaran (List Mapel, Hubungkan Mapel ke Kelas & Guru)
```

### B. Alur Guru (Teacher Flow)
Guru mengelola konten pembelajaran dan memeriksa tugas masuk.
```
Dashboard Guru (Statistik tugas masuk & list mapel aktif)
        ↓
Pilih Mata Pelajaran (Detail Mapel)
        ├── Tab 1: Materi (List materi) ──> Tambah/Edit Materi (Teks/File/Link Video)
        ├── Tab 2: Tugas (List tugas) ──> Tambah/Edit Tugas (Esai/File/Kuis/Coding/Proyek)
        └── Tab 3: Siswa (Daftar siswa di kelas tersebut & progresnya)
                  ↓ (Lihat pengumpulan tugas siswa)
          Antrean Penilaian (Grading Queue)
                  ↓
          Detail Pengumpulan Siswa
                  ├── Baca jawaban esai / download file tugas / periksa kode
                  ├── Ketik feedback kualitatif & masukkan nilai angka (0-100)
                  └── Pilih aksi: [Simpan Nilai] atau [Minta Revisi] (Ubah status)
```

### C. Alur Siswa (Student Flow)
Siswa berfokus pada belajar mandiri, mengumpulkan tugas, dan melihat hasil evaluasi.
```
Dashboard Siswa (Progress belajar per mapel & tugas deadline terdekat)
        ↓
Pilih Mata Pelajaran
        ├── Tab 1: Materi ──> Buka Detail Materi ──> Baca/Tonton Video ──> Tombol [Tandai Selesai]
        ├── Tab 2: Tugas ──> Detail Tugas (Deskripsi & Deadline) 
        │         ├── Jika status 'Draft'/'Need Revision' ──> Form Pengumpulan Tugas (Tulis/Upload)
        │         └── Jika status 'Graded' ──> Tampil Nilai & Feedback Guru
        └── Tab 3: Portofolio ──> Tambah Portofolio (Pilih tugas terbaik untuk dipamerkan)
```

---

## 3. Ketentuan Desain UI/UX (Aesthetics & Design Guidelines)
- **Desain Modern & Premium**: Layout harus bersih, menggunakan ruang negatif (whitespace) dengan baik, rounded corners standar (`rounded-lg`, `rounded-xl`), dan border tipis.
- **Palet Warna Harmonis**:
  - Warna Utama (Primary): Indigo/Blue (Sleek professional look, misal: Indigo-600 `#4f46e5`).
  - Warna Sukses (Success): Emerald (untuk nilai bagus/status selesai).
  - Warna Peringatan (Warning): Amber/Orange (untuk tugas mendekati deadline/butuh revisi).
  - Mode Gelap/Terang (Light/Dark Mode Support): Gunakan background lembut (`bg-slate-50` untuk light mode) untuk menjaga kenyamanan mata.
- **Tipografi**: Gunakan font sans-serif modern yang terbaca jelas untuk tingkat SMP (misal: Outfit, Inter, atau Roboto).
- **Responsivitas**: Desain wajib *Mobile-First*. Menu navigasi di handphone harus menggunakan hamburger menu atau bottom navigation bar.
- **Efek Transisi & Mikro-animasi**:
  - Gunakan hover effect halus (`transition-all duration-200 ease-in-out`) pada tombol, navigasi, dan card tugas.
  - Tampilkan loader animasi yang estetik saat memproses pengunggahan file tugas.

