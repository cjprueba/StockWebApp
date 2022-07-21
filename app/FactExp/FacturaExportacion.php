<?php

namespace App\FactExp;

use Illuminate\Database\Eloquent\Model;
use Mpdf\Mpdf;
use Luecano\NumeroALetras\NumeroALetras;
use App\Common;
use Illuminate\Support\Facades\Log;
class FacturaExportacion extends Model
{
    public static function factura_pdf($dato){
        
        $formatter = new NumeroALetras;

        /*  --------------------------------------------------------------------------------- */

        // CARGAR VARIABLES
    
        $cabecera = $dato["cabecera"];
        $cuerpo = $dato["cuerpo"];
        $filas_usar=0;//CANTIDAD DE FILAS QUE UTILIZARA LA DESCRIPCION
        $filas=0;//FILAS AUXILIARES PARA CALCULO DE DESCRIPCION
        $lengt=0;// CANTIDAD DE CARACTERES DE LA VARIABLE DESCRIPCION
        $leng_max=60;// CANTIDAD DE CARACTERES MAXIMO POR FILA
        $inicio_desc=0;// INICIO DE LA DESCRIPCION
        $final_desc=$leng_max;// FINAL DE LA DESCRIPCION
        $c = 0;
        $c_rows = 0;
        $c_rows_array = count($cuerpo);
        $c_filas_total = count($cuerpo);
        $total = 0;
        $ult_art_cargado=true;
        $contador_articulos=0;
        $switch_hojas = false;
        

        /*  --------------------------------------------------------------------------------- */

        // CARGAR CABECERA DE DATOS 
        
        $data['fecha'] = date("d-m-Y", strtotime($cabecera["SelectedFecha"]));
        $data['senores'] = substr($cabecera["senores"], 0,60);
        $data['pais'] = substr($cabecera["pais"], 0,16);
        $data['ciudad'] = substr($cabecera["ciudad"], 0,18);
        $data['direccion'] = substr($cabecera["direccion"], 0,60);
        $data['telefono'] = substr($cabecera["telefono"], 0,24);
        $data['condiciones_p'] = substr($cabecera["condiciones_p"], 0,30);
        $data['tipo'] = 'fisico';

        /*  --------------------------------------------------------------------------------- */

        // CREAR HOJA

        $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];
         
        $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        $mpdf = new Mpdf([
            'margin_left' => 7,
            'margin_right' => 7,
            'margin_top' => 30,
            'margin_bottom' => 2,
            'margin_header' => 10,
            "format" => [ 210, 307 ],
        ]);

        $mpdf->SetDisplayMode('fullpage');

