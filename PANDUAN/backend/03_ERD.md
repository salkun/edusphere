# ENTITY RELATIONSHIP DIAGRAM (ERD) - EDUSPHERE LMS

Dokumen ini memetakan seluruh entitas data dan hubungan relasional dalam database LMS Edusphere.

---

## 1. Diagram ERD (Mermaid)

```mermaid
erDiagram
    users ||--o{ class_students : "sebagai student"
    users ||--o{ subjects : "sebagai teacher"
    users ||--o{ submission_histories : "mengubah status/revisi"
    users ||--o{ grades : "sebagai penilai (teacher)"
    users ||--o{ portfolios : "memiliki portfolio"
    
    classes ||--o{ class_students : "memiliki"
    classes ||--o{ subjects : "memiliki"
    
    subjects ||--o{ materials : "berisi"
    subjects ||--o{ assignments : "memiliki"
    
    assignments ||--o{ submissions : "memiliki"
    
    submissions ||--|| grades : "dinilai oleh"
    submissions ||--o{ submission_histories : "mencatat riwayat"
    submissions ||--o{ portfolios : "bisa dipublikasikan ke"

    users {
        bigint id PK
        string name
        string email UK
        string password
        string role "admin|teacher|student"
        timestamp created_at
    }

    classes {
        bigint id PK
        string name "e.g., Kelas 7-A"
        timestamp created_at
    }

    class_students {
        bigint class_id FK
        bigint student_id FK
    }

    subjects {
        bigint id PK
        string name "e.g., Matematika"
        bigint class_id FK
        bigint teacher_id FK
        timestamp created_at
    }

    materials {
        bigint id PK
        bigint subject_id FK
        string title
        longtext content
        string file_path
        string video_url
        timestamp created_at
    }

    assignments {
        bigint id PK
        bigint subject_id FK
        string title
        text description
        string type "essay|file|quiz|coding|project"
        datetime deadline
        timestamp created_at
    }

    submissions {
        bigint id PK
        bigint assignment_id FK
        bigint student_id FK
        text content
        string file_path
        string status "draft|submitted|need_revision|graded"
        timestamp created_at
    }

    submission_histories {
        bigint id PK
        bigint submission_id FK
        string status
        text content
        string file_path
        text comment
        bigint changed_by FK
        timestamp created_at
    }

    grades {
        bigint id PK
        bigint submission_id FK
        decimal score
        text feedback
        bigint graded_by FK
        timestamp created_at
    }

    portfolios {
        bigint id PK
        bigint student_id FK
        bigint submission_id FK "nullable"
        string title
        text description
        string file_path
        string status "draft|published"
        timestamp created_at
    }
```

---

## 2. Penjelasan Relasi
1. **Relasi Kelas & Siswa (`class_students`)**:
   - Menghubungkan banyak siswa ke suatu kelas tertentu (banyak ke banyak / *many-to-many*).
2. **Relasi Mata Pelajaran (`subjects`)**:
   - Setiap subjek (mata pelajaran spesifik untuk kelas tertentu) terikat ke **satu Kelas** (`class_id`) dan **satu Guru** (`teacher_id`).
3. **Relasi Materi & Tugas (`materials` & `assignments`)**:
   - Terikat langsung ke mata pelajaran spesifik (`subject_id`).
4. **Relasi Pengumpulan Tugas (`submissions`)**:
   - Menghubungkan tugas (`assignment_id`) dengan siswa yang mengerjakan (`student_id`).
5. **Relasi Riwayat Pengumpulan (`submission_histories`)**:
   - Menyimpan jejak audit (*audit trail*) setiap kali ada perubahan status tugas atau pengiriman revisi baru.
6. **Relasi Nilai (`grades`)**:
   - Berelasi satu-ke-satu (*one-to-one*) dengan `submissions`. Setiap pengumpulan tugas hanya dapat memiliki satu entri nilai final.
7. **Relasi Portofolio (`portfolios`)**:
   - Menghubungkan karya siswa dengan akun siswa (`student_id`). Siswa dapat melampirkan tugas yang telah dinilai (`submission_id`) sebagai item portofolionya.

