# Aplikasi Pencatatan Pemakaian Uang Bulanan (Vanilla PHP)

## Deskripsi Proyek
Aplikasi ini adalah sistem pencatat keuangan sederhana yang dirancang untuk membantu pengguna mengelola pemasukan dan pengeluaran uang bulanan mereka. Dibuat menggunakan PHP murni (Vanilla PHP) tanpa framework, aplikasi ini fokus pada kesederhanaan dan kemudahan pengelolaan file. Aplikasi ini menyediakan fitur dasar untuk menambah, melihat, mengedit, dan menghapus catatan transaksi, serta menampilkan laporan keuangan bulanan.

## Teknologi yang Digunakan
* **Bahasa Pemrograman:** PHP (Vanilla PHP)
* **Database:** MySQL
* **Koneksi Database:** MySQLi (PHP native)
* **Frontend:** HTML, CSS Kustom (untuk struktur dasar dan beberapa gaya umum), Bootstrap 5.3.3 (untuk komponen UI dan responsivitas)
* **Upload File:** Native PHP file handling

## Fitur Utama
* **Pencatatan Transaksi:** Mencatat pemasukan dan pengeluaran dengan detail deskripsi, jumlah, dan tanggal.
* **Manajemen Bukti:** Mengunggah gambar bukti (.jpg/.jpeg) untuk setiap transaksi (opsional untuk transaksi baru, bisa diganti saat edit).
* **Tampilan Daftar Transaksi:** Menampilkan semua catatan transaksi (pemasukan dan pengeluaran digabungkan) dalam bentuk tabel yang terorganisir, diurutkan berdasarkan tanggal terbaru.
* **Edit Transaksi:** Memperbarui detail transaksi yang sudah ada (termasuk mengganti bukti).
* **Hapus Transaksi:** Menghapus catatan transaksi dari sistem, termasuk file bukti terkait.
* **Dashboard:** Tampilan awal selamat datang untuk pengguna.
* **Laporan Keuangan:** Menampilkan ringkasan detail pemasukan dan pengeluaran secara terpisah, beserta total pemasukan, total pengeluaran, dan saldo akhir.
* **Notifikasi:** Pesan sukses atau error setelah operasi CRUD (menggunakan PHP Session).
* **Sanitasi Input:** Fungsi dasar untuk membersihkan input pengguna demi keamanan.
* **Struktur File Sederhana:** Semua logika utama aplikasi (Routing, CRUD, Tampilan) terkonsolidasi dalam satu file `index.php`.

## Cara Menjalankan Aplikasi

### 1. Persiapan Lingkungan Server
Pastikan Anda memiliki lingkungan server PHP (misalnya XAMPP, Laragon, WAMP, atau LAMP stack) dengan konfigurasi berikut:
* PHP versi 7.4 atau lebih tinggi (direkomendasikan PHP 8.x).
* MySQL/MariaDB.
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
    ├── index.php             <-- File PHP utama aplikasi (sudah mencakup semua logika dan tampilan)
    ├── config.php            <-- Pengaturan koneksi database
    ├── style.css             <-- Styling kustom aplikasi
    ├── uploads/
    │   └── bukti/            <-- **Pastikan folder ini ada dan dapat ditulis oleh server (izin 777)**
    └── .htaccess             <-- Opsional, untuk URL bersih (jika menggunakan Apache)
    ```
    *Catatan: Folder `vendor/dompdf` tidak lagi diperlukan karena fitur cetak PDF telah dihapus.*

### 4. Jalankan Aplikasi
* Buka browser web Anda dan akses URL proyek Anda. Contoh: `http://localhost/catatan_uang_sederhana/`
