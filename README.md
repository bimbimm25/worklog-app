# WORKLOG — Employee Activity Monitoring System

**WORKLOG** adalah sistem informasi berbasis web untuk pencatatan, pemantauan, dan pelaporan aktivitas operasional karyawan secara terpusat. Sistem ini memisahkan ruang kerja **Admin** dan **Karyawan** dengan kontrol akses berbasis peran (*Role-Based Access Control*).

---

## Ringkasan Teknis

- **Arsitektur:** Monolithic MVC (CodeIgniter 4)
- **Database:** MySQL (`InnoDB`, 14 tabel terelasi)
- **Antarmuka:** Bootstrap 5.3, Lucide Icons, Chart.js
- **Output Laporan:** PDF (Dompdf) dan Excel `.xlsx` (PhpSpreadsheet)

---

## Pembagian Role

1. **Admin**
   Mengelola master data organisasi, memantau absensi, meninjau dan menyetujui/menolak laporan aktivitas, memberikan tugas, memproses pengajuan izin/cuti, mengunduh laporan analitik, serta memantau log aktivitas sistem.
2. **Karyawan**
   Melakukan absensi harian, mencatat dan mengirimkan laporan aktivitas pekerjaan, memperbarui progres tugas yang ditugaskan, mengajukan izin/cuti, serta mengelola profil pribadi.

---

## Alur & Cara Kerja Sistem

### 1. Autentikasi & Kontrol Akses
- Pengguna masuk menggunakan email dan password yang terverifikasi dengan *password hashing*.
- Sistem membuat ulang ID sesi (*session regeneration*) saat login dan menyimpan identitas peran (`admin` atau `employee`) serta `employee_id`.
- Setiap permintaan halaman melewati lapisan filter (`AuthFilter` dan `RoleFilter`). Pengguna yang mengakses URL di luar wewenangnya otomatis dialihkan kembali ke dashboard masing-masing.
- Untuk mencegah akses data lintas pengguna (*IDOR*), seluruh kueri data milik karyawan selalu mengacu pada `employee_id` di dalam sesi server, bukan dari parameter URL.

### 2. Alur Absensi Harian
1. Karyawan menekan tombol **Check-in** pada halaman Absensi.
2. Sistem mengambil waktu langsung dari server (`Asia/Jakarta`) dan membandingkannya dengan `start_time` serta `late_tolerance_minutes` pada jadwal kerja (`work_schedules`) karyawan tersebut.
   - Jika waktu check-in melewati batas toleransi, status otomatis tercatat sebagai **Terlambat (`late`)**.
   - Jika masih dalam batas toleransi, status tercatat sebagai **Hadir (`present`)**.
3. Duplikasi absensi pada hari yang sama dicegah melalui validasi aplikasi dan *Unique Index* `(employee_id, date)` di database.
4. Saat karyawan menekan **Check-out**, sistem mencatat waktu pulang dan menghitung total durasi kerja dalam satuan menit secara otomatis.

### 3. Alur Pencatatan & Persetujuan Aktivitas
Siklus laporan aktivitas berjalan mengikuti alur status berikut:

```text
[Draft] ──(Kirim)──> [Pending] ──(Admin Review)──> [Approved]
                         │
                     (Ditolak + Alasan)
                         ▼
                    [Rejected] ──(Edit & Kirim Ulang)──> [Pending]
```

- **Pencatatan:** Karyawan mengisi judul, deskripsi, tanggal, jam mulai/selesai, persentase progres, status pekerjaan, serta lampiran bukti (opsional).
- **Review Admin:** Laporan berstatus `pending` muncul di panel Admin. Admin dapat melihat lampiran, menambahkan komentar diskusi, lalu menyetujui (`approved`) atau menolak (`rejected`) dengan alasan wajib.
- **Revisi:** Laporan berstatus `draft` atau `rejected` dapat diedit kembali oleh karyawan. Laporan yang sudah `approved` terkunci dan tidak dapat diubah.

### 4. Alur Manajemen Tugas
1. Admin membuat tugas baru dengan menentukan karyawan penanggung jawab (PIC), prioritas (`low`, `medium`, `high`, `urgent`), dan tenggat waktu (*deadline*).
2. Karyawan menerima notifikasi tugas baru dan dapat memperbarui persentase progres, status (`todo`, `in_progress`, `review`, `completed`), serta catatan pekerjaan.
3. Setiap pembaruan progres tersimpan sebagai riwayat kronologis di tabel `task_updates`.
4. Jika Admin mengubah *deadline* tugas, sistem otomatis mengirimkan notifikasi perubahan ke karyawan terkait.

### 5. Alur Pengajuan Izin & Cuti
1. Karyawan mengajukan permohonan (`izin`, `sakit`, atau `cuti`) dengan rentang tanggal, alasan, dan lampiran pendukung (misalnya surat dokter).
2. Status awal pengajuan adalah `pending` dan masuk ke daftar tinjauan Admin.
3. Admin menyetujui atau menolak pengajuan beserta catatan peninjauan, yang otomatis memicu notifikasi ke akun karyawan.

### 6. Alur Notifikasi & Audit Trail
- **Notifikasi:** Sistem secara otomatis mengirim notifikasi ke topbar pengguna saat terjadi peristiwa penting (laporan disetujui/ditolak, komentar baru dari Admin, tugas baru, perubahan deadline, dan keputusan izin/cuti).
- **Audit Trail:** Setiap tindakan penting (login, logout, perubahan master data, absensi, persetujuan laporan, hingga export dokumen) dicatat secara otomatis ke tabel `activity_logs` beserta waktu, modul, deskripsi, dan alamat IP.

### 7. Alur Pelaporan & Export
- Admin memilih jenis laporan (**Kehadiran**, **Aktivitas**, **Tugas**, atau **Izin & Cuti**), rentang tanggal, dan filter departemen.
- Sistem mengagregasi data menjadi ringkasan metrik, grafik distribusi (Chart.js), dan tabel pratinjau.
- Data yang telah difilter dapat langsung diekspor ke format **PDF** (dengan kop identitas perusahaan dari tabel `settings`) atau **Excel (`.xlsx`)**.

---

## Struktur Database

Sistem menggunakan 14 tabel yang terbagi dalam tiga kelompok utama:

- **Core & Organisasi:** `users`, `employees`, `departments`, `positions`, `work_schedules`
- **Operasional:** `attendance`, `work_activities`, `activity_comments`, `tasks`, `task_updates`, `leave_requests`
- **Sistem & Pendukung:** `notifications`, `activity_logs`, `settings`

---

## Mekanisme Keamanan

- **CSRF Protection:** Aktif secara global untuk seluruh permintaan `POST`.
- **HTTP Security Headers:** Mencegah *clickjacking* dan *MIME-sniffing*.
- **Validasi Upload:** Pembatasan ekstensi dan ukuran file di sisi server, penamaan file acak, serta pemblokiran eksekusi script pada direktori `public/uploads/`.
- **Integritas Data:** Penggunaan *Foreign Key*, *Database Transactions* pada pembuatan/pembaruan akun karyawan, serta *Query Builder* untuk mencegah SQL Injection.