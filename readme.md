# Aplikasi Pencatatan Pemakaian Uang Bulanan

## Deskripsi Proyek
Aplikasi ini adalah sistem pencatat keuangan sederhana yang dirancang untuk membantu pengguna mengelola pemasukan dan pengeluaran uang bulanan mereka. Aplikasi ini dibuat menggunakan PHP murni (Vanilla PHP) tanpa framework, dengan tujuan untuk kesederhanaan dan kemudahan pengelolaan file. Aplikasi ini menyediakan fitur untuk menambah, melihat, mengedit, dan menghapus catatan transaksi, serta menghasilkan laporan keuangan bulanan dalam format web dan PDF.

## Teknologi yang Digunakan
* **Bahasa Pemrograman:** PHP (Vanilla PHP)
* **Database:** MySQL
* **Koneksi Database:** MySQLi (PHP native)
* **Frontend:** HTML, CSS Kustom (dengan inspirasi desain minimalis), JavaScript (minimal)
* **Laporan PDF:** Dompdf

## Fitur Utama dan Tambahan
### Fitur Utama:
* **Pencatatan Transaksi:** Mencatat pemasukan dan pengeluaran dengan detail deskripsi, jumlah, dan tanggal.
* **Manajemen Bukti:** Mengunggah gambar bukti (.jpg/.jpeg) untuk setiap transaksi (opsional untuk transaksi baru, bisa diubah pada edit).
* **Tampilan Daftar Transaksi:** Menampilkan semua catatan transaksi (pemasukan dan pengeluaran digabungkan) dalam bentuk tabel yang terorganisir, diurutkan berdasarkan tanggal terbaru.
* **Edit Transaksi:** Memperbarui detail transaksi yang sudah ada (termasuk mengganti bukti).
* **Hapus Transaksi:** Menghapus catatan transaksi dari sistem, termasuk file bukti terkait.
* **Dashboard:** Tampilan awal selamat datang.
* **Laporan Keuangan:** Menampilkan ringkasan detail pemasukan dan pengeluaran secara terpisah, beserta total pemasukan, total pengeluaran, dan saldo akhir.

### Fitur Tambahan:
* **Cetak Laporan PDF:** Kemampuan untuk mencetak laporan keuangan yang sama dengan tampilan web dalam format PDF.
* **Notifikasi:** Pesan sukses atau error setelah operasi CRUD (menggunakan PHP Session).
* **Sanitasi Input:** Fungsi dasar untuk membersihkan input pengguna demi keamanan.
* **Struktur File Sederhana:** Semua logika utama aplikasi (Routing, CRUD, Tampilan) terkonsolidasi dalam satu file `index.php`.

## Cara Menjalankan Aplikasi

### 1. Persiapan Lingkungan Server
Pastikan Anda memiliki lingkungan server PHP (misalnya XAMPP, Laragon, WAMP, atau LAMP stack) dengan konfigurasi berikut:
* PHP versi 7.4 atau lebih tinggi (direkomendasikan PHP 8.x)
* MySQL/MariaDB
* Ekstensi PHP `mysqli` harus aktif.
* Pastikan PHP memiliki izin untuk menulis ke folder `uploads/bukti`.

### 2. Konfigurasi Database
Aplikasi ini menggunakan database MySQL.
* Buat database baru di MySQL Anda, misalnya bernama `duit`.
* Jalankan skema SQL berikut untuk membuat tabel `pemasukan` dan `pengeluaran`:

    ```sql
    CREATE DATABASE IF NOT EXISTS duit;

    USE duit;

    DROP TABLE IF EXISTS pemasukan;

    CREATE TABLE pemasukan (
        id INT AUTO_INCREMENT PRIMARY KEY,
        deskripsi VARCHAR(255) NOT NULL,
        jumlah INT NOT NULL,
        tanggal DATE NOT NULL,
        bukti VARCHAR(255) NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    );

    DROP TABLE IF EXISTS pengeluaran;

    CREATE TABLE pengeluaran (
        id INT AUTO_INCREMENT PRIMARY KEY,
        deskripsi VARCHAR(255) NOT NULL,
        jumlah INT NOT NULL,
        tanggal DATE NOT NULL,
        bukti VARCHAR(255) NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    );

    INSERT INTO pemasukan (deskripsi, jumlah, tanggal, bukti) VALUES
    ('Gaji Bulanan', 7500000, '2025-06-01', 'gaji_juni.jpg'),
    ('Bonus Proyek', 1000000, '2025-06-10', NULL);

    INSERT INTO pengeluaran (deskripsi, jumlah, tanggal, bukti) VALUES
    ('Bayar Listrik', 300000, '2025-06-05', 'listrik_juni.png'),
    ('Belanja Mingguan', 500000, '2025-06-11', 'belanja_minggu_1.jpg'),
    ('Internet Bulanan', 250000, '2025-06-03', NULL);
    ```
* Konfigurasi koneksi database di file `config.php`:

    ```php
    <?php
    define('DB_SERVER', 'localhost');
    define('DB_USERNAME', 'root');
    define('DB_PASSWORD', '');    
    define('DB_NAME', 'duit');

    $conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

    if ($conn->connect_error) {
        die("Koneksi database gagal: " . $conn->connect_error);
    }
    ?>
    ```

### 3. Pengaturan Folder dan File Proyek
1.  Buat folder baru di dalam `htdocs` (XAMPP) atau `www` (Laragon/WAMP) dengan nama proyek Anda, contoh: `catatan_uang_sederhana`.
2.  Di dalam folder `catatan_uang_sederhana`, buat struktur folder dan file berikut:
    ```
    catatan_uang_sederhana/
    ├── index.php             <-- File PHP utama aplikasi
    ├── config.php            <-- Pengaturan koneksi database
    ├── style.css             <-- Styling aplikasi
    ├── uploads/
    │   └── bukti/            <-- **Pastikan folder ini ada dan dapat ditulis oleh server (izin 777)**
    ├── vendor/               <-- Tempat library Dompdf
    │   └── dompdf/           <-- Hasil ekstrak Dompdf dari GitHub
    └── .htaccess             <-- Opsional, untuk URL bersih
    ```
3.  **Unduh Dompdf:** Kunjungi [https://github.com/dompdf/dompdf/releases](https://github.com/dompdf/dompdf/releases) dan unduh versi stabil terbaru (misalnya `dompdf_x.x.x.zip`). Ekstrak isinya ke dalam folder `vendor/dompdf/`.
4.  Isi file `index.php`, `config.php`, dan `style.css` dengan kode yang telah disediakan sebelumnya.

### 4. Jalankan Aplikasi
* Buka browser web Anda dan akses URL proyek Anda. Contoh: `http://localhost/catatan_uang_sederhana/`

## Screenshot Aplikasi
*(Sisipkan screenshot aplikasi Anda di sini. Pastikan jalur gambar benar dan relatif terhadap `README.md`.)*

### Dashboard
![Screenshot Dashboard](path/to/screenshot/dashboard.png)

### Daftar Transaksi
![Screenshot Daftar Transaksi](path/to/screenshot/daftar_transaksi.png)

### Form Tambah Transaksi
![Screenshot Form Tambah Transaksi](path/to/screenshot/tambah_transaksi.png)

### Form Edit Transaksi
![Screenshot Form Edit Transaksi](path/to/screenshot/edit_transaksi.png)

### Laporan Keuangan (Web)
![Screenshot Laporan Web](path/to/screenshot/laporan_web.png)

### Laporan Keuangan (PDF)
![Screenshot Laporan PDF](path/to/screenshot/laporan_pdf.png)