        foreach ($cuerpo as $key => $value) {
          
            /*  --------------------------------------------------------------------------------- */
            
            // CARGAR ITEM
            
            $descripcion = FacturaExportacion::quitar_tildes(utf8_decode(utf8_encode($value["DESCRIPCION"])));
           
           

            //TRAER CARACTERES DE DESCRIPCION
            $lengt=intval(strlen($descripcion));
            $filas_usar=FacturaExportacion::calcular_filas($lengt,$leng_max);
            $contador_articulos=$contador_articulos+1;
           //LOOP HASTA QUE TERMINE LA DESCRIPCION
            $fila=0;
            $inicio_desc=0;
            $final_desc=$leng_max;
            if(($c_rows+$filas_usar)<=39){ 
                 $total = $total + Common::quitar_coma($value["TOTAL"], 2);
                 while ($lengt > 0) {
                    //SUMAR FILAS AUXILIARES
                    $lengt=$lengt-$leng_max;
                    //SI LA FILA AUXILIAR ES 1 SOLO SE CARGA LA DESCRIPCION, SI NO SE CARGAN TODOS LOS VALORES VACION MAS LA DESCRIPCION
                   if($fila==0){
                        $articulos[$c_rows]["item"] = $value["ITEM"];
                        $articulos[$c_rows]["precio"] = $value["PRECIO"];
                        $articulos[$c_rows]["total"] = $value["TOTAL"];
                        $articulos[$c_rows]["cantidad"] = $value["CANTIDAD"];
                        $articulos[$c_rows]["descripcion"] = substr($descripcion,$inicio_desc,$final_desc );
                   }else{
                        $articulos[$c_rows]["descripcion"] = substr($descripcion,$inicio_desc,$final_desc);
                        $articulos[$c_rows]["item"] = " ";
                        $articulos[$c_rows]["precio"] = " ";
                        $articulos[$c_rows]["total"] = " ";
                        $articulos[$c_rows]["cantidad"] = " ";
                   }
                   $fila=$fila+1;
                   $c_rows=$c_rows+1;
                   $inicio_desc=$inicio_desc+$leng_max;
                }
            }else{
                $ult_art_cargado=false;
                while ($c_rows < 39) {
                   $articulos[$c_rows]["descripcion"] = " ";
                   $articulos[$c_rows]["item"] = " ";
                   $articulos[$c_rows]["precio"] = " ";
                   $articulos[$c_rows]["total"] = " ";
                   $articulos[$c_rows]["cantidad"] = " ";
                   $c_rows=$c_rows+1;
                }
            }
            

            

            /*  --------------------------------------------------------------------------------- */
            
            // CONTAR CANTIDAD DE FILAS DE HOJAS 

           /* $c_rows = $c_rows + 1;    */
          /* log::error(["filas"=>$c_rows]);*/
            
            /*  --------------------------------------------------------------------------------- */

            // CONTAR LA CANTIDAD DE FILAS 

            $c = $c + 1;

            /*  --------------------------------------------------------------------------------- */


            // SI CANTIDAD DE FILAS ES IGUAL A 39 ENTONCES CREAR PAGINA 

            if ($c_rows === 39){
                
                
                
                // AGREGAR ARTICULOS 

                $data['articulos'] = $articulos;

                // RESTAR LAS CANTIDADES CARGADAS 

                $c_rows_array = $c_rows_array - $contador_articulos;

                //CERAR CONTADOR ARTICULOS POR HOJA

                $contador_articulos=0;

                // PONER TRUE SWITCH YA QUE CREO UNA PAGINA 

                $switch_hojas = true;

                // CARGAR SUB TOTALES POR HOJA

                $data['letra'] = 'Son Dolares: '.($formatter->toMoney($total, 2, 'dolares'));
                $data['total'] = Common::precio_candec_sin_letra($total, 2);

                $html = view('pdf.facturaExportacion', $data)->render();
                
                /*  --------------------------------------------------------------------------------- */

                // SI NO ES LA PRIMERA HOJA AGREGAR PAGINA

                if ($c !== 39) {
                    $mpdf->AddPage();
                }

                /*  --------------------------------------------------------------------------------- */

                // CERAR CONTADOR DE FILAS CARGADAS POR HOJAS Y ARTICULOS

                $c_rows = 0;
                $total = 0;
                $data['articulos'] = [];
                $articulos = [];   
                $mpdf->WriteHTML($html);
                //CARGAR EL ULTIMO ARTICULO SI NO CARGO EN LA FACTURA
                $fila=0;
                $inicio_desc=0;
                $final_desc=$leng_max;
                if(!$ult_art_cargado){
                     $total = $total + Common::quitar_coma($value["TOTAL"], 2);
                     while ($lengt > 0) {
                        //SUMAR FILAS AUXILIARES
                        $lengt=$lengt-$leng_max;
                        //SI LA FILA AUXILIAR ES 1 SOLO SE CARGA LA DESCRIPCION, SI NO SE CARGAN TODOS LOS VALORES VACION MAS LA DESCRIPCION
                       if($fila==0){
                            $articulos[$c_rows]["item"] = $value["ITEM"];
                            $articulos[$c_rows]["precio"] = $value["PRECIO"];
                            $articulos[$c_rows]["total"] = $value["TOTAL"];
                            $articulos[$c_rows]["cantidad"] = $value["CANTIDAD"];
                            $articulos[$c_rows]["descripcion"] = substr($descripcion,$inicio_desc,$final_desc );
                       }else{
                            $articulos[$c_rows]["descripcion"] = substr($descripcion,$inicio_desc,$final_desc);
                            $articulos[$c_rows]["item"] = " ";
                            $articulos[$c_rows]["precio"] = " ";
                            $articulos[$c_rows]["total"] = " ";
                            $articulos[$c_rows]["cantidad"] = " ";
                       }
                       $fila=$fila+1;
                       $c_rows=$c_rows+1;
                       $inicio_desc=$inicio_desc+$leng_max;
                    }
                }
                $ult_art_cargado=true;
               

                /*  --------------------------------------------------------------------------------- */

            }  else if ($c_rows_array < 39 && $c_filas_total === $c) {
                
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

    public static function quitar_tildes($cadena) {
      $no_permitidas= array ("á", "ã","Á","Ã","À","Ã™","Ã ","Ã¨","Ã¬","Ã²","Ã¹","Ã®","Ã´","Ã»","Ã‚","ÃŠ","ÃŽ","Ã”","Ã›","Ã„","Ã‹","Ã¢","Ã¶","Ã–","Ã¯","Ã¤","Ã","é","É","ê","í","Í","Ì","ó", "ô", "õ","Ó","Ò", "Õ", "Ô","Ú","Ù","ú","ü","ñ","«","ç","Ç", "Nº");
      $permitidas= array ("a", "a", "A","A","A","A","A","A","A","A","A","A","A","A","AS","AZ","A","A","A","A","A","A","A","A","A","A","A","e","E","e","i","I","I","o", "o", "o","O","O","O", "O","U","U","u","u","n","","c","C", "N");
      $texto = str_replace($no_permitidas, $permitidas ,$cadena);
      return $texto;
    }
    public static function calcular_filas($tamaño,$maximo){
        $filas=0;
        while ($tamaño > 0) {
            $filas=$filas+1;
            $tamaño=$tamaño-$maximo;
        }
        return $filas;
    }
}
