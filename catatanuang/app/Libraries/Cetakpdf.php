<?php
namespace App\Libraries;
Use Dompdf\Dompdf;
Use Dompdf\Options;

class Cetakpdf {
    function buatPDF($tampilan, $nama_file='', $kertas='', $posisi='', $stream=true) {
        $options = new Options;
        $options->set('isRemoteEnabled', true);
        $options->set('isPhpEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($tampilan);
        $dompdf->setPaper($kertas, $posisi);
        $dompdf->render();

        if($stream) {
            $dompdf->stream($nama_file.'.pdf', array('Attachment'=>0));
            exit();
        }
        else {
            return $dompdf->output();
        }  
    }   
}