<?php $this->extend('layout') ?>
<?= $this->section('konten') ?>

<div class="container-fluid">
    <div class="d-flex justify-content-between mb-2">
        <h1 class="h3">Laporan Pemakaian Uang Bulanan</h1>
        <h1 class="h3">
            <a href="<?= base_url('duit/tambah') ?>" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i></a>
        </h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success">
                    <?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>
            <h6 class="m-0 font-weight-bold text-primary">Catatan Duit Yang Terpakai</h6>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
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
                    <?php foreach ($duit as $d): ?>
                    <tr>
                        <td><?= $d['id'] ?></td> <td><?= ucfirst($d['tipe_transaksi']) ?></td> <td><?= $d['deskripsi'] ?></td> <td><?= number_format($d['jumlah'], 0, ',', '.') ?></td> <td><?= $d['tanggal'] ?></td> <td>
                        <?php if (!empty($d['bukti'])): ?> <img src="<?= base_url('uploads/bukti/' . $d['bukti']) ?>" width="100" alt="Bukti"> <?php else: ?>
                            <span class="text-muted">Tidak ada bukti</span>
                        <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?= base_url('duit/edit/' . $d['id']) ?>" class="btn btn-primary btn-sm"> <i class="fas fa-edit"></i>
                            </a>
                            <a href="<?= base_url('duit/hapus/' . $d['id']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Anda yakin akan menghapus data ini?')"> <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>