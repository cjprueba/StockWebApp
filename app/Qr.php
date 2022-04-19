<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Fpdf\Fpdf;
use App\Barcode;
use App\ProductosAux;
use Picqer\Barcode\BarcodeGeneratorPNG;
use TCPDF;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File; 
class Qr extends Model
{
    //
     public static function crear_pdf($datos)
    {
     $c=0;

       $file=public_path('qr.png');
        /*  --------------------------------------------------------------------------------- */
/*       var_dump($datos);*/
        $pag=1;
          $pdf = new FPDF('L','mm',array(105,22));
          $pdf->AddPage();
          $x=3;
         $y = -3;
      foreach ($datos["data"] as $key => $value) {
        # code...
           
               while ($c<$value["CANTIDAD"]){
                  $c=$c+1;
                      if($x>77){
                         $y=$y+27;
                          if($y > 22){

                           $pag=$pag+1;
                           $pdf->AddPage();
    
                              $y = -3;
                      }
           
                    $x=3;
             }

              
             Qr::crear_qr($value["ID_LOTE"]);

             $file=public_path(''.$value["ID_LOTE"].'.png');
           $pdf->Image($file,$x,$y,28,28);
           File::delete(''.$value["ID_LOTE"].'.png');
           //$y=$y+35;
           $x=$x+37-2;
         }
         $c=0;
      }


     
/*        foreach ($codigos as $key => $value) {
           if($x>73){
             $y=$y+27;
             if($y > 22){
                $pag=$pag+1;
                $pdf->AddPage();
    
                $y = -3;
            }
           
            $x=-1;
           }
           
             Qr::crear_qr($value->CODIGO);
             $file=public_path(''.$value->CODIGO.'.png');
           $pdf->Image($file,$x,$y,28,28);
           File::delete(''.$value->CODIGO.'.png');
           //$y=$y+35;
           $x=$x+37-1.5;
      
        }*/



        return $pdf->Output('qr.pdf','d');

        /*  --------------------------------------------------------------------------------- */

    }
         public static function crear_pdf_qr_2($datos)
    {
        $user = auth()->user();
        $c=0;


       $file=public_path('qr.png');
        /*  --------------------------------------------------------------------------------- */
/*       var_dump($datos);*/
        $pag=1;
          $pdf = new FPDF('L','mm',array(105,22));
          $pdf->AddPage();
          $x=25;
          $y = 0.3;
          $z = 2;
          $c=0;
      foreach ($datos["data"] as $key => $value) {
        # code...
          while ($c<$value["CANTIDAD"]){
            $c=$c+1;
            if($x>97){
              $y=$y+27;
              if($y > 22){
                $pag=$pag+1;
                $pdf->AddPage();
                $y=0.3;
              }   
              $x=25;
            }

              
            Qr::crear_qr($value["CODIGO"],1);

            $file=public_path(''.$value["CODIGO"].'.png');
            $pdf->Image($file,$x-20,$y-2.7,24,24);
            File::delete(''.$value["CODIGO"].'.png');
            //$y=$y+35;
            $x=$x+37-1.5;
         }
         $c=0;
      }


     
/*        foreach ($codigos as $key => $value) {
           if($x>73){
             $y=$y+27;
             if($y > 22){
                $pag=$pag+1;
                $pdf->AddPage();
    
                $y = -3;
            }
           
            $x=-1;
           }
           
             Qr::crear_qr($value->CODIGO);
             $file=public_path(''.$value->CODIGO.'.png');
           $pdf->Image($file,$x,$y,28,28);
           File::delete(''.$value->CODIGO.'.png');
           //$y=$y+35;
           $x=$x+37-1.5;
      
        }*/



        return $pdf->Output('qr.pdf','i');

        /*  --------------------------------------------------------------------------------- */

    }
         public static function crear_qr($codigo,$tipo)
    {
        $user = auth()->user();
        /*  --------------------------------------------------------------------------------- */
        $file=public_path(''.$codigo.'.png');
        if($tipo===1){
           return \QRCode::text('http://131.196.192.165:8080/productoqr?s='.$user->id_sucursal.'&c="'.$codigo."" )->setOutfile($file)->png();
         }elseif ($tipo===2) {
            
            return \QRCode::text('http://131.196.192.165:8080/cajaqr?s='.$user->id_sucursal."&c='".$codigo."'&t=1")->setOutfile($file)->png();
         }elseif ($tipo===3){
            return \QRCode::text('http://131.196.192.165:8080/cajaqr?s='.$user->id_sucursal."&c='".$codigo."'&t=2")->setOutfile($file)->png();
         }
      

        /*  --------------------------------------------------------------------------------- */

    }
             
    public static function crear_barcode($datos){
      if($datos['tamaño']==='1'){
        return(qr::etiqueta_tipo_1($datos));
      }else if($datos['tamaño']==='2'){
        return(qr::etiqueta_tipo_2($datos));
      }else if ($datos['tamaño']==='3'){
        return(qr::etiqueta_tipo_3($datos));
      }elseif ($datos['tamaño']==='4') {
        return(qr::etiqueta_tipo_4($datos));
      }elseif ($datos['tamaño']==='5') {
        return(qr::crear_pdf_qr_2($datos));
        var_dump($datos['switch_desc']);
      }elseif ($datos['seleccionImpresion']==='3' && $datos['switch_desc']===false) {
        return(qr::etiqueta_nombre_desc($datos));
      }elseif ($datos['seleccionImpresion']==='3' && $datos['switch_desc']===true) {
        return(qr::etiqueta_nombre_desc_old($datos));
      }     
    }
    public static function etiqueta_nombre_desc_old($datos){
      $name = '111'; 
      $type = 'C128B';

      //definir estilo del Barcode 
      //-------------------------------------------------------------------------
        $style = array(
            'position' => '',
            'align' => 'N',
            'stretch' => true,
            'fitwidth' => false,
            'cellfitalign' => '',
            'border' => false, // border
            'hpadding' => 3,
            'vpadding' => 1.5,
            'fgcolor' => array(0, 0, 0),
            'bgcolor' => false, //array(255,255,255),
                     'text' => true, // whether to display the text below the barcode
                     'font' => 'helvetica', //font
                     'fontsize' => 6, //font size
            'stretchtext' => 4
        );
      $pdf = new TCPDF('L','mm',array(78, 38));
      $pdf->SetPrintHeader(false);
      $pdf->SetPrintFooter(false);
      $pdf->addPage();


      $pdf->SetFont('helvetica', '', 6);
      $pag=1;
      $x=25;
      $y = 0.3;
      $z = 2;
      $c=0;
      $a=0;
      $b=17.5;
      $sum=0;
      foreach ($datos["data"] as $key => $value) {
        $htmldesc=
        '<html>
                    <style>
                    </style>
                    <body>
                    <table style="width:115%" height="100px">
                      <tr nobr="true">
                        <td>'.$value['NOMBRE'].'</td>
                      </tr>
                    </table>
                    </body>
                    </html>';

                    // '<html>
                    // <style>
                    // </style>
                    // <body>
                    // <p>'.$value['NOMBRE'].'</p>
                    // </body>
                    // </html>';


        while($c<$value["CANTIDAD"]){
          $c=$c+1;
          if($y > 28){
            $pag=$pag+1;
            $pdf->AddPage();
            $y = 0.3;
          }
          $pdf->SetAutoPageBreak(FALSE, 0);
          $pdf->SetFontSize(9.5);
          $pdf->SetFont('freesans', 'B');
          $cantCaract=strlen($value['NOMBRE']);
          log::error(["cantidad de caracteres: ",$cantCaract]);
          while ($a<$cantCaract) {
            $cantCaract=($cantCaract-44);
            $sum=$sum+1.15;
          }
          if ($sum>7.5) {
            $pdf->SetFontSize(8.5);
          }else if($sum>8.5){
            $pdf->SetFontSize(8);
          }
          $pdf->text($x, (($b-3.5)-$sum), substr(Qr::quitar_tildes($value["CODIGO"]), 0,45), false, false, true, 0, 1, '', false, '', 0);
          
          $pdf->writeHTMLCell(0, 2, 0, $b-$sum, $htmldesc, 0, 0, 0, false, 'left', false);
          $y=$y+28;
          $sum=0;
        }

        $c=0;
      }
        return $pdf->Output($name . ".pdf", 'D'); //D Download I Show
    }

