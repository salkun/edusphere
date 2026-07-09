# SPESIFIKASI API - EDUSPHERE LMS

Dokumen ini mendokumentasikan API endpoints yang tersedia dalam Edusphere LMS, terbagi berdasarkan modul fungsional sistem.

---

## 1. Modul Autentikasi (Authentication)

### POST `/api/login`
- **Tujuan**: Autentikasi pengguna dan pembuatan token.
- **Request Body**:
  ```json
  {
    "email": "siswa@almuhajirin.sch.id",
    "password": "password123"
  }
  ```
- **Response (200 OK)**:
  ```json
  {
    "token": "1|abc123xyz...",
    "user": {
      "id": 5,
      "name": "Budi Santoso",
      "email": "siswa@almuhajirin.sch.id",
      "role": "student"
    }
  }
  ```

### POST `/api/logout`
- **Tujuan**: Revoke token autentikasi saat ini.
- **Headers**: `Authorization: Bearer <token>`
- **Response (200 OK)**:
  ```json
  {
    "message": "Token revoked successfully"
  }
  ```

---

## 2. Modul Kelas & Mata Pelajaran (Classes & Subjects)

### GET `/api/classes`
- **Tujuan**: Mengambil daftar kelas (Hanya untuk Admin & Guru).
- **Response (200 OK)**:
  ```json
  [
    {
      "id": 1,
      "name": "Kelas 7-A",
      "students_count": 32
    }
  ]
  ```

### POST `/api/classes`
- **Tujuan**: Membuat kelas baru (Hanya untuk Admin).
- **Request Body**: `{"name": "Kelas 7-B"}`
- **Response (201 Created)**

### GET `/api/subjects`
- **Tujuan**: Mengambil mata pelajaran yang diampu (Guru) atau yang diikuti (Siswa).
- **Response (200 OK)**:
  ```json
  [
    {
      "id": 3,
      "name": "Informatika",
      "class": {
        "id": 1,
        "name": "Kelas 7-A"
      },
      "teacher": {
        "id": 2,
        "name": "Guru Komputer"
      }
    }
  ]
  ```

---

## 3. Modul Materi Pembelajaran (Materials)

### GET `/api/subjects/{subject_id}/materials`
- **Tujuan**: Mengambil seluruh materi dalam satu mata pelajaran.
- **Response (200 OK)**

### POST `/api/materials`
- **Tujuan**: Membuat materi baru (Hanya Guru).
- **Request Body (Multipart Form-Data)**:
  - `subject_id` (bigint, required)
  - `title` (string, required)
  - `content` (string/HTML, optional)
  - `file` (file attachment, optional)
  - `video_url` (string, optional)
- **Response (210 Created)**

---

## 4. Modul Tugas (Assignments)

### GET `/api/subjects/{subject_id}/assignments`
- **Tujuan**: Mengambil daftar tugas dalam mata pelajaran tertentu.
- **Response (200 OK)**

### POST `/api/assignments`
- **Tujuan**: Membuat tugas baru (Hanya Guru).
- **Request Body**:
  ```json
  {
    "subject_id": 3,
    "title": "Tugas Membuat Form HTML",
    "description": "Buatlah form login dengan input email dan password...",
    "type": "coding",
    "deadline": "2026-07-15 23:59:00"
  }
  ```
- **Response (201 Created)**

---

## 5. Modul Pengumpulan & Riwayat (Submissions & Audit Trail)

### POST `/api/assignments/{assignment_id}/submissions`
- **Tujuan**: Siswa mengumpulkan tugas.
- **Request Body (Multipart Form-Data)**:
  - `content` (text/HTML/source code, optional)
  - `file` (file upload, optional)
- **Response (200 OK)**

### GET `/api/submissions/{id}`
- **Tujuan**: Mengambil detail pengumpulan tugas, termasuk feedback nilai dan riwayat revisi.
- **Response (200 OK)**:
  ```json
  {
    "id": 12,
    "assignment_id": 2,
    "student_id": 5,
    "content": "Jawabannya...",
    "file_path": "uploads/tugas_ipa.pdf",
    "status": "need_revision",
    "grade": null,
    "history": [
      {
        "status": "need_revision",
        "comment": "Tolong perbaiki langkah nomor 3 yang kurang tepat.",
        "changed_by": "Guru IPA",
        "created_at": "2026-07-10 14:00:00"
      },
      {
        "status": "submitted",
        "comment": "Pertama kali dikirim.",
        "changed_by": "Budi Santoso",
        "created_at": "2026-07-09 10:00:00"
      }
    ]
  }
  ```

---

## 6. Modul Penilaian (Grades)

### POST `/api/submissions/{id}/grade`
- **Tujuan**: Memberikan nilai atau memicu revisi (Hanya Guru).
- **Request Body**:
  ```json
  {
    "score": 92.50,
    "feedback": "Kerja bagus! Form HTML Anda rapi.",
    "status": "graded" 
  }
  ```
  *(Catatan: Jika ingin meminta revisi, kirim `status: "need_revision"` dan isi `feedback` untuk menjelaskan perbaikannya).*
- **Response (200 OK)**

