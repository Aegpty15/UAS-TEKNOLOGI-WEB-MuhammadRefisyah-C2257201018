# Aplikasi Pencatatan Pemakaian Uang Bulanan

## Deskripsi Proyek
Aplikasi ini adalah sistem pencatat keuangan sederhana yang dirancang untuk membantu pengguna mengelola pemasukan dan pengeluaran uang bulanan mereka. Aplikasi ini menyediakan fitur untuk menambah, melihat, mengedit, dan menghapus catatan transaksi, serta menghasilkan laporan keuangan bulanan.

## Teknologi yang Digunakan
* **Framework:** CodeIgniter 4
* **Bahasa Pemrograman:** PHP
* **Database:** MySQL
* **Manajemen Database:** Adminer (atau alat serupa seperti phpMyAdmin)
* **Frontend:** Bootstrap (berbasis SB Admin 2 template), HTML, CSS, JavaScript
* **Laporan PDF:** Custom library `Cetakpdf` (kemungkinan menggunakan mPDF atau sejenisnya)

## Fitur Utama dan Tambahan
### Fitur Utama:
* **Pencatatan Transaksi:** Mencatat pemasukan dan pengeluaran dengan detail deskripsi, jumlah, dan tanggal.
* **Manajemen Bukti:** Mengunggah gambar bukti (.jpg) untuk setiap transaksi (opsional untuk transaksi baru, bisa diubah pada edit).
* **Tampilan Daftar Transaksi:** Menampilkan semua catatan transaksi dalam bentuk tabel yang terorganisir.
* **Edit Transaksi:** Memperbarui detail transaksi yang sudah ada.
* **Hapus Transaksi:** Menghapus catatan transaksi dari sistem.
* **Dashboard:** Tampilan awal yang memberikan informasi umum.
* **Laporan Keuangan:** Menampilkan ringkasan total pemasukan, total pengeluaran, dan saldo akhir.

### Fitur Tambahan:
* **Cetak Laporan PDF:** Kemampuan untuk mencetak laporan keuangan dalam format PDF.
* **Notifikasi:** Pesan sukses atau error setelah operasi CRUD.
* **Navigasi Sidebar:** Menu navigasi yang intuitif, termasuk tautan kembali ke dashboard dari judul utama sidebar.

## Cara Menjalankan Aplikasi

### 1. Persiapan Lingkungan Server
Pastikan Anda memiliki lingkungan server PHP (misalnya XAMPP, Laragon, WAMP, atau LAMP stack) dengan konfigurasi berikut:
* PHP versi 7.4 atau lebih tinggi (direkomendasikan PHP 8.x)
* MySQL/MariaDB
* Composer (untuk manajemen dependensi CodeIgniter, jika ada)

### 2. Konfigurasi Database
Aplikasi ini menggunakan database MySQL.
* Buat database baru di MySQL Anda, misalnya bernama `duit`.
* Jalankan skema SQL berikut untuk membuat tabel `transaksi`:

    ```sql
    -- Hapus tabel yang ada jika sudah ada untuk menghindari konflik (HATI-HATI: Ini akan menghapus SEMUA DATA!)
    DROP TABLE IF EXISTS transaksi;

    -- Buat tabel 'transaksi' baru
    CREATE TABLE transaksi (
        id INT AUTO_INCREMENT PRIMARY KEY,
        tipe_transaksi ENUM('pemasukan', 'pengeluaran') NOT NULL,
        deskripsi VARCHAR(255) NOT NULL,
        jumlah INT NOT NULL,
        tanggal DATE NOT NULL,
        bukti VARCHAR(255) NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    );

    -- Opsional: Tambahkan beberapa data contoh
    INSERT INTO transaksi (tipe_transaksi, deskripsi, jumlah, tanggal, bukti) VALUES
    ('pemasukan', 'Gaji Bulanan', 7500000, '2025-06-01', 'gaji_juni.jpg'),
    ('pengeluaran', 'Bayar Listrik', 300000, '2025-06-05', 'listrik_juni.png'),
    ('pemasukan', 'Bonus Proyek', 1000000, '2025-06-10', NULL),
    ('pengeluaran', 'Belanja Mingguan', 500000, '2025-06-11', 'belanja_minggu_1.jpg'),
    ('pengeluaran', 'Internet Bulanan', 250000, '2025-06-03', NULL);
    ```
* Konfigurasi koneksi database di file `.env` atau `app/Config/Database.php` Anda:
    ```
    # Di file .env (ubah DBNAME, DBUSER, DBPASS menjadi yang sesuai)
    database.default.hostname = localhost
    database.default.database = duit
    database.default.username = root
    database.default.password =
    database.default.DBDriver = MySQLi
    database.default.DBPrefix =
    database.default.port = 3306
    ```
    (Jika Anda menggunakan file `.env`, pastikan Anda menyalin `env` ke `.env` dan uncomment baris database).

### 3. Penempatan File Proyek
* Tempatkan seluruh folder proyek CodeIgniter Anda ke dalam `htdocs` (untuk XAMPP) atau `www` (untuk Laragon/WAMP) di server web lokal Anda.

### 4. Jalankan Aplikasi
* Buka browser web Anda dan akses URL proyek Anda. Contoh: `http://localhost/nama_folder_proyek_anda/public` atau `http://nama_proyek.test` jika menggunakan Laragon/Valet.
