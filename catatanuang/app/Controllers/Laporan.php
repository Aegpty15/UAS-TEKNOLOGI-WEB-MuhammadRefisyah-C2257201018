<?php

namespace App\Controllers;
use App\Libraries\Cetakpdf; // Pastikan library ini ada dan berfungsi

class Laporan extends BaseController {
    protected $dml;

    public function __construct() {
        $this->dml = model('App\Models\DML');
    }

    public function tampil() {
        // Ambil semua data transaksi untuk laporan
        // Anda bisa menambahkan filter tanggal di sini jika diperlukan
        $data['uang'] = $this->dml->dataRead('transaksi');
        return view('laporan', $data);
    }

    public function pdf() {
        // Ambil semua data transaksi untuk PDF
        $data['uang'] = $this->dml->dataRead('transaksi');

        $cetak_pdf = new Cetakpdf();
        $nama_file = 'Laporan-Uang';
        $kertas = 'A4';
        $orientasi = 'portrait';
        // Pastikan view 'laporan' bisa menampilkan data dari tabel 'transaksi'
        $html = view('laporan', $data);

        $cetak_pdf->buatPDF($html, $nama_file, $kertas, $orientasi);
    }
}