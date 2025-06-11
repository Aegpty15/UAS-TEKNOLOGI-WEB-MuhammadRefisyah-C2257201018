<?php
echo $this->extend('layout');
echo $this->section('konten');
?>
<div class="container-fluid">

    <div class="d-flex justify-content-between mb-2">
        <h1 class="h3">Edit Data Uang Bulanan</h1>
        <h1 class="h3">
            <a href="<?= base_url('duit/daftar') ?>" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i></a>
        </h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-danger">Semua kolom wajib diisi!</h6>
        </div>
        <div class="card-body">

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger">
                    <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <?= form_open_multipart('duit/update', ['class' => 'user']) ?>
                <div class="form-group row">
                    <div class="col-md-4">
                        <select name="tipe_transaksi" class="form-control form-control-lg" required>
                            <option value="">-- Pilih Jenis --</option>
                            <option value="pemasukan" <?= ($duit->tipe_transaksi == 'pemasukan') ? 'selected' : '' ?>>Pemasukan</option>
                            <option value="pengeluaran" <?= ($duit->tipe_transaksi == 'pengeluaran') ? 'selected' : '' ?>>Pengeluaran</option>
                        </select>
                    </div>
                    <div class="col-sm-6">
                        <input type="number" name="jumlah" class="form-control form-control-lg" value="<?= $duit->jumlah ?>" required>
                    </div>
                </div>
                <div class="form-group">
                    <input type="text" name="deskripsi" class="form-control form-control-lg" value="<?= $duit->deskripsi ?>" required>
                </div>

                <div class="form-group">
                    <input type="date" name="tanggal" class="form-control form-control-lg" value="<?= $duit->tanggal ?>" required>
                </div>

                <div class="form-group">
                    <label for="bukti">Ganti Bukti (Opsional, .jpg):</label>
                    <input type="file" name="bukti" class="form-control form-control-lg" accept="image/jpeg">
                    <?php if (!empty($duit->bukti)): ?>
                        <div class="mt-2">
                            <img src="<?= base_url('uploads/bukti/' . $duit->bukti) ?>" width="120" alt="Bukti Saat Ini">
                        </div>
                    <?php endif; ?>
                </div>

                <input type="hidden" name="id" value="<?= $duit->id ?>">

                <button type="submit" class="btn btn-outline-primary">Simpan</button>
            <?= form_close() ?>

        </div>
    </div>

</div>
<?= $this->endSection() ?>