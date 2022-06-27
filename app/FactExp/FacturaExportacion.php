<?php

namespace App\FactExp;

use Illuminate\Database\Eloquent\Model;
use Mpdf\Mpdf;
use Luecano\NumeroALetras\NumeroALetras;
use App\Common;

class FacturaExportacion extends Model
{
    public static function factura_pdf($dato){
        
        $formatter = new NumeroALetras;

        /*  --------------------------------------------------------------------------------- */

        // CARGAR VARIABLES
    
        $cabecera = $dato["cabecera"];
        $cuerpo = $dato["cuerpo"];

        $c = 0;
        $c_rows = 0;
        $c_rows_array = count($cuerpo);
        $c_filas_total = count($cuerpo);
        $total = 0;
        $switch_hojas = false;

        /*  --------------------------------------------------------------------------------- */

        // CARGAR CABECERA DE DATOS 
        
        $data['fecha'] = date("d-m-Y", strtotime($cabecera["SelectedFecha"]));
        $data['senores'] = substr($cabecera["senores"], 0,60);
        $data['pais'] = substr($cabecera["pais"], 0,21);
        $data['ciudad'] = substr($cabecera["ciudad"], 0,23);
        $data['direccion'] = substr($cabecera["direccion"], 0,60);
        $data['telefono'] = substr($cabecera["telefono"], 0,24);
        $data['condiciones_p'] = substr($cabecera["condiciones_p"], 0,36);
        $data['tipo'] = 'fisico';

        /*  --------------------------------------------------------------------------------- */

        // CREAR HOJA

        $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];
         
        $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        $mpdf = new Mpdf([
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 20,
            'margin_header' => 10
        ]);

        $mpdf->SetDisplayMode('fullpage');

        foreach ($cuerpo as $key => $value) {
          
            /*  --------------------------------------------------------------------------------- */
            
            // CARGAR ITEM

            $articulos[$c_rows]["item"] = $value["ITEM"];
            $articulos[$c_rows]["descripcion"] = utf8_decode(utf8_encode(substr($value["DESCRIPCION"], 0,60)));
            $articulos[$c_rows]["precio"] = $value["PRECIO"];
            $articulos[$c_rows]["total"] = $value["TOTAL"];
            $articulos[$c_rows]["cantidad"] = $value["CANTIDAD"];
            $total = $total + Common::quitar_coma($value["TOTAL"], 2);

            /*  --------------------------------------------------------------------------------- */
            
            // CONTAR CANTIDAD DE FILAS DE HOJAS 

            $c_rows = $c_rows + 1;    
            
            /*  --------------------------------------------------------------------------------- */

            // CONTAR LA CANTIDAD DE FILAS 

            $c = $c + 1;

            /*  --------------------------------------------------------------------------------- */


            // SI CANTIDAD DE FILAS ES IGUAL A 38 ENTONCES CREAR PAGINA 

            if ($c_rows === 38){

                // AGREGAR ARTICULOS 

                $data['articulos'] = $articulos;

                // RESTAR LAS CANTIDADES CARGADAS 

                $c_rows_array = $c_rows_array - 38;

                // PONER TRUE SWITCH YA QUE CREO UNA PAGINA 

                $switch_hojas = true;

                // CARGAR SUB TOTALES POR HOJA

                $data['letra'] = 'Son Dolares: '.($formatter->toMoney($total, 2, 'dolares'));
                $data['total'] = Common::precio_candec_sin_letra($total, 2);

                $html = view('pdf.facturaExportacion', $data)->render();
                
                /*  --------------------------------------------------------------------------------- */

                // SI NO ES LA PRIMERA HOJA AGREGAR PAGINA

                if ($c !== 38) {
                    $mpdf->AddPage();
                }

                /*  --------------------------------------------------------------------------------- */

                // CERAR CONTADOR DE FILAS CARGADAS POR HOJAS Y ARTICULOS

                $c_rows = 0;
                $total = 0;
                $data['articulos'] = [];
                $articulos = [];
                    
                $mpdf->WriteHTML($html);

                /*  --------------------------------------------------------------------------------- */

            }  else if ($c_rows_array < 38 && $c_filas_total === $c) {
                
                // AGREGAR ARTICULOS 
                
                $data['articulos'] = $articulos;

                // CARGAR SUB TOTALES POR HOJA

                $data['letra'] = 'Son Dolares: '.($formatter->toMoney($total, 2, 'dolares'));
                $data['total'] = Common::precio_candec_sin_letra($total, 2);

                /*  --------------------------------------------------------------------------------- */

                // CREAR HOJA 

                $html = view('pdf.facturaExportacion', $data)->render();

                /*  --------------------------------------------------------------------------------- */

                // SI NO ES LA PRIMERA HOJA AGREGAR PAGINA

                if ($switch_hojas === true) {
                    $mpdf->AddPage();
                }

                /*  --------------------------------------------------------------------------------- */
                    
                $mpdf->WriteHTML($html);
            }
        }
        
        /*  --------------------------------------------------------------------------------- */
        
        // DESCARGAR ARCHIVO PDF 

        $mpdf->Output();
    }
}