    public static function etiqueta_nombre_desc($datos){
      if ($datos['tamaño']==='9'){
      
        $name = '111'; 
        $type = 'C128B';

        //definir estilo del Barcode 
        //-------------------------------------------------------------------------
          $style = array(
              'position' => '',
              'align' => 'N',
              'stretch' => true,
              'fitwidth' => false,
              'cellfitalign' => '',
              'border' => false, // border
              'hpadding' => 3,
              'vpadding' => 1.5,
              'fgcolor' => array(0, 0, 0),
              'bgcolor' => false, //array(255,255,255),
                       'text' => true, // whether to display the text below the barcode
                       'font' => 'helvetica', //font
                       'fontsize' => 6, //font size
              'stretchtext' => 4
          );
        $pdf = new TCPDF('L','mm',array(100, 75));
        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);
        $pdf->addPage();


        $pdf->SetFont('helvetica', '', 6);
        $pag=1;
        $x=25;
        $y = 0.3;
        $z = 2;
        $c=0;
        $a=0;
        $b=17.5;
        $sum=0;
        
        foreach ($datos["data"] as $key => $value) {
            $producto_det = DB::connection('retail')
                  ->table('DESCRIPCION_DETALLADA')
                  ->select(DB::raw('DESCRIPCION_DETALLADA.NOMBRE_DEL_PRODUCTO,
                                    DESCRIPCION_DETALLADA.MARCA,
                                    DESCRIPCION_DETALLADA.PROPIEDADES,
                                    DESCRIPCION_DETALLADA.FORMA_DE_USO,
                                    DESCRIPCION_DETALLADA.INGREDIENTES,
                                    DESCRIPCION_DETALLADA.CONTENIDO,
                                    IFNULL(DESCRIPCION_DETALLADA.VALOR_NUTRICIONAL, "INDEFINIDO") AS VALORNUTRCIONAL'))
                  ->where('DESCRIPCION_DETALLADA.CODIGO','=', $value["CODIGO"])
                  ->get()
                  ->toArray();
            $nestedData['COD_PROD']=$value["CODIGO"];
            $nestedData['NOMBRE_DEL_PRODUCTO']=$producto_det[0]->NOMBRE_DEL_PRODUCTO;
            $nestedData['MARCA']=$producto_det[0]->MARCA;
            $nestedData['PROPIEDADES']=$producto_det[0]->PROPIEDADES;
            $nestedData['FORMA_DE_USO']=$producto_det[0]->FORMA_DE_USO;
            $nestedData['CONTENIDO']=$producto_det[0]->CONTENIDO;
            $nestedData['INGREDIENTES']=$producto_det[0]->INGREDIENTES;
            $nestedData['TAMAÑO']="9";
            if($producto_det[0]->VALORNUTRCIONAL==="INDEFINIDO" || $producto_det[0]->VALORNUTRCIONAL == ""){
              $nestedData['VALORNUTRCIONAL_CHECK']=false;
              $nestedData['VALORNUTRCIONAL']='';
            }else{
              $nestedData['VALORNUTRCIONAL_CHECK']=true;
              $nestedData['VALORNUTRCIONAL']=$producto_det[0]->VALORNUTRCIONAL;
            }
            $htmldesc= view('pdf.traduccion', $nestedData)->render();
            while($c<$value["CANTIDAD"]){
              $c=$c+1;
              if($y > 28){
                $pag=$pag+1;
                $pdf->AddPage();
                $y = 0.3;
              }
              $pdf->SetFontSize(7);
              $pdf->SetAutoPageBreak(FALSE, 0);
              $pdf->SetFont('freesans', 'B');

              $pdf->StartTransform();
              $pdf->Rotate(90, 5, 48);
              $pdf->text(5,48, substr(Qr::quitar_tildes($value["CODIGO"]), 0,45), false, false, true, 0, 1, '', false, 'center', 0);
              $pdf->StopTransform();
              $pdf->SetFontSize(6.3);
              $pdf->StartTransform();
              $pdf->Rotate(90, 3, 74);
              $pdf->writeHTMLCell(73, 0, 3, 74, $htmldesc, 0, 0, 0, false, 'left', false);
              $pdf->StopTransform();

              

              $y=$y+28;
              $sum=0;
            }
            $c=0;
        }

        return $pdf->Output($name . ".pdf", 'D'); //D Download I Show
      }else if($datos['tamaño']==='8') {
        $name = '111'; 
        $type = 'C128B';

        //definir estilo del Barcode 
        //-------------------------------------------------------------------------
          $style = array(
              'position' => '',
              'align' => 'N',
              'stretch' => true,
              'fitwidth' => false,
              'cellfitalign' => '',
              'border' => false, // border
              'hpadding' => 3,
              'vpadding' => 1.5,
              'fgcolor' => array(0, 0, 0),
              'bgcolor' => false, //array(255,255,255),
                       'text' => true, // whether to display the text below the barcode
                       'font' => 'helvetica', //font
                       'fontsize' => 6, //font size
              'stretchtext' => 4
          );
        $pdf = new TCPDF('L','mm',array(80, 40));
        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);
        $pdf->addPage();


        $pdf->SetFont('helvetica', 'B', 6);
        $pag=1;
        $x=25;
        $y = 0.3;
        $z = 2;
        $c=0;
        $a=0;
        $b=17.5;
        $sum=0;
        
        foreach ($datos["data"] as $key => $value) {
            $producto_det = DB::connection('retail')
                  ->table('DESCRIPCION_DETALLADA')
                  ->select(DB::raw('DESCRIPCION_DETALLADA.NOMBRE_DEL_PRODUCTO,
                                    DESCRIPCION_DETALLADA.MARCA,
                                    DESCRIPCION_DETALLADA.PROPIEDADES,
                                    DESCRIPCION_DETALLADA.FORMA_DE_USO,
                                    DESCRIPCION_DETALLADA.INGREDIENTES,
                                    DESCRIPCION_DETALLADA.CONTENIDO,
                                    IFNULL(DESCRIPCION_DETALLADA.VALOR_NUTRICIONAL, "INDEFINIDO") AS VALORNUTRCIONAL'))
                  ->where('DESCRIPCION_DETALLADA.CODIGO','=', $value["CODIGO"])
                  ->get()
                  ->toArray();
            $nestedData['COD_PROD']=$value["CODIGO"];
            $nestedData['NOMBRE_DEL_PRODUCTO']=$producto_det[0]->NOMBRE_DEL_PRODUCTO;
            $nestedData['MARCA']=$producto_det[0]->MARCA;
            $nestedData['PROPIEDADES']=$producto_det[0]->PROPIEDADES;
            $nestedData['FORMA_DE_USO']=$producto_det[0]->FORMA_DE_USO;
            $nestedData['CONTENIDO']=$producto_det[0]->CONTENIDO;
            $nestedData['INGREDIENTES']=$producto_det[0]->INGREDIENTES;
            $nestedData['TAMAÑO']="9";
            if($producto_det[0]->VALORNUTRCIONAL==="INDEFINIDO" || $producto_det[0]->VALORNUTRCIONAL == ""){
              $nestedData['VALORNUTRCIONAL_CHECK']=false;
              $nestedData['VALORNUTRCIONAL']='';
            }else{
              $nestedData['VALORNUTRCIONAL_CHECK']=true;
              $nestedData['VALORNUTRCIONAL']=$producto_det[0]->VALORNUTRCIONAL;
            }
            $htmldesc= view('pdf.traduccion', $nestedData)->render();

            while($c<$value["CANTIDAD"]){
              $c=$c+1;
              if($y > 28){
                $pag=$pag+1;
                $pdf->AddPage();
                $y = 0.3;
              }
              $pdf->SetFontSize(7);
              $pdf->SetAutoPageBreak(FALSE, 0);
              $pdf->SetFont('freesans', 'L');
              $pdf->StartTransform();
              $pdf->Rotate(90, 3, 30);
              $pdf->text(3,30, substr(Qr::quitar_tildes($value["CODIGO"]), 0,45), false, false, true, 0, 1, '', false, 'center', 0);
              $pdf->StopTransform();
              $pdf->SetFontSize(5.7);
              $pdf->StartTransform();
              $pdf->Rotate(90, -4, 38.5);
              $pdf->writeHTMLCell(37, 0, -4, 38.5, $htmldesc, 0, 0, 0, false, 'left', false);
              $pdf->StopTransform();
              $y=$y+28;
              $sum=0;
            }
            $c=0;
        }

        return $pdf->Output($name . ".pdf", 'D'); //D Download I Show
      }else if($datos['tamaño']==='7'){
        $name = '111'; 
        $type = 'C128B';

        //definir estilo del Barcode 
        //-------------------------------------------------------------------------
          $style = array(
              'position' => '',
              'align' => 'N',
              'stretch' => true,
              'fitwidth' => false,
              'cellfitalign' => '',
              'border' => false, // border
              'hpadding' => 3,
              'vpadding' => 1.5,
              'fgcolor' => array(0, 0, 0),
              'bgcolor' => false, //array(255,255,255),
                       'text' => true, // whether to display the text below the barcode
                       'font' => 'helvetica', //font
                       'fontsize' => 6, //font size
              'stretchtext' => 4
          );
        $pdf = new TCPDF('L','mm',array(55, 29));
        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);
        $pdf->addPage();


        $pdf->SetFont('helvetica', 'B', 6);
        $pag=1;
        $x=25;
        $y = 0.3;
        $z = 2;
        $c=0;
        $a=0;
        $b=17.5;
        $sum=0;
        
        foreach ($datos["data"] as $key => $value) {
            $producto_det = DB::connection('retail')
                  ->table('DESCRIPCION_DETALLADA')
                  ->select(DB::raw('DESCRIPCION_DETALLADA.NOMBRE_DEL_PRODUCTO,
                                    DESCRIPCION_DETALLADA.MARCA,
                                    DESCRIPCION_DETALLADA.PROPIEDADES,
                                    DESCRIPCION_DETALLADA.FORMA_DE_USO,
                                    DESCRIPCION_DETALLADA.VALOR_NUTRICIONAL,
                                    DESCRIPCION_DETALLADA.CONTENIDO,
                                    IFNULL(DESCRIPCION_DETALLADA.CALORIAS, "INDEFINIDO") AS CALORIAS,
                                    IFNULL(DESCRIPCION_DETALLADA.GRAD_ALCOH,"INDEFINIDO") AS GRAD_ALCOH,
                                    IFNULL(DESCRIPCION_DETALLADA.INGREDIENTES,"INDEFINIDO") AS INGREDIENTES'))
                  ->where('DESCRIPCION_DETALLADA.CODIGO','=', $value["CODIGO"])
                  ->get()
                  ->toArray();
                  $nestedData['COD_PROD']=$value["CODIGO"];
                  $nestedData['NOMBRE']=$producto_det[0]->NOMBRE_DEL_PRODUCTO;
                  $nestedData['MARCA']=$producto_det[0]->MARCA;
                  $nestedData['PROPIEDADES']=$producto_det[0]->PROPIEDADES;
                  $nestedData['CONTENIDO']=$producto_det[0]->CONTENIDO;
                  $nestedData['TAMAÑO']="7";
                  if($producto_det[0]->CALORIAS==="INDEFINIDO" || $producto_det[0]->CALORIAS == ""){
                    $nestedData['CALORIAS_CHECK']=false;
                    $nestedData['CALORIAS']='';
                  }else{
                    $nestedData['CALORIAS_CHECK']=true;
                    $nestedData['CALORIAS']=$producto_det[0]->CALORIAS;
                  }
                  if($producto_det[0]->INGREDIENTES==="INDEFINIDO" || $producto_det[0]->INGREDIENTES==""){
                    $nestedData['INGRE_CHECK']=false;
                    $nestedData['INGREDIENTES']='';
                  }else{
                    $nestedData['INGRE_CHECK']=true;
                    $nestedData['INGREDIENTES']=$producto_det[0]->INGREDIENTES;
                  }
                  if($producto_det[0]->GRAD_ALCOH=="INDEFINIDO" || $producto_det[0]->GRAD_ALCOH==""){
                    $nestedData['ALCO_CHECK']=false;
                    $nestedData['GRAD_ALCOH']='';
                  }else{
                    $nestedData['ALCO_CHECK']=true;
                    $nestedData['GRAD_ALCOH']=$producto_det[0]->GRAD_ALCOH;
                  }
         $htmldesc= view('pdf.traduccion', $nestedData)->render();

            while($c<$value["CANTIDAD"]){
              $c=$c+1;
              if($y > 28){
                $pag=$pag+1;
                $pdf->AddPage();
                $y = 0.3;
              }
              $pdf->SetFontSize(6.4);
              $pdf->SetAutoPageBreak(FALSE, 0);
              $pdf->SetFont('freesans', 'L');
              $pdf->text(18.7,1.5, substr(Qr::quitar_tildes($value["CODIGO"]), 0,45), false, false, true, 0, 1, '', false, 'center', 0);
              $pdf->SetFontSize(5.7);
              $pdf->writeHTMLCell(52.5, 0, 1, 0, $htmldesc, 0, 0, 0, false, 'center', false);
              $y=$y+28;
              $sum=0;
            }
            $c=0;
        }

        return $pdf->Output($name . ".pdf", 'D'); //D Download I Show
      }else if($datos['tamaño']==='6'){
        $name = '111'; 
        $type = 'C128B';

        //definir estilo del Barcode 
        //-------------------------------------------------------------------------
          $style = array(
              'position' => '',
              'align' => 'N',
              'stretch' => true,
              'fitwidth' => false,
              'cellfitalign' => '',
              'border' => false, // border
              'hpadding' => 3,
              'vpadding' => 1.5,
              'fgcolor' => array(0, 0, 0),
              'bgcolor' => false, //array(255,255,255),
                       'text' => true, // whether to display the text below the barcode
                       'font' => 'helvetica', //font
                       'fontsize' => 6, //font size
              'stretchtext' => 4
          );
        $pdf = new TCPDF('L','mm',array(105, 22));
        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);
        $pdf->addPage();


        $pdf->SetFont('helvetica', 'B', 6);
        $pag=1;
        $x=25;
        $y = 0.3;
        $z = 2;
        $c=0;
        $a=0;
        $b=17.5;
        $sum=0;
        
        foreach ($datos["data"] as $key => $value) {
            $producto_det = DB::connection('retail')
                  ->table('DESCRIPCION_DETALLADA')
                  ->select(DB::raw('DESCRIPCION_DETALLADA.NOMBRE_DEL_PRODUCTO,
                                    DESCRIPCION_DETALLADA.MARCA,
                                    DESCRIPCION_DETALLADA.PROPIEDADES,
                                    DESCRIPCION_DETALLADA.FORMA_DE_USO,
                                    DESCRIPCION_DETALLADA.VALOR_NUTRICIONAL,
                                    DESCRIPCION_DETALLADA.CONTENIDO,
                                    IFNULL(DESCRIPCION_DETALLADA.CALORIAS, "INDEFINIDO") AS CALORIAS,
                                    IFNULL(DESCRIPCION_DETALLADA.GRAD_ALCOH,"INDEFINIDO") AS GRAD_ALCOH,
                                    IFNULL(DESCRIPCION_DETALLADA.INGREDIENTES,"INDEFINIDO") AS INGREDIENTES'))
                  ->where('DESCRIPCION_DETALLADA.CODIGO','=', $value["CODIGO"])
                  ->get()
                  ->toArray();
                  $nestedData['COD_PROD']=$value["CODIGO"];
                  $nestedData['NOMBRE']=$producto_det[0]->NOMBRE_DEL_PRODUCTO;
                  $nestedData['MARCA']=$producto_det[0]->MARCA;
                  $nestedData['PROPIEDADES']=$producto_det[0]->PROPIEDADES;
                  $nestedData['CONTENIDO']=$producto_det[0]->CONTENIDO;
                  $nestedData['TAMAÑO']="6";
                  if($producto_det[0]->CALORIAS==="INDEFINIDO" || $producto_det[0]->CALORIAS == ""){
                    $nestedData['CALORIAS_CHECK']=false;
                    $nestedData['CALORIAS']='';
                  }else{
                    $nestedData['CALORIAS_CHECK']=true;
                    $nestedData['CALORIAS']=$producto_det[0]->CALORIAS;
                  }
                  if($producto_det[0]->INGREDIENTES==="INDEFINIDO" || $producto_det[0]->INGREDIENTES==""){
                    $nestedData['INGRE_CHECK']=false;
                    $nestedData['INGREDIENTES']='';
                  }else{
                    $nestedData['INGRE_CHECK']=true;
                    $nestedData['INGREDIENTES']=$producto_det[0]->INGREDIENTES;
                  }
                  if($producto_det[0]->GRAD_ALCOH=="INDEFINIDO" || $producto_det[0]->GRAD_ALCOH==""){
                    $nestedData['ALCO_CHECK']=false;
                    $nestedData['GRAD_ALCOH']='';
                  }else{
                    $nestedData['ALCO_CHECK']=true;
                    $nestedData['GRAD_ALCOH']=$producto_det[0]->GRAD_ALCOH;
                  }
         $htmldesc= view('pdf.traduccion', $nestedData)->render();

              $nestedData=[];  
            while($c<$value["CANTIDAD"]){
              $c=$c+1;
              if($x>97){
                $y=$y+27;
                if($y > 22){
                  $pag=$pag+1;
                  $pdf->AddPage();
                  $y = 0.3;
                }
                $x=25;
              }
              $pdf->SetFontSize(5);
              $pdf->SetAutoPageBreak(FALSE, 0);
              $pdf->SetFont('freesans', 'B');
              $pdf->text($x-15.5,$y+0.3, substr(Qr::quitar_tildes($value["CODIGO"]), 0,45), false, false, true, 0, 1, '', false, 'center', 0);
              $pdf->SetFont('freesans', 'B');
              $pdf->SetFontSize(1.5);
              $pdf->writeHTMLCell(31.5, 0, $x-24, $y+1.3, $htmldesc, 0, 0, 0, false, 'center', false);
              $x=$x+37-1.5;
            }
            $c=0;
        }

        return $pdf->Output($name . ".pdf", 'D'); //D Download I Show
      }
    }

    public static function etiqueta_tipo_1($datos){
      $user = auth()->user();
      $name = '111'; 
      $type = 'C128B';

      //definir estilo del Barcode 
      //-------------------------------------------------------------------------
      $style = array(
        'position' => '',
        'align' => 'N',
        'stretch' => true,
        'fitwidth' => false,
        'cellfitalign' => '',
        'border' => false, // border
        'hpadding' => 3,
        'vpadding' => 1.5,
        'fgcolor' => array(0, 0, 0),
        'bgcolor' => false, //array(255,255,255),
        'text' => true, // whether to display the text below the barcode
        'font' => 'helvetica', //font
        'fontsize' => 6, //font size
        'stretchtext' => 4
      );

      $pdf = new TCPDF('L','mm',array(90,35));
      $pdf->SetPrintHeader(false);
      $pdf->SetPrintFooter(false);
      $pdf->addPage();
      $pag=1;
      $x=25;
      $y = 0.3;
      $z = 2;
      $c=0;
      if($datos['seleccionImpresion']==='2'){
          foreach ($datos["data"] as $key => $value) {
            while($c<$value["CANTIDAD"]){
              $c=$c+1;
              if($y > 28){
                $pag=$pag+1;
                $pdf->AddPage();
                $y = 0.3;
              }
        

              // $pdf->text($x-14.5, $y+11, $value['PRECIO'], false, false, true);
              $pdf->SetFont('helvetica', 'B', 15);
              
              // //Color Negro
              $pdf->SetTextColor(0, 0, 0);
              $pdf->text($x-24.5, $y, utf8_decode(utf8_encode(utf8_decode(utf8_encode($value['DESCRIPCION'])))), false, false, true, 0, 1, '', false, '', 0);
              //Tamaño de fuente
              $pdf->SetFontSize(13);
              if($value['MONEDA']===1){
                $pdf->text($x+28, $y+13, 'G$:'.$value['PRECIO'], false, false, true, 0, 1, '', false, '', 0);
              }elseif ($value['MONEDA']===2) {
                $pdf->SetFontSize(16);
                $pdf->text($x+28, $y+13, 'U$:'.$value['PRECIO'], false, false, true, 0, 1, '', false, '', 0);
                $pdf->SetFontSize(13);
              }elseif ($value['MONEDA']===4) {
                $pdf->SetFontSize(16);
                $pdf->text($x+28, $y+13, 'R$:'.$value['PRECIO'], false, false, true, 0, 1, '', false, '', 0);
                $pdf->SetFontSize(13);
              }elseif ($value['MONEDA']===3){
                $pdf->text($x+28, $y+13, 'P$:'.$value['PRECIO'], false, false, true, 0, 1, '', false, '', 0);
              }
              $pdf->SetFontSize(10);
              $pdf->text($x+28, $y+9, 'UNITARIO', false, false, true, 0, 1, '', false, '', 0);
              $pdf->SetFontSize(6.5);
              $pdf->text($x+43.5, $y+19.2, 'COD. INTERNO', false, false, true, 0, 1, '', false, '', 0);
              // //background
              $pdf->SetFontSize(28);
              $pdf->writeHTMLCell(51, 0, $x-23, $y+8,  '',1, 0, 1, 'C', true, 'J', true);

              $pdf->write1DBarcode($value["CODIGO_INTERNO"], $type, $x+43, $y+20.5, 30, 12, 0, $style, 'N');
              $pdf->write1DBarcode($value["CODIGO"], $type, $x-12.8, $y+20.5, 45, 12, 0, $style, 'N');
              //------------------------------------------------------------------------------------------------------
              //Color Blanco
              $pdf->SetTextColor(1000, 1000, 1000);
              //Tamaño de fuente
              $pdf->SetFontSize(10); 
              $pdf->text($x-22, $y+10, 'DESDE 6', false, false, true, 0, 1, '', false, '', 0);
              $pdf->text($x-16, $y+14, 'UNID.', false, false, true, 0, 1, '', false, '', 0);
              $pdf->SetFontSize(13);
              if($value['MONEDA']===1){
                $pdf->text($x-4, $y+13, 'G$:'.$value['PRECIO_MAYORISTA'], false, false, true, 0, 1, '', false, '', 0);
              }elseif ($value['MONEDA']===2) {
                $pdf->SetFontSize(16);
                $pdf->text($x-4, $y+13, 'U$:'.$value['PRECIO_MAYORISTA'], false, false, true, 0, 1, '', false, '', 0);
                $pdf->SetFontSize(13);
              }elseif ($value['MONEDA']===4) {
                $pdf->SetFontSize(16);
                $pdf->text($x-4, $y+13, 'R$:'.$value['PRECIO_MAYORISTA'], false, false, true, 0, 1, '', false, '', 0);
                $pdf->SetFontSize(13);
              }elseif ($value['MONEDA']===3) {
                $pdf->text($x+28, $y+13, 'P$:'.$value['PRECIO'], false, false, true, 0, 1, '', false, '', 0);
              }
              $pdf->SetFontSize(10);
              $pdf->text($x-4, $y+9, 'MAYORISTA', false, false, true, 0, 1, '', false, '', 0);
              $pdf->SetFontSize(20);
              $pdf->SetFont('helvetica');
              $pdf->text($x-6.3, $y+7.5, '|', false, false, true, 0, 1, '', false, '', 0);
              $pdf->text($x-6.3, $y+11, '|', false, false, true, 0, 1, '', false, '', 0);
              $y=$y+28;
            }
          $c=0;
        }
      }
      else{
        foreach ($datos['seleccion_gondola'] as $key => $value) {
          $productos=DB::connection('retail')
                        ->table('gondola_tiene_productos AS GTP')
                        ->select(DB::raw('PRA.CODIGO, PR.DESCRIPCION, PRA.PREC_VENTA AS PRECIO, PRA.PREMAYORISTA AS PRECIO_MAYORISTA, PRA.CODIGO_INTERNO, PRA.MONEDA'))
                        ->leftjoin('productos_aux AS PRA', 'PRA.ID', '=', 'GTP.FK_PRODUCTOS_AUX')
                        ->leftjoin('productos AS PR', 'PR.CODIGO', '=', 'PRA.CODIGO')
                        ->where('GTP.ID_GONDOLA', '=', $value['ID'])
                        ->where('PRA.ID_SUCURSAL', '=', $user->id_sucursal);
                     
          if ($datos['tipoStock'] === '1') {
            $productos = $productos->whereRaw('(IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = PRA.CODIGO) AND (l.ID_SUCURSAL = PRA.ID_SUCURSAL))),0)) > 0');
          }else{
            $productos = $productos->whereRaw('(IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = PRA.CODIGO) AND (l.ID_SUCURSAL = PRA.ID_SUCURSAL))),0)) <= 0');
          }
          $productos = $productos->get()

                                 ->toArray();
                                log::error(["datos del producto"=>$productos]);
           
          foreach ($productos as $key => $value2) {
              if($y > 28){
                $pag=$pag+1;
                $pdf->AddPage();
                $y = 0.3;
              }
              // $pdf->text($x-14.5, $y+11, $value['PRECIO'], false, false, true);
              $pdf->SetFont('helvetica', 'B', 15);
              // //Color Negro
              $pdf->SetTextColor(0, 0, 0);
              $pdf->text($x-24.5, $y, Qr::quitar_tildes($value2->DESCRIPCION), false, false, true, 0, 1, '', false, '', 0);
              //Tamaño de fuente
              $pdf->SetFontSize(13);
              if($value2->MONEDA===1){
                $número_formato = number_format($value2->PRECIO);
                $pdf->text($x+28, $y+13, 'G$:'.$número_formato, false, false, true, 0, 1, '', false, '', 0);
              }elseif ($value2->MONEDA===2) {
                $pdf->SetFontSize(16);
                $pdf->text($x+28, $y+13, 'U$:'.$value2->PRECIO, false, false, true, 0, 1, '', false, '', 0);
                $pdf->SetFontSize(13);
              }elseif ($value2->MONEDA===4) {
                $pdf->SetFontSize(16);
                $pdf->text($x+28, $y+13, 'R$:'.$value2->PRECIO, false, false, true, 0, 1, '', false, '', 0);
                $pdf->SetFontSize(13);
              }elseif ($value2->MONEDA===3){
                $pdf->text($x+28, $y+13, 'P$:'.$value2->PRECIO, false, false, true, 0, 1, '', false, '', 0);
              }
              $pdf->SetFontSize(10);
              $pdf->text($x+28, $y+9, 'UNITARIO', false, false, true, 0, 1, '', false, '', 0);
              $pdf->SetFontSize(6.5);
              $pdf->text($x+43.5, $y+19.2, 'COD. INTERNO', false, false, true, 0, 1, '', false, '', 0);
              // //background
              $pdf->SetFontSize(28);
              $pdf->writeHTMLCell(51, 0, $x-23, $y+8,  '',1, 0, 1, 'C', true, 'J', true);

              $pdf->write1DBarcode($value2->CODIGO_INTERNO, $type, $x+43, $y+20.5, 30, 12, 0, $style, 'N');
              $pdf->write1DBarcode($value2->CODIGO, $type, $x-12.8, $y+20.5, 45, 12, 0, $style, 'N');
              //------------------------------------------------------------------------------------------------------
              //Color Blanco
              $pdf->SetTextColor(1000, 1000, 1000);
              //Tamaño de fuente
              $pdf->SetFontSize(10); 
              $pdf->text($x-22, $y+10, 'DESDE 6', false, false, true, 0, 1, '', false, '', 0);
              $pdf->text($x-16, $y+14, 'UNID.', false, false, true, 0, 1, '', false, '', 0);
              $pdf->SetFontSize(13);
              if($value2->MONEDA===1){
                $número_formato_may = number_format($value2->PRECIO_MAYORISTA);
                $pdf->text($x-4, $y+13, 'G$:'.$número_formato_may, false, false, true, 0, 1, '', false, '', 0);
              }elseif ($value2->MONEDA===2) {
                $pdf->SetFontSize(16);
                $pdf->text($x-4, $y+13, 'U$:'.$value2->PRECIO_MAYORISTA, false, false, true, 0, 1, '', false, '', 0);
                $pdf->SetFontSize(13);
              }elseif ($value2->MONEDA===4) {
                $pdf->SetFontSize(16);
                $pdf->text($x-4, $y+13, 'R$:'.$value2->PRECIO_MAYORISTA, false, false, true, 0, 1, '', false, '', 0);
                $pdf->SetFontSize(13);
              }elseif ($value2->MONEDA===3) {
                $pdf->text($x+28, $y+13, 'P$:'.$value2->PRECIO, false, false, true, 0, 1, '', false, '', 0);
              }
              $pdf->SetFontSize(10);
              $pdf->text($x-4, $y+9, 'MAYORISTA', false, false, true, 0, 1, '', false, '', 0);
              $pdf->SetFontSize(20);
              $pdf->SetFont('helvetica');
              $pdf->text($x-6.3, $y+7.5, '|', false, false, true, 0, 1, '', false, '', 0);
              $pdf->text($x-6.3, $y+11, '|', false, false, true, 0, 1, '', false, '', 0);

              $y=$y+28;
          }
        }
      }
      
      return $pdf->Output($name . ".pdf", 'D'); //D Download I Show
    } 


    public static function etiqueta_tipo_2($datos){
      $pdf = new TCPDF('L','mm',array(105,22));
      $pdf->SetPrintHeader(false);
      $pdf->SetPrintFooter(false);
      $pdf->addPage();
      // $datos['proveedor']['nombre']=strtoupper($datos['proveedor']['nombre']);


      $pdf->SetFont('helvetica', '', 6);
      $pag=1;
      $x=25;
      $y = 0.3;
      $z = 2;
      $c=0;
      while($datos['proveedor']['cantidad']>$c){
        $c=$c+1;
        if($x>97){
          $y=$y+27;
          if($y > 22){
            $pag=$pag+1;
            $pdf->AddPage();
            $y = 0.3;
          }
          $x=25;
        }
        // $html = 
        // '
        // <p style="font-size:5px"><b>'.$datos['proveedor']['nombre'].'</b><br>
        // <b>'.$datos['proveedor']['razon'].'</b><br>
        // '.$datos['proveedor']['direccion'].' '.$datos['proveedor']['ciudad'].'<br>
        // Telef: '.$datos['proveedor']['telefono'].'<br>
        // Fax: '.$datos['proveedor']['fax'].'<br>
        // RUC: '.$datos['proveedor']['ruc'].'<br>
        // Ciudad del Este - Paraguay
        // </p>
        // ';
        // $pdf->writeHTMLCell(0, 0, $x, $y, $html, 0, 0, 0, false, '', false);

        $pdf->SetFontSize(6); 
        $pdf->SetFont('freesans','B');
        $pdf->text($x-24.5, $y+1, strtoupper(utf8_decode(utf8_encode($datos['proveedor']['nombre']))), false, false, true);
        $pdf->SetFont('freesans','B');
        $pdf->text($x-24.5, $y+3, utf8_decode(utf8_encode($datos['proveedor']['razon'])), false, false, true);
        $pdf->SetFont('freesans');
        $pdf->SetFontSize(5.5);
        $pdf->text($x-24.5, $y+6, utf8_decode(utf8_encode($datos['proveedor']['direccion'])), false, false, true);
        $pdf->SetFontSize(6);
        $pdf->text($x-24.5, $y+8.5, "Telef.: ".utf8_decode(utf8_encode($datos['proveedor']['telefono'])), false, false, true);
        $pdf->text($x-24.5, $y+11, "Fax.: ".utf8_decode(utf8_encode($datos['proveedor']['fax'])), false, false, true);
        $pdf->text($x-24.5, $y+13.5, "RUC.: ".utf8_decode(utf8_encode($datos['proveedor']['ruc'])), false, false, true);
        $pdf->text($x-24.5, $y+16.5, utf8_decode(utf8_encode($datos['proveedor']['ciudad'])), false, false, true);

        //$y=$y+35;
        $x=$x+37-1.5;
      }
       return $pdf->Output("etiqueta_tipo_2" . ".pdf", 'D'); //D Download I Show
    }


    public static function etiqueta_tipo_3($datos){
      $user = auth()->user();
      $pdf = new TCPDF('L','mm',array(105,22));
      $pdf->SetPrintHeader(false);
      $pdf->SetPrintFooter(false);
      $pdf->addPage();

      $pdf->SetFont('helvetica', '', 6);
      $name = '111'; 
      $type = 'C128B';

      //definir estilo del Barcode 
      //-------------------------------------------------------------------------
      $style = array(
          'position' => '',
          'align' => 'N',
          'stretch' => true,
          'fitwidth' => false,
          'cellfitalign' => '',
          'border' => false, // border
          'hpadding' => 3,
          'vpadding' => 1.5,
          'fgcolor' => array(0, 0, 0),
          'bgcolor' => false, //array(255,255,255),
                   'text' => true, // whether to display the text below the barcode
                   'font' => 'courier', //font
                   'fontsize' => 9, //font size
          'stretchtext' => 4
      );
      $pag=1;
      $x=25;
      $y = 0.3;
      $z = 2;
      $c=0;
      if($datos['seleccionImpresion']<>'1'){
         foreach ($datos["data"] as $key => $value) {
          while($c<$value["CANTIDAD"]){
            $c=$c+1;
            if($x>97){
                 $y=$y+27;
                 if($y > 22){

                    $pag=$pag+1;
                    $pdf->AddPage();
        
                    $y = 0.3;
                }
           
            $x=25;
           }
          // $pdf->SetAutoPageBreak(FALSE, 0);
          if ($datos['codigo']==='2' and $datos['precio']<>'4'){
            $pdf->write1DBarcode($value["CODIGO"], $type, $x+1, $y, 32, 12, 0.2, $style, 'N');
          }else if($datos['precio']<>'4'){
            $pdf->write1DBarcode($value["CODIGO_INTERNO"], $type, $x+1.5, $y, 32, 12, 0.2, $style, 'N');
          }
           
          if($datos['precio']==='1'){
            $pdf->SetFontSize(8);
            $pdf->SetFont('','B');
            $pdf->text($x-14.5, $y+11, $value['PRECIO'], false, false, true);
          
          }else if($datos['precio']==='2'){
            $pdf->SetFontSize(8);
            $pdf->SetFont('','B');
            $pdf->text($x-14.5, $y+11, $value['PRECIO_MAYORISTA'], false, false, true);

          }else if($datos['precio']==='3'){
            $pdf->SetFontSize(8);
            $pdf->SetFont('','B');
            if($value['MONEDA']===2){
              $pdf->text($x-17.5, $y+10, $value['PRECIO']." / ".$value['PRECIO_MAYORISTA'], false, false, true);
            }else{
              $pdf->text($x-21, $y+10, $value['PRECIO']." / ".$value['PRECIO_MAYORISTA'], false, false, true);
            }                   
          }else if ($datos['precio']==='4') {
            $pdf->SetFontSize(12);
            $pdf->SetFont('','B');
            if($datos['tipomoneda']==='1'){
              if ($value['MONEDA']===1) {
                $pdf->text($x-18.5, $y+9, "G$ ".$value['PRECIO'], false, false, true);
              }else {
                $conversion = $datos['cotizacion']*$value['PRECIO'];
                $restar=strlen($conversion);
                if($restar>6){
                  $restar=$restar-3;
                }else{
                  $restar=0;
                }
                $pdf->text($x-18.5-$restar, $y+9, "G$ ".$conversion, false, false, true);
              }
              
            }elseif($datos['tipomoneda']==='2'){
              if ($value['MONEDA']===2) {
                $pdf->text($x-16.5, $y+9, "U$ ".$value['PRECIO'], false, false, true);
              }else{
                $conversion = $value['PRECIO']/$datos['cotizacion'];
                $restar=strlen($conversion);
                $pdf->text($x-16.5, $y+9, "U$ ".$conversion, false, false, true);
              }
            } 
          }
          $pdf->SetFontSize(5.7);
          $pdf->SetFont('freesans');
          if ($datos['precio']<>'4') {
            $pdf->text($x-23.8, $y+14, substr(Qr::quitar_tildes($value['DESCRIPCION']), 0,22), false, false, true, 0, 1, '', false, '', 0);
          }
           //$y=$y+35;

           $x=$x+37-1.5;
          }
          $c=0;
        }
      }else{
        foreach ($datos['seleccion_gondola'] as $key => $value) {
          $productos=DB::connection('retail')
                      ->table('gondola_tiene_productos AS GTP')
                      ->select(DB::raw('PRA.CODIGO, PR.DESCRIPCION, PRA.PREC_VENTA AS PRECIO, PRA.PREMAYORISTA AS PRECIO_MAYORISTA, PRA.CODIGO_INTERNO, PRA.MONEDA'))
                      ->leftjoin('productos_aux AS PRA', 'PRA.ID', '=', 'GTP.FK_PRODUCTOS_AUX')
                      ->leftjoin('productos AS PR', 'PR.CODIGO', '=', 'PRA.CODIGO')
                      ->where('GTP.ID_GONDOLA', '=', $value['ID'])
                      ->where('PRA.ID_SUCURSAL', '=', $user->id_sucursal);           
          if ($datos['tipoStock'] === '1') {
            $productos = $productos->whereRaw('(IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = PRA.CODIGO) AND (l.ID_SUCURSAL = PRA.ID_SUCURSAL))),0)) > 0');
          }else{
            $productos = $productos->whereRaw('(IFNULL((SELECT SUM(l.CANTIDAD) FROM lotes as l WHERE ((l.COD_PROD = PRA.CODIGO) AND (l.ID_SUCURSAL = PRA.ID_SUCURSAL))),0)) <= 0');
          }
          $productos = $productos->get()
                                 ->toArray();
           
          foreach ($productos as $key => $value2) {
            if($x>97){
              $y=$y+27;
              if($y > 22){
                $pag=$pag+1;
                $pdf->AddPage();
                $y = 0.3;
              }
              $x=25;
            }
            $pdf->write1DBarcode($value2->CODIGO, $type, $x+1, $y, 32, 12, 0.2, $style, 'N');
            $pdf->SetFontSize(8);
            $pdf->SetFont('','B');
            // if($value2->MONEDA===1){
            //     $pdf->SetFontSize(7);
            //     $número_formato_prec = number_format($value2->PRECIO);
            //     $número_formato_may = number_format($value2->PRECIO_MAYORISTA);
            //     $cantCaract=strlen($value2->PRECIO);
            //     if ($cantCaract>=7) {
            //       $pdf->SetFont('','B');
            //       $pdf->text($x-24, $y+10, "UNIT.: ", false, false, true);
            //       $pdf->text($x-24, $y+12.5, "MAY.: ", false, false, true);
            //       $pdf->SetFont('','L');
            //       $pdf->text($x-16.5, $y+10, $número_formato_prec." G$.", false, false, true);
            //       $pdf->text($x-16.5, $y+12.5, $número_formato_may." G$.", false, false, true);
            //     }else{
            //       $pdf->text($x-23.6, $y+10, $número_formato_prec."G$. / ".$número_formato_may."G$.", false, false, true);
            //     }
            // }elseif($value2->MONEDA===2){
            //   $pdf->text($x-19.5, $y+10, $value2->PRECIO."$ / ".$value2->PRECIO_MAYORISTA."$", false, false, true);
            // }else{
            //   $pdf->text($x-21, $y+10, $value2->PRECIO." / ".$value2->PRECIO_MAYORISTA, false, false, true);
            // }
            if($value2->MONEDA===2){
              $pdf->text($x-19.5, $y+10, $value2->PRECIO."$ / ".$value2->PRECIO_MAYORISTA."$", false, false, true);
            }else{
              $pdf->text($x-21, $y+10, $value2->PRECIO." / ".$value2->PRECIO_MAYORISTA, false, false, true);
            }
            $pdf->SetFontSize(5.7);
            $pdf->SetFont('freesans');
            $pdf->text($x-23.8, $y+16, substr(Qr::quitar_tildes($value2->DESCRIPCION), 0,22), false, false, true, 0, 1, '', false, '', 0);
            //$y=$y+35;
            $x=$x+37-1.5;
          }
        }
      }
      return $pdf->Output($name . ".pdf", 'D'); //D Download I Show
    }

    public static function etiqueta_tipo_4($datos){
      $name = '111'; 
      $type = 'C128B';

      //definir estilo del Barcode 
      //-------------------------------------------------------------------------
        $style = array(
            'position' => '',
            'align' => 'N',
            'stretch' => true,
            'fitwidth' => false,
            'cellfitalign' => '',
            'border' => false, // border
            'hpadding' => 3,
            'vpadding' => 1.5,
            'fgcolor' => array(0, 0, 0),
            'bgcolor' => false, //array(255,255,255),
                     'text' => true, // whether to display the text below the barcode
                     'font' => 'helvetica', //font
                     'fontsize' => 6, //font size
            'stretchtext' => 4
        );
      $pdf = new TCPDF('L','mm',array(57,28));
      $pdf->SetPrintHeader(false);
      $pdf->SetPrintFooter(false);
      $pdf->addPage();


      $pdf->SetFont('helvetica', '', 6);
      $pag=1;
      $x=25;
      $y = 0.3;
      $z = 2;
      $c=0;
      
      foreach ($datos["data"] as $key => $value) {
        while($c<$value["CANTIDAD"]){
          $c=$c+1;
          if($y > 28){
            $pag=$pag+1;
            $pdf->AddPage();
            $y = 0.3;
          }
          $pdf->SetFontSize(7);
          $pdf->SetFont('freesans', 'B'); 

          $pdf->text($x-23.8, $y+2, substr(Qr::quitar_tildes($value['DESCRIPCION']), 0,34), false, false, true, 0, 1, '', false, '', 0);
          if ($datos['precio']==='1') {
            $pdf->SetFontSize(10);
            $pdf->text($x-9, $y+6, "U$ ".$value['PRECIO'], false, false, true);
            $pdf->SetFontSize(7);
            if ($datos['codigo']==='2'){
              $pdf->write1DBarcode($value["CODIGO"], $type, $x+6, $y+10, 55, 12, 0.2, $style, 'N');
            }else{
              $pdf->write1DBarcode($value["CODIGO_INTERNO"],  $type, $x+6, $y+10, 55, 12, 0.2, $style, 'N');
            }
          }else{
            if ($datos['codigo']==='2'){
              $pdf->write1DBarcode($value["CODIGO"], $type, $x+6, $y+6, 55, 12, 0.2, $style, 'N');
            }else{
              $pdf->write1DBarcode($value["CODIGO_INTERNO"],  $type, $x+6, $y+6, 55, 12, 0.2, $style, 'N');
            }
          }
          
          
          $y=$y+28;
        }

        $c=0;
      }
        return $pdf->Output($name . ".pdf", 'D'); //D Download I Show
    } 

      public static function crear_pdf_CajaCompra_qr($datos)
    {
      

          $file=public_path('qr.png');
     
          $pdf = new FPDF('L','mm',array(50,50));
          $pdf->AddPage();
          $x=0;
          $y = 0;
          $c=0;
          Qr::crear_qr($datos["data"],2);

            /*$pdf->Text(5,8,$datos["data"]);*/
          $file=public_path($datos["data"].'.png');
          $pdf->Image($file,$x,$y,50,50);
           
          File::delete($datos["data"].'.png');
      


     




        return $pdf->Output('CajaCompraqr.pdf','i');

        /*  --------------------------------------------------------------------------------- */

    }
          public static function crear_pdf_CajaTransferencia_qr($datos)
    {
      

          $file=public_path('qr.png');
     
          $pdf = new FPDF('L','mm',array(50,50));
          $pdf->AddPage();
          $x=0;
          $y = 0;
          $c=0;
          Qr::crear_qr($datos["data"],3);

            /*$pdf->Text(5,8,$datos["data"]);*/
          $file=public_path($datos["data"].'.png');
          $pdf->Image($file,$x,$y,50,50);
           
          File::delete($datos["data"].'.png');
      


     




        return $pdf->Output('CajaCompraqr.pdf','i');

        /*  --------------------------------------------------------------------------------- */

    }
    


    //-------------------------CODIGO INTERNO---------------------------------------------------------------------------------
//      public static function crear_barinterno($datos)
//     {
//     $name = '111'; 
//     $type = 'C128B';

//     $pdf = new TCPDF('L','mm',array(105,22));
//     $pdf->SetPrintHeader(false);
//     $pdf->SetPrintFooter(false);
//     $pdf->addPage();

//     $pdf->SetFont('helvetica', '', 6);
//  //definir estilo del Barcode 
//     //-------------------------------------------------------------------------
//         $style = array(
//             'position' => '',
//             'align' => 'C',
//             'stretch' => true,
//             'fitwidth' => true,
//             'cellfitalign' => '',
//             'border' => false, // border
//             'hpadding' => 'auto',
//             'vpadding' => 'auto',
//             'fgcolor' => array(0, 0, 0),
//             'bgcolor' => false, //array(255,255,255),
//                      'text' => true, // whether to display the text below the barcode
//                      'font' => 'helvetica', //font
//                      'fontsize' => 6, //font size
//             'stretchtext' => 4
//         );


//         //definir la posición del primer bardoce
//     $pag=1;
//        $x=25;
//        $y = 3;
//        $z = 2;
//        $c=0;
// //-----------------------------------------------------------------------------------------------------------------

//           //proceso del codigo de Barra
//        //--------------------------------------------------------------------------------------------------------
//       /* $codigos = DB::connection('retail')
//         ->table('productos_aux')
//         ->select(DB::raw(
//                         'CODIGO_interno AS CODIGO'
//                     ))
                 
//         ->where('productos_aux.ID_SUCURSAL','=', 9)
//         ->where('productos_aux.CODIGO_INTERNO','like', '9-%')
//         ->orderby('CODIGO')
//         ->limit(52)->get()->toArray();
// */
//         foreach ($datos["data"] as $key => $value) {
//           while($c<$value["CANTIDAD"]){
//             $c=$c+1;
//                        if($x>97){
//              $y=$y+27;
//              if($y > 22){

//                 $pag=$pag+1;
//                 $pdf->AddPage();
    
//                 $y = 3;
//             }
           
//             $x=25;
//            }
//            $pdf->write1DBarcode($value["CODIGO_INTERNO"], $type, $x, $y, 30, 12, 0.2, $style, 'N');
           
            
//            //$y=$y+35;

//            $x=$x+37-1.5;
//           }
//         $c=0;
     
      
//         }
     
// /*        $pdf->write1DBarcode($fn_sku, $type, $x + 48.7, $y + $i * 25, 44.2, 14.4, 0.4, $style, 'N');
//         $pdf->write1DBarcode($fn_sku, $type, $x + 48.7 * 2, $y + $i * 25, 44.2, 14.4, 0.4, $style, 'N');*/
//   /*    $pdf->Text($z, 18 ,'      '.$fn_sku);*/
//                  //The second line fn_sku
// /*        
//         $pdf->Text($z + 48.7, $y + $i * 25 + 13, '   ' . $fn_sku);
//         $pdf->Text($z + 48.7 * 2, $y + $i * 25 + 13, '   ' . $fn_sku);
//         $pdf->Text($z + 48.7 * 3, $y + $i * 25 + 13, '   ' . $fn_sku);
//                  //third line title
//         $pdf->Text($x, $y + $i * 25 + 15, '   ' . $title);
//         $pdf->Text($x + 48.7, $y + $i * 25 + 15, '   ' . $title);
//         $pdf->Text($x + 48.7 * 2, $y + $i * 25 + 15, '   ' . $title);
//         $pdf->Text($x + 48.7 * 3, $y + $i * 25 + 15, '   ' . $title);
//                  //fourth line
//         $pdf->Text($z, $y + $i * 25 + 17, "(MADE IN CHINA)");
//         $pdf->Text($z + 48.7, $y + $i * 25 + 17, "(MADE IN CHINA)");
//         $pdf->Text($z + 48.7 * 2, $y + $i * 25 + 17, "(MADE IN CHINA)");
//         $pdf->Text($z + 48.7 * 3, $y + $i * 25 + 17, "(MADE IN CHINA)");*/
  
//         return $pdf->Output($name . ".pdf", 'i'); //D Download I Show
        

//     }
    public static function quitar_tildes($cadena) {
      $no_permitidas= array ("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","À","Ã","Ì","Ò","Ù","Ã™","Ã ","Ã¨","Ã¬","Ã²","Ã¹","ç","Ç","Ã¢","ê","Ã®","Ã´","Ã»","Ã‚","ÃŠ","ÃŽ","Ã”","Ã›","ü","Ã¶","Ã–","Ã¯","Ã¤","«","Ò","Ã","Ã„","Ã‹");
      $permitidas= array ("a","e","i","o","u","A","E","I","O","U","n","N","A","E","I","O","U","a","e","i","o","u","c","C","a","e","i","o","u","A","E","I","O","U","u","o","O","i","a","e","U","I","A","E");
      $texto = str_replace($no_permitidas, $permitidas ,$cadena);
      return $texto;
    }
           public static function crear_etiqueta_gondola()
    {
$pdf = new TCPDF('L','mm',array(90,48));

// set document information

$descripcion="ALI ALC CERVEZA 7818";
$descriM="U$";
$precio=2.36;
$cod_prod='4909512345612';
// Add a page
// This method has several options, check the source code documentation for more information.
 $style = array(
            'position' => '',
            'align' => 'C',
            'stretch' => true,
            'fitwidth' => true,
            'cellfitalign' => '',
            'border' => false, // border
            'hpadding' => 'auto',
            'vpadding' => 'auto',
            'fgcolor' => array(0, 0, 0),
            'bgcolor' => false, //array(255,255,255),
                     'text' => true, // whether to display the text below the barcode
                     'font' => 'helvetica', //font
                     'fontsize' => 6, //font size
            'stretchtext' => 4
        );
            $type = 'C128B';
$pdf->SetPrintHeader(false);
$pdf->SetPrintFooter(false);
$pdf->AddPage();

// set text shadow effect
/*$pdf->setTextShadow(array('enabled'=>true, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));*/

// Set some content to 



$html = 
'<h3 style="margin-left: 30px">'.substr($descripcion,0,28).' <a style = "font-family:helvetica;</a>"</h3>';



// Print text using writeHTMLCell()
$pdf->writeHTMLCell(0, 0, 5, 3, $html, 0, 0, 0, false, '', false);

$html = 
'
<table border=1 cellspacing="1" >
<tr  align="left">
<td bgcolor="#000000" align="right" style="color:white" width="55"><p>DESDE 6 UNID.</p></td>
<td  width="1" bgcolor="#FFFFFF"><BR></td>
<td bgcolor="#000000"  width="71"  align="left">
<p style="color:white">MAYORISTA '.$descriM.' '.$precio.'</p>
</td>
<td bgcolor="#FFFFFF" width="65"  align="left">
<p  style="color:black">   UNITARIO&nbsp;&nbsp;'.$descriM.' '.$precio.'</p>
</td>
</tr>
</table>
';

/*$html .= '<link href="//netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap.min.css" 
      rel="stylesheet">';*/

$pdf->writeHTMLCell(0, 0, 4, 10, $html, 0, 0, 0, false, '', false);

$htmldesc='<html>
<style>
</style>
<body>
<table style="width:32%">
  <tr>
    <th></th>
  </tr>
  <tr>
    <td>'.$value['NOMBRE'].'</td>
  </tr>
</table>
</body>
</html>';
  
// ---------------------------------------------------------

// Close and output PDF document
// This method has several options, check the source code documentation for more information.


$pdf->Output('example_001.pdf', 'I');
        

    }
}

/*<p><a  style="text-decoration:none;background-color:#000000;color:white;"><span style="color:WHITE;">DESDE &nbsp; 6<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;UNID.</span></a></p>*/
/*<table width="140"  cellspacing="0" cellpadding="0" border="0" bgcolor="#000000">
<tr>
   <td bgcolor="#000000" style="border-rigth: 1px solid white; padding: 5px;">
   <font face="arial, verdana, helvetica" style="color:white">DESDE 6  MAYORISTA
   </font>
   <font face="arial, verdana, helvetica" style="color:white">&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; UNID.| '.$descriM.': '.$precio.'
   </font>
   </td>
</tr>
</table>*/