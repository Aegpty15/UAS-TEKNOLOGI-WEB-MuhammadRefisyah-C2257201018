<?php
use CodeIgniter\CodeIgniter;

function jenis($jenis){
    if($jenis == 'pemasukan') {
        return "pemasukan";
    }
    else {
        return "pengeluaran";
    }
    
}

?>