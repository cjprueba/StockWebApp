<?php

namespace App\FactExp;

use Illuminate\Database\Eloquent\Model;
use Mpdf\Mpdf;
class FacturaExportacion extends Model
{
    public static function factura_pdf($dato){
        

       
       /* $formatter = new NumeroALetras;
      
    
        $c = 0;
        $c_rows = 0;
        $c_rows_array = count($ventas_det);
        $c_filas_total = count($ventas_det);
        


        $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];
         
        $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        $mpdf = new Mpdf([
            'fontDir' => array_merge($fontDirs, [
                public_path() . '/fonts',
            ]),
            'fontdata' => $fontData + [
                'arial' => [
                    'R' => 'arial.ttf',
                    'B' => 'arialbd.ttf',
                ],
            ],
            'default_font' => 'arial',
            "format" => [ 240, 148 ],
        ]);

        $mpdf->SetDisplayMode('fullpage');

 		$html = view('pdf.facturaVenta', $data)->render();
 		$mpdf->WriteHTML($html);*/

        
        /*  --------------------------------------------------------------------------------- */
        
        // DESCARGAR ARCHIVO PDF 

       /* $mpdf->Output($namefile,"D");*/
    }
}
