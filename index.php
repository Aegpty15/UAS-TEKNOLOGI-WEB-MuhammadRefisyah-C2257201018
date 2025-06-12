<?php
session_start(); 

include 'config.php';

require_once 'vendor/dompdf/autoload.inc.php';

use Dompdf\Dompdf;
use Dompdf\Options;

function sanitize_input($data) {
    global $conn;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    $data = $conn->real_escape_string($data);
    return $data;
}

$page = isset($_GET['page']) ? $_GET['page'] : 'daftar';
$action = isset($_GET['action']) ? $_GET['action'] : '';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0; 
$jenis = isset($_GET['jenis']) ? sanitize_input($_GET['jenis']) : '';

$message = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}
$message_type = '';
if (isset($_SESSION['message_type'])) {
    $message_type = $_SESSION['message_type'];
    unset($_SESSION['message_type']);
}

if ($action == 'hapus' && $id > 0 && !empty($jenis)) {
    $tableName = ($jenis == 'pemasukan') ? 'pemasukan' : 'pengeluaran';

    $sql_select_bukti = "SELECT bukti FROM $tableName WHERE id = ?";
    $stmt_select_bukti = $conn->prepare($sql_select_bukti);
    $stmt_select_bukti->bind_param("i", $id);
    $stmt_select_bukti->execute();
    $result_bukti = $stmt_select_bukti->get_result();
    $bukti_to_delete = null;
    if ($result_bukti->num_rows > 0) {
        $bukti_to_delete = $result_bukti->fetch_assoc()['bukti'];
    }
    $stmt_select_bukti->close();

    $sql = "DELETE FROM $tableName WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        if (!empty($bukti_to_delete) && file_exists('uploads/bukti/' . $bukti_to_delete)) {
            unlink('uploads/bukti/' . $bukti_to_delete);
        }
        $_SESSION['message'] = "Data $jenis berhasil dihapus.";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Gagal menghapus data $jenis: " . $stmt->error;
        $_SESSION['message_type'] = "danger";
    }
    $stmt->close();
    header("Location: index.php?page=daftar");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && (isset($_POST['simpan_transaksi']) || isset($_POST['update_transaksi']))) {
    $tipe_transaksi_form = sanitize_input($_POST['tipe_transaksi']);
    $deskripsi = sanitize_input($_POST['deskripsi']);
    $jumlah = (int)sanitize_input($_POST['jumlah']);
    $tanggal = sanitize_input($_POST['tanggal']);
    $bukti = null;

    $tableName = ($tipe_transaksi_form == 'pemasukan') ? 'pemasukan' : 'pengeluaran';

    $is_update = isset($_POST['update_transaksi']);
    $transaksi_id = $is_update ? (int)sanitize_input($_POST['id']) : 0;
    $old_bukti = $is_update ? sanitize_input($_POST['old_bukti']) : null;

    if (empty($tipe_transaksi_form) || empty($deskripsi) || $jumlah <= 0 || empty($tanggal)) {
        $_SESSION['message'] = "Semua kolom wajib diisi (kecuali bukti) dan jumlah harus lebih dari 0.";
        $_SESSION['message_type'] = "danger";
        header("Location: " . ($is_update ? "index.php?page=edit&id=$transaksi_id&jenis=$tipe_transaksi_form" : "index.php?page=tambah"));
        exit();
    }

    if (isset($_FILES['bukti']) && $_FILES['bukti']['error'] == UPLOAD_ERR_OK) {
        $file_tmp_name = $_FILES['bukti']['tmp_name'];
        $file_name = basename($_FILES['bukti']['name']);
        $file_type = $_FILES['bukti']['type'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        $allowed_ext = ['jpg', 'jpeg'];
        $allowed_type = ['image/jpeg'];

        if (!in_array($file_ext, $allowed_ext) || !in_array($file_type, $allowed_type)) {
            $_SESSION['message'] = "File bukti harus berupa gambar .jpg/.jpeg.";
            $_SESSION['message_type'] = "danger";
            header("Location: " . ($is_update ? "index.php?page=edit&id=$transaksi_id&jenis=$tipe_transaksi_form" : "index.php?page=tambah"));
            exit();
        }

        $upload_dir = 'uploads/bukti/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $new_file_name = uniqid() . '.' . $file_ext;
        $destination = $upload_dir . $new_file_name;

        if (move_uploaded_file($file_tmp_name, $destination)) {
            $bukti = $new_file_name;
            if ($is_update && !empty($old_bukti) && file_exists($upload_dir . $old_bukti)) {
                unlink($upload_dir . $old_bukti);
            }
        } else {
            $_SESSION['message'] = "Gagal mengunggah file bukti.";
            $_SESSION['message_type'] = "danger";
            header("Location: " . ($is_update ? "index.php?page=edit&id=$transaksi_id&jenis=$tipe_transaksi_form" : "index.php?page=tambah"));
            exit();
        }
    } else {
        if ($is_update) {
            $bukti = $old_bukti;
        }
    }

    if ($is_update) {
        $sql = "UPDATE $tableName SET deskripsi=?, jumlah=?, tanggal=?, bukti=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sissi", $deskripsi, $jumlah, $tanggal, $bukti, $transaksi_id);
    } else {
        $sql = "INSERT INTO $tableName (deskripsi, jumlah, tanggal, bukti) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("siss", $deskripsi, $jumlah, $tanggal, $bukti);
    }

    if ($stmt->execute()) {
        $_SESSION['message'] = "Data " . ucfirst($tipe_transaksi_form) . " berhasil " . ($is_update ? "diperbarui" : "ditambahkan") . ".";
        $_SESSION['message_type'] = "success";
        header("Location: index.php?page=daftar");
        exit();
    } else {
        $_SESSION['message'] = "Gagal " . ($is_update ? "memperbarui" : "menambahkan") . " data " . ucfirst($tipe_transaksi_form) . ": " . $stmt->error;
        $_SESSION['message_type'] = "danger";
        header("Location: " . ($is_update ? "index.php?page=edit&id=$transaksi_id&jenis=$tipe_transaksi_form" : "index.php?page=tambah"));
        exit();
    }
    $stmt->close();
}

