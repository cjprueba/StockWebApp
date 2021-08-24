<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Fpdf\Fpdf;
use App\Barcode;
use Picqer\Barcode\BarcodeGeneratorPNG;
use TCPDF;

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
           return \QRCode::text('http://131.196.192.165:8080/productoqr?s='.$user->id_sucursal.'&c='.$codigo)->setOutfile($file)->png();
         }elseif ($tipo===2) {
            return \QRCode::text('http://131.196.192.165:8080/cajaqr?s='.$user->id_sucursal.'&c='.$codigo.'&t=1')->setOutfile($file)->png();
         }elseif ($tipo===3){
            return \QRCode::text('http://131.196.192.165:8080/cajaqr?s='.$user->id_sucursal.'&c='.$codigo.'&t=2')->setOutfile($file)->png();
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
      }     
    }


    public static function etiqueta_tipo_1($datos){
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
                  if ($datos['codigo']==='2'){
                    $pdf->write1DBarcode($value["CODIGO"], $type, $x+1, $y, 32, 12, 0.2, $style, 'N');
                  }else{
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
                  }
                  $pdf->SetFontSize(5.7);
                  $pdf->SetFont('freesans');
                  $pdf->text($x-23.8, $y+14, substr(utf8_decode(utf8_encode(utf8_decode(utf8_encode($value['DESCRIPCION']))), 0,22), false, false, true, 0, 1, '', false, '', 0); 

                    
                   //$y=$y+35;

                   $x=$x+37-1.5;
                  }
        $c=0;
     
      
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
          $pdf->text($x-23.8, $y+2, utf8_decode(utf8_encode($value['DESCRIPCION'])), false, false, true, 0, 1, '', false, '', 0);

          if ($datos['codigo']==='2'){
            $pdf->write1DBarcode($value["CODIGO"], $type, $x+6, $y+9, 55, 12, 0.2, $style, 'N');
          }else{
            $pdf->write1DBarcode($value["CODIGO_INTERNO"],  $type, $x+6, $y+9, 55, 12, 0.2, $style, 'N');
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