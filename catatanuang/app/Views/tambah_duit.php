<?php
echo $this->extend('layout');
echo $this->section('konten');
?>
<div class="container-fluid">

    <div class="d-flex justify-content-between mb-2">
        <h1 class="h3">Pengisian Data Uang Bulanan Yang Terpakai</h1>
        <h1 class="h3">
            <a href="<?= base_url('duit/daftar') ?>" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i></a>
        </h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-danger">Semua kolom wajib diisi, dan bukti wajib dalam format .jpg!</h6>
        </div>
        <div class="card-body">

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger">
                    <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <?= form_open_multipart('duit/simpan', ['class' => 'user']) ?>
                <div class="form-group row">
                    <div class="col-md-4">
                        <select name="tipe_transaksi" class="form-control form-control-lg" required>
                            <option value="">-- Pilih Jenis --</option>
                            <option value="pemasukan">Pemasukan</option>
                            <option value="pengeluaran">Pengeluaran</option>
                        </select>
                    </div>

                    <div class="col-sm-6">
                        <input type="number" name="jumlah" class="form-control form-control-lg" placeholder="Jumlah" required>
                    </div>
                </div>
                <div class="form-group">
                    <input type="text" name="deskripsi" class="form-control form-control-lg" placeholder="Deskripsi" required>
                </div>

                <div class="form-group">
                    <input type="date" name="tanggal" class="form-control form-control-lg" required>
                </div>

                <div class="form-group">
                    <input type="file" name="bukti" class="form-control form-control-lg" accept="image/jpeg">
                    <small class="text-muted">Hanya file dengan ekstensi <strong>.jpg</strong> yang diperbolehkan. (Opsional)</small>
                </div>

                <button type="submit" class="btn btn-outline-primary">Simpan</button>
            <?= form_close() ?>
        </div>
    </div>

</div>
<?= $this->endSection() ?>