if ($page == 'cetak_pdf') {
    $sql_pemasukan_pdf = "SELECT id, deskripsi, jumlah, tanggal, bukti FROM pemasukan ORDER BY tanggal ASC";
    $result_pemasukan_pdf = $conn->query($sql_pemasukan_pdf);
    $total_pemasukan_pdf = 0;
    $transaksi_pemasukan_pdf = [];
    if ($result_pemasukan_pdf->num_rows > 0) {
        while($row = $result_pemasukan_pdf->fetch_assoc()) {
            $transaksi_pemasukan_pdf[] = $row;
            $total_pemasukan_pdf += $row['jumlah'];
        }
    }

    $sql_pengeluaran_pdf = "SELECT id, deskripsi, jumlah, tanggal, bukti FROM pengeluaran ORDER BY tanggal ASC";
    $result_pengeluaran_pdf = $conn->query($sql_pengeluaran_pdf);
    $total_pengeluaran_pdf = 0;
    $transaksi_pengeluaran_pdf = [];
    if ($result_pengeluaran_pdf->num_rows > 0) {
        while($row = $result_pengeluaran_pdf->fetch_assoc()) {
            $transaksi_pengeluaran_pdf[] = $row;
            $total_pengeluaran_pdf += $row['jumlah'];
        }
    }

    $conn->close();

    $html = '
    <style>
        body { font-family: sans-serif; font-size: 10pt; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table, th, td { border: 1px solid #000; padding: 5px; text-align: center; }
        th { background-color: #007bff; color: white; }
        h1 { text-align: center; padding-bottom: 10px; border-bottom: 1px solid #000; margin-bottom: 30px; }
        .detail-heading { background-color: #e0ffe0; font-weight: bold; text-align: center; }
        .detail-heading.pengeluaran { background-color: #ffe0e0; }
        img.bukti-img { width: 80px; height: auto; }
        .text-muted { color: #6c757d; font-size: 8pt; }
    </style>

    <h1>Laporan Duit Bulanan</h1>

    <table>
        <thead>
            <tr>
                <th>No.</th>
                <th>Jenis</th>
                <th>Deskripsi</th>
                <th>Jumlah</th>
                <th>Tanggal</th>
                <th>Bukti</th>
            </tr>
        </thead>
        <tbody>
            <tr><td colspan="6" class="detail-heading">Detail Pemasukan</td></tr>';

    if (!empty($transaksi_pemasukan_pdf)) {
        $no_pemasukan_pdf = 1;
        foreach ($transaksi_pemasukan_pdf as $d) {
            $html .= '<tr>
                <td>' . $no_pemasukan_pdf++ . '</td>
                <td>Pemasukan</td>
                <td>' . htmlspecialchars($d['deskripsi']) . '</td>
                <td>' . number_format($d['jumlah'], 0, ',', '.') . '</td>
                <td>' . $d['tanggal'] . '</td>
                <td>';
            if (!empty($d['bukti'])) {
                $image_path = realpath('uploads/bukti/' . $d['bukti']);
                if (file_exists($image_path)) {
                    $type = pathinfo($image_path, PATHINFO_EXTENSION);
                    $data = file_get_contents($image_path);
                    $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                    $html .= '<img src="' . $base64 . '" alt="Bukti" class="bukti-img">';
                } else {
                    $html .= '<span class="text-muted">File tidak ditemukan</span>';
                }
            } else {
                $html .= '<span class="text-muted">Tidak ada</span>';
            }
            $html .= '</td></tr>';
        }
    } else {
        $html .= '<tr><td colspan="6" style="text-align: center;">Belum ada data pemasukan.</td></tr>';
    }

    $html .= '
            <tr><td colspan="6" class="detail-heading pengeluaran">Detail Pengeluaran</td></tr>';

    if (!empty($transaksi_pengeluaran_pdf)) {
        $no_pengeluaran_pdf = 1;
        foreach ($transaksi_pengeluaran_pdf as $d) {
            $html .= '<tr>
                <td>' . $no_pengeluaran_pdf++ . '</td>
                <td>Pengeluaran</td>
                <td>' . htmlspecialchars($d['deskripsi']) . '</td>
                <td>' . number_format($d['jumlah'], 0, ',', '.') . '</td>
                <td>' . $d['tanggal'] . '</td>
                <td>';
            if (!empty($d['bukti'])) {
                $image_path = realpath('uploads/bukti/' . $d['bukti']);
                if (file_exists($image_path)) {
                    $type = pathinfo($image_path, PATHINFO_EXTENSION);
                    $data = file_get_contents($image_path);
                    $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                    $html .= '<img src="' . $base64 . '" alt="Bukti" class="bukti-img">';
                } else {
                    $html .= '<span class="text-muted">File tidak ditemukan</span>';
                }
            } else {
                $html .= '<span class="text-muted">Tidak ada</span>';
            }
            $html .= '</td></tr>';
        }
    } else {
        $html .= '<tr><td colspan="6" style="text-align: center;">Belum ada data pengeluaran.</td></tr>';
    }

    $html .= '
            <tr>
                <th colspan="3">Total Pemasukan</th>
                <td colspan="3">Rp ' . number_format($total_pemasukan_pdf, 0, ',', '.') . '</td>
            </tr>
            <tr>
                <th colspan="3">Total Pengeluaran</th>
                <td colspan="3">Rp ' . number_format($total_pengeluaran_pdf, 0, ',', '.') . '</td>
            </tr>
            <tr>
                <th colspan="3">Saldo Akhir</th>
                <td colspan="3">Rp ' . number_format($total_pemasukan_pdf - $total_pengeluaran_pdf, 0, ',', '.') . '</td>
            </tr>
        </tbody>
    </table>';

    $options = new Options();
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isRemoteEnabled', true);
    $options->set('defaultFont', 'Arial');

    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);

    $dompdf->setPaper('A4', 'portrait');

    $dompdf->render();

    $dompdf->stream("Laporan_Uang_Bulanan.pdf", array("Attachment" => 0));
    exit(); 
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aplikasi Catatan Uang Sederhana</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="wrapper">
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark"> <div class="container-fluid">
                <a class="navbar-brand" href="index.php?page=dashboard">Catatan Uang Bulanan</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                    <div class="navbar-nav me-auto"> <a class="nav-link <?= ($page == 'dashboard' ? 'active' : '') ?>" aria-current="page" href="index.php?page=dashboard">Dashboard</a>
                        <a class="nav-link <?= ($page == 'daftar' || $page == 'tambah' || $page == 'edit' ? 'active' : '') ?>" href="index.php?page=daftar">Daftar Transaksi</a>
                        <a class="nav-link <?= ($page == 'laporan' ? 'active' : '') ?>" href="index.php?page=laporan">Laporan Keuangan</a>
                    </div>
                    <div class="navbar-nav"> <a class="btn btn-success me-2" href="index.php?page=tambah">+ Tambah Transaksi</a> <a class="btn btn-secondary" href="index.php?page=cetak_pdf" target="_blank">Cetak PDF</a> </div>
                </div>
            </div>
        </nav>

        <div class="container">
            <?php if (!empty($message)): ?>
                <div class="alert alert-<?= $message_type ?>">
                    <?= $message ?>
                </div>
            <?php endif; ?>

            <?php
            switch ($page) {
                case 'dashboard':
                    ?>
                    <h2 class="text-center mt-5"><strong>Selamat Datang</strong></h2>
                    <p class="lead text-center">Di Aplikasi Pencatatan Pemakaian Uang Bulanan</p>
                    <hr class="my-4">
                    <p class="text-center">Dibuat oleh Muhammad Refisyah</p>
                    <?php
                    break;

                case 'daftar':
                    $data_pemasukan = [];
                    $sql_pemasukan = "SELECT id, 'pemasukan' as tipe_transaksi, deskripsi, jumlah, tanggal, bukti FROM pemasukan";
                    $result_pemasukan = $conn->query($sql_pemasukan);
                    if ($result_pemasukan->num_rows > 0) {
                        while($row = $result_pemasukan->fetch_assoc()) {
                            $data_pemasukan[] = $row;
                        }
                    }

                    $data_pengeluaran = [];
                    $sql_pengeluaran = "SELECT id, 'pengeluaran' as tipe_transaksi, deskripsi, jumlah, tanggal, bukti FROM pengeluaran";
                    $result_pengeluaran = $conn->query($sql_pengeluaran);
                    if ($result_pengeluaran->num_rows > 0) {
                        while($row = $result_pengeluaran->fetch_assoc()) {
                            $data_pengeluaran[] = $row;
                        }
                    }

                    $all_transactions = array_merge($data_pemasukan, $data_pengeluaran);
                    usort($all_transactions, function($a, $b) {
                        $tanggal_a = strtotime($a['tanggal']);
                        $tanggal_b = strtotime($b['tanggal']);
                        if ($tanggal_a == $tanggal_b) {
                            return $b['id'] - $a['id'];
                        }
                        return $tanggal_b - $tanggal_a;
                    });

                    ?>
                    <h2 class="mb-4">Daftar Transaksi</h2>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Jenis</th>
                                    <th>Deskripsi</th>
                                    <th>Jumlah</th>
                                    <th>Tanggal</th>
                                    <th>Bukti</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($all_transactions)): ?>
                                    <?php foreach($all_transactions as $row): ?>
                                        <tr>
                                            <td><?= $row['id'] ?></td>
                                            <td><?= ucfirst($row['tipe_transaksi']) ?></td>
                                            <td><?= $row['deskripsi'] ?></td>
                                            <td><?= number_format($row['jumlah'], 0, ',', '.') ?></td>
                                            <td><?= $row['tanggal'] ?></td>
                                            <td>
                                                <?php if (!empty($row['bukti'])): ?>
                                                    <img src="uploads/bukti/<?= $row['bukti'] ?>" alt="Bukti" width="80" class="img-thumbnail">
                                                <?php else: ?>
                                                    <span class="text-muted">Tidak ada</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="index.php?page=edit&id=<?= $row['id'] ?>&jenis=<?= $row['tipe_transaksi'] ?>" class="btn btn-primary btn-sm me-1">Edit</a>
                                                <a href="index.php?action=hapus&id=<?= $row['id'] ?>&jenis=<?= $row['tipe_transaksi'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="7" class="text-center">Belum ada data transaksi.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php
                    break;

                case 'tambah':
                    ?>
                    <h2 class="mb-4">Tambah Transaksi</h2>
                    <form action="index.php" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="tipe_transaksi" class="form-label">Jenis Transaksi:</label>
                            <select name="tipe_transaksi" id="tipe_transaksi" class="form-select" required>
                                <option value="">Pilih Jenis</option>
                                <option value="pemasukan" <?= (isset($_POST['tipe_transaksi']) && $_POST['tipe_transaksi'] == 'pemasukan' ? 'selected' : '') ?>>Pemasukan</option>
                                <option value="pengeluaran" <?= (isset($_POST['tipe_transaksi']) && $_POST['tipe_transaksi'] == 'pengeluaran' ? 'selected' : '') ?>>Pengeluaran</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="jumlah" class="form-label">Jumlah:</label>
                            <input type="number" name="jumlah" id="jumlah" class="form-control" placeholder="Jumlah" required value="<?= isset($_POST['jumlah']) ? $_POST['jumlah'] : '' ?>">
                        </div>

                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">Deskripsi:</label>
                            <input type="text" name="deskripsi" id="deskripsi" class="form-control" placeholder="Deskripsi" required value="<?= isset($_POST['deskripsi']) ? $_POST['deskripsi'] : '' ?>">
                        </div>

                        <div class="mb-3">
                            <label for="tanggal" class="form-label">Tanggal:</label>
                            <input type="date" name="tanggal" id="tanggal" class="form-control" required value="<?= isset($_POST['tanggal']) ? $_POST['tanggal'] : '' ?>">
                        </div>

                        <div class="mb-3">
                            <label for="bukti" class="form-label">Bukti (Opsional, JPG/JPEG):</label>
                            <input type="file" name="bukti" id="bukti" class="form-control" accept="image/jpeg">
                            <small class="form-text text-muted">Hanya file dengan ekstensi .jpg atau .jpeg yang diperbolehkan.</small>
                        </div>

                        <button type="submit" name="simpan_transaksi" class="btn btn-primary">Simpan Transaksi</button>
                    </form>
                    <?php
                    break;

                case 'edit':
                    $transaksi_data = null;
                    if ($id > 0 && !empty($jenis)) {
                        $tableName = ($jenis == 'pemasukan') ? 'pemasukan' : 'pengeluaran';
                        $sql = "SELECT id, deskripsi, jumlah, tanggal, bukti FROM $tableName WHERE id = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("i", $id);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        if ($result->num_rows > 0) {
                            $transaksi_data = $result->fetch_assoc();
                            $transaksi_data['tipe_transaksi'] = $jenis;
                        }
                        $stmt->close();
                    }

                    if (!$transaksi_data): ?>
                        <div class="alert alert-danger">Data tidak ditemukan atau jenis tidak valid.</div>
                    <?php else: ?>
                        <h2 class="mb-4">Edit Transaksi (ID: <?= $transaksi_data['id'] ?>)</h2>
                        <form action="index.php" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="id" value="<?= $transaksi_data['id'] ?>">
                            <input type="hidden" name="old_bukti" value="<?= $transaksi_data['bukti'] ?>">
                            <input type="hidden" name="tipe_transaksi" value="<?= $transaksi_data['tipe_transaksi'] ?>">

                            <div class="mb-3">
                                <label for="tipe_transaksi_display" class="form-label">Jenis Transaksi:</label>
                                <input type="text" id="tipe_transaksi_display" class="form-control" value="<?= ucfirst($transaksi_data['tipe_transaksi']) ?>" readonly>
                            </div>

                            <div class="mb-3">
                                <label for="jumlah" class="form-label">Jumlah:</label>
                                <input type="number" name="jumlah" id="jumlah" class="form-control" placeholder="Jumlah" required value="<?= $transaksi_data['jumlah'] ?>">
                            </div>

                            <div class="mb-3">
                                <label for="deskripsi" class="form-label">Deskripsi:</label>
                                <input type="text" name="deskripsi" id="deskripsi" class="form-control" placeholder="Deskripsi" required value="<?= $transaksi_data['deskripsi'] ?>">
                            </div>

                            <div class="mb-3">
                                <label for="tanggal" class="form-label">Tanggal:</label>
                                <input type="date" name="tanggal" id="tanggal" class="form-control" required value="<?= $transaksi_data['tanggal'] ?>">
                            </div>

                            <div class="mb-3">
                                <label for="bukti" class="form-label">Ganti Bukti (Opsional, JPG/JPEG):</label>
                                <input type="file" name="bukti" id="bukti" class="form-control" accept="image/jpeg">
                                <small class="form-text text-muted">Hanya file dengan ekstensi .jpg atau .jpeg yang diperbolehkan.</small>
                                <?php if (!empty($transaksi_data['bukti'])): ?>
                                    <div class="mt-2 current-bukti">
                                        <p>Bukti Saat Ini:</p>
                                        <img src="uploads/bukti/<?= $transaksi_data['bukti'] ?>" alt="Bukti Saat Ini" width="100" class="img-thumbnail">
                                    </div>
                                <?php endif; ?>
                            </div>

                            <button type="submit" name="update_transaksi" class="btn btn-primary">Update Transaksi</button>
                        </form>
                    <?php
                    endif;
                    break;

                case 'laporan':
                    $sql_pemasukan_laporan = "SELECT id, deskripsi, jumlah, tanggal, bukti FROM pemasukan ORDER BY tanggal ASC";
                    $result_pemasukan_laporan = $conn->query($sql_pemasukan_laporan);
                    $total_pemasukan = 0;
                    $transaksi_pemasukan_laporan = [];
                    if ($result_pemasukan_laporan->num_rows > 0) {
                        while($row = $result_pemasukan_laporan->fetch_assoc()) {
                            $transaksi_pemasukan_laporan[] = $row;
                            $total_pemasukan += $row['jumlah'];
                        }
                    }
                    $sql_pengeluaran_laporan = "SELECT id, deskripsi, jumlah, tanggal, bukti FROM pengeluaran ORDER BY tanggal ASC";
                    $result_pengeluaran_laporan = $conn->query($sql_pengeluaran_laporan);
                    $total_pengeluaran = 0;
                    $transaksi_pengeluaran_laporan = [];
                    if ($result_pengeluaran_laporan->num_rows > 0) {
                        while($row = $result_pengeluaran_laporan->fetch_assoc()) {
                            $transaksi_pengeluaran_laporan[] = $row;
                            $total_pengeluaran += $row['jumlah'];
                        }
                    }
                    
                    ?>
                    <h2 class="mb-4">Laporan Duit Bulanan</h2>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Jenis</th>
                                    <th>Deskripsi</th>
                                    <th>Jumlah</th>
                                    <th>Tanggal</th>
                                    <th>Bukti</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr><td colspan="6" class="table-success fw-bold text-center">Detail Pemasukan</td></tr>
                                <?php $no_pemasukan = 1; if (!empty($transaksi_pemasukan_laporan)): ?>
                                    <?php foreach ($transaksi_pemasukan_laporan as $d): ?>
                                        <tr>
                                            <td><?= $no_pemasukan++ ?></td>
                                            <td>Pemasukan</td>
                                            <td><?= $d['deskripsi'] ?></td>
                                            <td><?= number_format($d['jumlah'], 0, ',', '.') ?></td>
                                            <td><?= $d['tanggal'] ?></td>
                                            <td>
                                                <?php if (!empty($d['bukti'])): ?>
                                                    <img src="uploads/bukti/<?= $d['bukti'] ?>" alt="Bukti" width="80" class="img-thumbnail">
                                                <?php else: ?>
                                                    <span class="text-muted">Tidak ada</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="6" class="text-center">Belum ada data pemasukan.</td></tr>
                                <?php endif; ?>

                                <tr><td colspan="6" class="table-danger fw-bold text-center mt-3">Detail Pengeluaran</td></tr>
                                <?php $no_pengeluaran = 1; if (!empty($transaksi_pengeluaran_laporan)): ?>
                                    <?php foreach ($transaksi_pengeluaran_laporan as $d): ?>
                                        <tr>
                                            <td><?= $no_pengeluaran++ ?></td>
                                            <td>Pengeluaran</td>
                                            <td><?= $d['deskripsi'] ?></td>
                                            <td><?= number_format($d['jumlah'], 0, ',', '.') ?></td>
                                            <td><?= $d['tanggal'] ?></td>
                                            <td>
                                                <?php if (!empty($d['bukti'])): ?>
                                                    <img src="uploads/bukti/<?= $d['bukti'] ?>" alt="Bukti" width="80" class="img-thumbnail">
                                                <?php else: ?>
                                                    <span class="text-muted">Tidak ada</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="6" class="text-center">Belum ada data pengeluaran.</td></tr>
                                <?php endif; ?>

                                <tr>
                                    <th colspan="3" class="table-primary">Total Pemasukan</th>
                                    <td colspan="3" class="fw-bold">Rp <?= number_format($total_pemasukan, 0, ',', '.') ?></td>
                                </tr>
                                <tr>
                                    <th colspan="3" class="table-primary">Total Pengeluaran</th>
                                    <td colspan="3" class="fw-bold">Rp <?= number_format($total_pengeluaran, 0, ',', '.') ?></td>
                                </tr>
                                <tr>
                                    <th colspan="3" class="table-primary">Saldo Akhir</th>
                                    <td colspan="3" class="fw-bold">Rp <?= number_format($total_pemasukan - $total_pengeluaran, 0, ',', '.') ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <?php
                    break;
                
                default:
                    echo '<h2>Selamat Datang</h2>';
                    echo '<p>Di Aplikasi Pencatatan Pemakaian Uang Bulanan</p>';
                    echo '<p>Dibuat oleh <strong>M.Refisyah</strong></p>';
                    break;
            }
            if ($page != 'cetak_pdf') {
                $conn->close();
            }
            ?>
        </body>
        </html>