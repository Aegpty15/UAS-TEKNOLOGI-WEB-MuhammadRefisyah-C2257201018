<?php

namespace App\Controllers;

class Duit extends BaseController {
    protected $dml;

    public function __construct() {
        $this->dml = model('App\Models\DML'); // Pastikan model DML bisa menangani tabel 'transaksi'
    }

    public function daftar() {
        helper('duit'); // Pastikan helper 'duit' masih relevan atau sesuaikan jika perlu
        // Ambil semua data dari tabel 'transaksi'
        // Jika Anda ingin memfilter hanya pengeluaran atau pemasukan secara default,
        // Anda bisa menambahkan kondisi di sini.
        // Contoh: $data['duit'] = $this->dml->dataRead('transaksi', ['tipe_transaksi' => 'pengeluaran']);
        $data['duit'] = $this->dml->dataRead('transaksi');
        return view('daftar_duit', $data);
    }

    // Metode hapus sekarang akan menggunakan 'id' sebagai primary key
    public function hapus($id) {
        $this->dml->dataDelete('transaksi', ['id' => $id]);
        session()->setFlashdata('success', 'Data kas berhasil dihapus.');
        return redirect()->to('duit/daftar');
    }

    public function tambah() {
        helper(['form']);
        // Tidak perlu membaca semua data di fungsi tambah ini
        // $data['duit'] = $this->dml->dataRead('transaksi');
        return view('tambah_duit'); // View mungkin perlu disesuaikan untuk input 'tipe_transaksi'
    }

    public function simpan() {
        $file = $this->request->getFile('bukti');
        $originalName = null;

        if ($file && $file->isValid() && !$file->hasMoved()) {
            $mimeType = $file->getClientMimeType();
            $ext = strtolower($file->getClientExtension());

            if ($ext !== 'jpg' && $mimeType !== 'image/jpeg') { // Menggunakan && karena keduanya harus true
                session()->setFlashdata('error', 'File bukti harus berupa gambar .jpg');
                return redirect()->back()->withInput();
            }

            $originalName = $file->getName();
            $file->move(FCPATH . 'uploads/bukti', $originalName);
        }

        $data = [
            'tipe_transaksi' => $this->request->getVar('tipe_transaksi'), // Tambahkan input ini dari form
            'deskripsi'      => $this->request->getVar('deskripsi'),
            'jumlah'         => $this->request->getVar('jumlah'),
            'tanggal'        => $this->request->getVar('tanggal'),
            'bukti'          => $originalName
        ];

        // Validasi dasar (opsional, tapi sangat disarankan)
        if (empty($data['tipe_transaksi']) || empty($data['deskripsi']) || empty($data['jumlah']) || empty($data['tanggal'])) {
            session()->setFlashdata('error', 'Semua kolom wajib diisi (kecuali bukti).');
            return redirect()->back()->withInput();
        }

        $this->dml->dataInsert('transaksi', $data);
        session()->setFlashdata('success', 'Data berhasil ditambahkan.');

        return redirect()->to('duit/daftar');
    }

    // Metode edit sekarang akan menggunakan 'id' sebagai primary key
    public function edit($id) {
        helper('form');
        // Pastikan dataRead mengembalikan array dari object atau array asosiatif
        $data['duit'] = (object) $this->dml->dataRead('transaksi', ['id' => $id])[0];
        return view('edit_duit', $data);
    }

    // Metode update sekarang akan menggunakan 'id' sebagai primary key
    public function update() {
        $id = $this->request->getVar('id'); // Ambil ID dari form
        $file = $this->request->getFile('bukti');

        $originalName = null;
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $mimeType = $file->getClientMimeType();
            $ext = strtolower($file->getClientExtension());

            if ($ext !== 'jpg' && $mimeType !== 'image/jpeg') {
                session()->setFlashdata('error', 'File bukti harus berupa gambar .jpg');
                return redirect()->back()->withInput();
            }

            $originalName = $file->getName();
            $file->move(FCPATH . 'uploads/bukti', $originalName);
        }

        $data = [
            'tipe_transaksi' => $this->request->getVar('tipe_transaksi'), // Ambil dari form
            'deskripsi'      => $this->request->getVar('deskripsi'),
            'jumlah'         => $this->request->getVar('jumlah'),
            'tanggal'        => $this->request->getVar('tanggal'),
        ];

        if ($originalName) {
            $data['bukti'] = $originalName;
        }

        $this->dml->dataUpdate('transaksi', $data, ['id' => $id]);
        session()->setFlashdata('success', 'Data berhasil diperbarui.');

        return redirect()->to('duit/daftar');
    }
}