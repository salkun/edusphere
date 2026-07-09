# SKEMA DATABASE - EDUSPHERE LMS

Dokumen ini mendeskripsikan secara rinci struktur tabel, tipe data, batasan (*constraints*), dan opsi kolom dalam database Edusphere LMS.

---

## 1. Tabel: `users`
Tabel utama untuk data autentikasi dan profil pengguna.

| Nama Kolom | Tipe Data | Atribut | Deskripsi |
| :--- | :--- | :--- | :--- |
| `id` | bigint | Primary Key, Auto Increment | ID unik pengguna. |
| `name` | varchar(255) | Not Null | Nama lengkap pengguna. |
| `email` | varchar(255) | Unique, Not Null | Alamat email (digunakan untuk login). |
| `email_verified_at` | timestamp | Nullable | Tanggal verifikasi email. |
| `password` | varchar(255) | Not Null | Hash password pengguna. |
| `role` | enum('admin','teacher','student') | Not Null, Default 'student' | Hak akses sistem. |
| `remember_token` | varchar(100) | Nullable | Token untuk fitur "remember me". |
| `created_at` | timestamp | Nullable | Waktu pembuatan akun. |
| `updated_at` | timestamp | Nullable | Waktu perubahan data akun. |

---

## 2. Tabel: `classes`
Tabel untuk mencatat data rombongan belajar / kelas siswa.

| Nama Kolom | Tipe Data | Atribut | Deskripsi |
| :--- | :--- | :--- | :--- |
| `id` | bigint | Primary Key, Auto Increment | ID unik kelas. |
| `name` | varchar(255) | Unique, Not Null | Nama kelas (misal: "Kelas 7-A", "Kelas 8-B"). |
| `created_at` | timestamp | Nullable | Waktu pembuatan kelas. |
| `updated_at` | timestamp | Nullable | Waktu perubahan data kelas. |

---

## 3. Tabel: `class_students` (Pivot Table)
Tabel penghubung antara siswa dan kelas.

| Nama Kolom | Tipe Data | Atribut | Deskripsi |
| :--- | :--- | :--- | :--- |
| `class_id` | bigint | Foreign Key (classes.id), Cascade On Delete | ID kelas terkait. |
| `student_id` | bigint | Foreign Key (users.id), Cascade On Delete | ID user (dengan role 'student'). |

- **Indeks**: Composite Primary Key (`class_id`, `student_id`).

---

## 4. Tabel: `subjects`
Tabel untuk mencatat mata pelajaran spesifik yang diajarkan oleh guru tertentu di kelas tertentu.

| Nama Kolom | Tipe Data | Atribut | Deskripsi |
| :--- | :--- | :--- | :--- |
| `id` | bigint | Primary Key, Auto Increment | ID unik mata pelajaran kelas. |
| `name` | varchar(255) | Not Null | Nama mata pelajaran (misal: "Matematika", "IPA"). |
| `class_id` | bigint | Foreign Key (classes.id), Cascade On Delete | Kelas tempat mata pelajaran diajarkan. |
| `teacher_id` | bigint | Foreign Key (users.id), Set Null On Delete | ID guru (role 'teacher') pengampu materi. |
| `created_at` | timestamp | Nullable | - |
| `updated_at` | timestamp | Nullable | - |

---

## 5. Tabel: `materials`
Tabel untuk mencatat modul atau materi pembelajaran yang diunggah Guru.

| Nama Kolom | Tipe Data | Atribut | Deskripsi |
| :--- | :--- | :--- | :--- |
| `id` | bigint | Primary Key, Auto Increment | ID unik materi. |
| `subject_id` | bigint | Foreign Key (subjects.id), Cascade On Delete | ID mata pelajaran terkait. |
| `title` | varchar(255) | Not Null | Judul materi pembelajaran. |
| `content` | longtext | Nullable | Catatan teks materi (rich-text format). |
| `file_path` | varchar(255) | Nullable | Path file lampiran (PDF/PPT/DOCX). |
| `video_url` | varchar(255) | Nullable | URL video tersemat (YouTube/Google Drive). |
| `created_at` | timestamp | Nullable | - |
| `updated_at` | timestamp | Nullable | - |

---

## 6. Tabel: `assignments`
Tabel untuk mencatat daftar tugas yang diberikan oleh Guru.

| Nama Kolom | Tipe Data | Atribut | Deskripsi |
| :--- | :--- | :--- | :--- |
| `id` | bigint | Primary Key, Auto Increment | ID unik tugas. |
| `subject_id` | bigint | Foreign Key (subjects.id), Cascade On Delete | ID mata pelajaran terkait. |
| `title` | varchar(255) | Not Null | Judul tugas. |
| `description` | text | Not Null | Petunjuk/instruksi pengerjaan tugas. |
| `type` | enum('essay','file','quiz','coding','project') | Not Null | Format tugas yang wajib dikumpulkan. |
| `deadline` | datetime | Not Null | Tenggat waktu pengumpulan. |
| `created_at` | timestamp | Nullable | - |
| `updated_at` | timestamp | Nullable | - |

