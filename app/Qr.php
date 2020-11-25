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
     $c=0;


       $file=public_path('qr.png');
        /*  --------------------------------------------------------------------------------- */
/*       var_dump($datos);*/
        $pag=1;
          $pdf = new FPDF('L','mm',array(110,31));
          $pdf->AddPage();
          $x=10;
         $y = 1;
      foreach ($datos["data"] as $key => $value) {
        # code...
           
               while ($c<$value["CANTIDAD"]){
                  $c=$c+1;
                      if($x>77){
                         $y=$y+31;
                          if($y > 31){

                           $pag=$pag+1;
                           $pdf->AddPage();
    
                              $y =1;
                      }
           
                    $x=10;
             }

              
             Qr::crear_qr($value["CODIGO"]);

             $file=public_path(''.$value["CODIGO"].'.png');
           $pdf->Image($file,$x,$y,28,28);
           File::delete(''.$value["CODIGO"].'.png');
           //$y=$y+35;
           $x=$x+60-2;
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
         public static function crear_qr($codigo)
    {
     
        /*  --------------------------------------------------------------------------------- */
        $file=public_path(''.$codigo.'.png');
       return \QRCode::text('http://131.196.192.165:8080/productoqr?s=9&c='.$codigo)->setOutfile($file)->png();

        /*  --------------------------------------------------------------------------------- */

    }
       public static function crear_barcode($datos)
    {
    $name = '111'; 
    $type = 'C128B';

    $pdf = new TCPDF('L','mm',array(105,22));
    $pdf->SetPrintHeader(false);
    $pdf->SetPrintFooter(false);
    $pdf->addPage();

    $pdf->SetFont('helvetica', '', 6);
 //definir estilo del Barcode 
    //-------------------------------------------------------------------------
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


        //definir la posición del primer bardoce
    $pag=1;
       $x=25;
       $y = 3;
       $z = 2;
       $c=0;
//-----------------------------------------------------------------------------------------------------------------

          //proceso del codigo de Barra
       //--------------------------------------------------------------------------------------------------------
      /* $codigos = DB::connection('retail')
        ->table('productos_aux')
        ->select(DB::raw(
                        'CODIGO_interno AS CODIGO'
                    ))
                 
        ->where('productos_aux.ID_SUCURSAL','=', 9)
        ->where('productos_aux.CODIGO_INTERNO','like', '9-%')
        ->orderby('CODIGO')
        ->limit(52)->get()->toArray();
*/
        foreach ($datos["data"] as $key => $value) {
          while($c<$value["CANTIDAD"]){
            $c=$c+1;
                       if($x>97){
             $y=$y+27;
             if($y > 22){

                $pag=$pag+1;
                $pdf->AddPage();
    
                $y = 3;
            }
           
            $x=25;
           }
           $pdf->write1DBarcode($value["CODIGO"], $type, $x, $y, 30, 14.4, 0.2, $style, 'N');
           
            
           //$y=$y+35;

           $x=$x+37-1.5;
          }
        $c=0;
     
      
        }
     
/*        $pdf->write1DBarcode($fn_sku, $type, $x + 48.7, $y + $i * 25, 44.2, 14.4, 0.4, $style, 'N');
        $pdf->write1DBarcode($fn_sku, $type, $x + 48.7 * 2, $y + $i * 25, 44.2, 14.4, 0.4, $style, 'N');*/
  /*    $pdf->Text($z, 18 ,'      '.$fn_sku);*/
                 //The second line fn_sku
/*        
        $pdf->Text($z + 48.7, $y + $i * 25 + 13, '   ' . $fn_sku);
        $pdf->Text($z + 48.7 * 2, $y + $i * 25 + 13, '   ' . $fn_sku);
        $pdf->Text($z + 48.7 * 3, $y + $i * 25 + 13, '   ' . $fn_sku);
                 //third line title
        $pdf->Text($x, $y + $i * 25 + 15, '   ' . $title);
        $pdf->Text($x + 48.7, $y + $i * 25 + 15, '   ' . $title);
        $pdf->Text($x + 48.7 * 2, $y + $i * 25 + 15, '   ' . $title);
        $pdf->Text($x + 48.7 * 3, $y + $i * 25 + 15, '   ' . $title);
                 //fourth line
        $pdf->Text($z, $y + $i * 25 + 17, "(MADE IN CHINA)");
        $pdf->Text($z + 48.7, $y + $i * 25 + 17, "(MADE IN CHINA)");
        $pdf->Text($z + 48.7 * 2, $y + $i * 25 + 17, "(MADE IN CHINA)");
        $pdf->Text($z + 48.7 * 3, $y + $i * 25 + 17, "(MADE IN CHINA)");*/
  
        return $pdf->Output($name . ".pdf", 'I'); //D Download I Show
        

    }
     public static function crear_barinterno($datos)
    {
    $name = '111'; 
    $type = 'C128B';

    $pdf = new TCPDF('L','mm',array(105,22));
    $pdf->SetPrintHeader(false);
    $pdf->SetPrintFooter(false);
    $pdf->addPage();

    $pdf->SetFont('helvetica', '', 6);
 //definir estilo del Barcode 
    //-------------------------------------------------------------------------
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


        //definir la posición del primer bardoce
    $pag=1;
       $x=25;
       $y = 3;
       $z = 2;
       $c=0;
//-----------------------------------------------------------------------------------------------------------------

          //proceso del codigo de Barra
       //--------------------------------------------------------------------------------------------------------
      /* $codigos = DB::connection('retail')
        ->table('productos_aux')
        ->select(DB::raw(
                        'CODIGO_interno AS CODIGO'
                    ))
                 
        ->where('productos_aux.ID_SUCURSAL','=', 9)
        ->where('productos_aux.CODIGO_INTERNO','like', '9-%')
        ->orderby('CODIGO')
        ->limit(52)->get()->toArray();
*/
        foreach ($datos["data"] as $key => $value) {
          while($c<$value["CANTIDAD"]){
            $c=$c+1;
                       if($x>97){
             $y=$y+27;
             if($y > 22){

                $pag=$pag+1;
                $pdf->AddPage();
    
                $y = 3;
            }
           
            $x=25;
           }
           $pdf->write1DBarcode($value["CODIGO_INTERNO"], $type, $x, $y, 30, 14.4, 0.2, $style, 'N');
           
            
           //$y=$y+35;

           $x=$x+37-1.5;
          }
        $c=0;
     
      
        }
     
/*        $pdf->write1DBarcode($fn_sku, $type, $x + 48.7, $y + $i * 25, 44.2, 14.4, 0.4, $style, 'N');
        $pdf->write1DBarcode($fn_sku, $type, $x + 48.7 * 2, $y + $i * 25, 44.2, 14.4, 0.4, $style, 'N');*/
  /*    $pdf->Text($z, 18 ,'      '.$fn_sku);*/
                 //The second line fn_sku
/*        
        $pdf->Text($z + 48.7, $y + $i * 25 + 13, '   ' . $fn_sku);
        $pdf->Text($z + 48.7 * 2, $y + $i * 25 + 13, '   ' . $fn_sku);
        $pdf->Text($z + 48.7 * 3, $y + $i * 25 + 13, '   ' . $fn_sku);
                 //third line title
        $pdf->Text($x, $y + $i * 25 + 15, '   ' . $title);
        $pdf->Text($x + 48.7, $y + $i * 25 + 15, '   ' . $title);
        $pdf->Text($x + 48.7 * 2, $y + $i * 25 + 15, '   ' . $title);
        $pdf->Text($x + 48.7 * 3, $y + $i * 25 + 15, '   ' . $title);
                 //fourth line
        $pdf->Text($z, $y + $i * 25 + 17, "(MADE IN CHINA)");
        $pdf->Text($z + 48.7, $y + $i * 25 + 17, "(MADE IN CHINA)");
        $pdf->Text($z + 48.7 * 2, $y + $i * 25 + 17, "(MADE IN CHINA)");
        $pdf->Text($z + 48.7 * 3, $y + $i * 25 + 17, "(MADE IN CHINA)");*/
  
        return $pdf->Output($name . ".pdf", 'i'); //D Download I Show
        

    }
           public static function crear_etiqueta_gondola()
    {
$pdf = new TCPDF('L','mm',array(95,48));

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
<table cellspacing="1" >
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