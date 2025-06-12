<?php
require_once 'vendor/dompdf/autoload.inc.php';
include 'config.php'; 

use Dompdf\Dompdf;
use Dompdf\Options;

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

if (!empty($transaksi_pemasukan_laporan)) {
    $no_pemasukan = 1;
    foreach ($transaksi_pemasukan_laporan as $d) {
        $html .= '<tr>
            <td>' . $no_pemasukan++ . '</td>
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

if (!empty($transaksi_pengeluaran_laporan)) {
    $no_pengeluaran = 1;
    foreach ($transaksi_pengeluaran_laporan as $d) {
        $html .= '<tr>
            <td>' . $no_pengeluaran++ . '</td>
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
            <td colspan="3">Rp ' . number_format($total_pemasukan, 0, ',', '.') . '</td>
        </tr>
        <tr>
            <th colspan="3">Total Pengeluaran</th>
            <td colspan="3">Rp ' . number_format($total_pengeluaran, 0, ',', '.') . '</td>
        </tr>
        <tr>
            <th colspan="3">Saldo Akhir</th>
            <td colspan="3">Rp ' . number_format($total_pemasukan - $total_pengeluaran, 0, ',', '.') . '</td>
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
?>