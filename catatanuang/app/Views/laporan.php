<style>
    table { width: 100%; border-collapse: collapse; }
    table, th, td {
        border: 1px solid #000;
        padding: 5px;
        text-align: center;
    }
    h1 {
        text-align: center;
        padding-bottom: 10px;
        border-bottom: 1px solid #000;
        margin-bottom: 30px;
    }
    img.bukti-img {
        width: 100px;
        height: auto;
    }
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
        <?php
        $no = 1;
        $total_pemasukan = 0;
        $total_pengeluaran = 0;
        foreach ($uang as $d):
            // Menggunakan $d['tipe_transaksi'] untuk perhitungan
            if ($d['tipe_transaksi'] == 'pemasukan') { // UBAH DI SINI
                $total_pemasukan += $d['jumlah']; // UBAH DI SINI
            } else {
                $total_pengeluaran += $d['jumlah']; // UBAH DI SINI
            }
        ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= ucfirst($d['tipe_transaksi']) ?></td> <td style="text-align: left;"><?= $d['deskripsi'] ?></td> <td><?= number_format($d['jumlah'], 0, ',', '.') ?></td> <td><?= $d['tanggal'] ?></td> <td>
               <?php if (!empty($d['bukti'])): ?> <img src="<?php echo base_url() . 'uploads/bukti/' . $d['bukti']; ?>" height="100" class="bukti-img"> <?php else: ?>
                   <span class="text-muted">Tidak ada bukti</span>
               <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
        <tr>
            <th colspan="3">Total Pemasukan</th>
            <td colspan="3">Rp <?= number_format($total_pemasukan, 0, ',', '.') ?></td>
        </tr>
        <tr>
            <th colspan="3">Total Pengeluaran</th>
            <td colspan="3">Rp <?= number_format($total_pengeluaran, 0, ',', '.') ?></td>
        </tr>
        <tr>
            <th colspan="3">Saldo Akhir</th>
            <td colspan="3">Rp <?= number_format($total_pemasukan - $total_pengeluaran, 0, ',', '.') ?></td>
        </tr>
    </tbody>
</table>