---

## 7. Tabel: `submissions`
Tabel untuk menampung jawaban/pengumpulan tugas dari Siswa.

| Nama Kolom | Tipe Data | Atribut | Deskripsi |
| :--- | :--- | :--- | :--- |
| `id` | bigint | Primary Key, Auto Increment | ID unik pengumpulan. |
| `assignment_id` | bigint | Foreign Key (assignments.id), Cascade On Delete | ID tugas terkait. |
| `student_id` | bigint | Foreign Key (users.id), Cascade On Delete | ID siswa pengirim tugas. |
| `content` | text | Nullable | Jawaban teks (untuk tugas esai / link proyek). |
| `file_path` | varchar(255) | Nullable | Path file tugas yang diunggah (gambar/PDF). |
| `status` | enum('draft','submitted','need_revision','graded') | Not Null, Default 'submitted' | Status pengumpulan tugas saat ini. |
| `created_at` | timestamp | Nullable | - |
| `updated_at` | timestamp | Nullable | - |

---

## 8. Tabel: `submission_histories`
Tabel audit trail untuk mencatat riwayat perubahan status atau pengumpulan ulang revisi tugas.

| Nama Kolom | Tipe Data | Atribut | Deskripsi |
| :--- | :--- | :--- | :--- |
| `id` | bigint | Primary Key, Auto Increment | ID unik riwayat. |
| `submission_id` | bigint | Foreign Key (submissions.id), Cascade On Delete | ID pengumpulan tugas terkait. |
| `status` | varchar(50) | Not Null | Status yang tercatat. |
| `content` | text | Nullable | Arsip jawaban teks. |
| `file_path` | varchar(255) | Nullable | Arsip path file pengumpulan. |
| `comment` | text | Nullable | Catatan atau alasan revisi dari guru. |
| `changed_by` | bigint | Foreign Key (users.id), Set Null | User yang melakukan perubahan status. |
| `created_at` | timestamp | Nullable | Waktu terjadinya perubahan riwayat. |

---

## 9. Tabel: `grades`
Tabel untuk mencatat penilaian hasil tugas siswa.

| Nama Kolom | Tipe Data | Atribut | Deskripsi |
| :--- | :--- | :--- | :--- |
| `id` | bigint | Primary Key, Auto Increment | ID unik penilaian. |
| `submission_id` | bigint | Foreign Key (submissions.id), Cascade On Delete, Unique | ID pengumpulan tugas (Satu nilai per tugas). |
| `score` | decimal(5,2) | Not Null | Nilai numerik siswa (skala 0 - 100.00). |
| `feedback` | text | Nullable | Umpan balik kualitatif/catatan dari guru. |
| `graded_by` | bigint | Foreign Key (users.id), Set Null | Guru yang memberikan penilaian. |
| `created_at` | timestamp | Nullable | Waktu penilaian dilakukan. |
| `updated_at` | timestamp | Nullable | - |

---

## 10. Tabel: `portfolios`
Tabel untuk mengelola pajangan hasil karya terbaik yang dikurasi oleh Siswa dan disetujui Guru.

| Nama Kolom | Tipe Data | Atribut | Deskripsi |
| :--- | :--- | :--- | :--- |
| `id` | bigint | Primary Key, Auto Increment | ID unik portofolio. |
| `student_id` | bigint | Foreign Key (users.id), Cascade On Delete | Pemilik portofolio. |
| `submission_id` | bigint | Foreign Key (submissions.id), Set Null, Nullable | Hubungan ke tugas yang pernah dikerjakan. |
| `title` | varchar(255) | Not Null | Judul portofolio. |
| `description` | text | Not Null | Deskripsi mengenai hasil karya tersebut. |
| `file_path` | varchar(255) | Nullable | File karya tambahan. |
| `status` | enum('draft','published') | Not Null, Default 'draft' | Status visibilitas di halaman publik siswa. |
| `created_at` | timestamp | Nullable | - |

---

## 11. Tabel: `notifications`
Tabel bawaan Laravel untuk notifikasi aplikasi.

| Nama Kolom | Tipe Data | Atribut | Deskripsi |
| :--- | :--- | :--- | :--- |
| `id` | uuid | Primary Key | ID UUID notifikasi. |
| `type` | varchar(255) | Not Null | Class notifikasi Laravel. |
| `notifiable_type`| varchar(255) | Not Null | Model target (e.g. `App\Models\User`). |
| `notifiable_id` | bigint | Not Null | ID target user. |
| `data` | text | Not Null | Payload JSON data notifikasi. |
| `read_at` | timestamp | Nullable | Waktu notifikasi dibaca oleh user. |
| `created_at` | timestamp | Nullable | - |
| `updated_at` | timestamp | Nullable | - |

