<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Qr;
class QrController extends Controller
{
    //CrearPdfCompraCaja
      public function Crear(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER TODOS LOS DATOS DEL PRODUCTO

/*      $file=public_path('qr.png');
       return \QRCode::text('QR Code Generator for Laravel! xD')->setOutfile($file)->png();*/

        $qr = Qr::crear_pdf_qr_2($request->all());
        return response()->json($qr);
        
        /*  --------------------------------------------------------------------------------- */

    } 
          public function Crear_Barcode(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER TODOS LOS DATOS DEL PRODUCTO

/*      $file=public_path('qr.png');
       return \QRCode::text('QR Code Generator for Laravel! xD')->setOutfile($file)->png();*/

       $barcode = Qr::crear_barcode($request->all());
        return response()->json($barcode);
        
        /*  --------------------------------------------------------------------------------- */

    }
              public function Crear_Barinterno(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER TODOS LOS DATOS DEL PRODUCTO

/*      $file=public_path('qr.png');
       return \QRCode::text('QR Code Generator for Laravel! xD')->setOutfile($file)->png();*/

       $barcode = Qr::crear_barinterno($request->all());
        return response()->json($barcode);
        
        /*  --------------------------------------------------------------------------------- */

    }  
              public function Crear_Etiqueta_Gondola(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER TODOS LOS DATOS DEL PRODUCTO

/*      $file=public_path('qr.png');
       return \QRCode::text('QR Code Generator for Laravel! xD')->setOutfile($file)->png();*/

       $gondola = Qr::crear_etiqueta_gondola();
        return response()->json($gondola);
        
        /*  --------------------------------------------------------------------------------- */

    } 
    public function Crear_Pdf_Compra_Caja_Qr(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER TODOS LOS DATOS DEL PRODUCTO

/*      $file=public_path('qr.png');
       return \QRCode::text('QR Code Generator for Laravel! xD')->setOutfile($file)->png();*/

       $CompraCaja = Qr::crear_pdf_CajaCompra_qr($request->all());
        return response()->json($CompraCaja);
        
        /*  --------------------------------------------------------------------------------- */

    } 
        public function Crear_Pdf_Transferencia_Caja_Qr(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER TODOS LOS DATOS DEL PRODUCTO

/*      $file=public_path('qr.png');
       return \QRCode::text('QR Code Generator for Laravel! xD')->setOutfile($file)->png();*/

       $TransferenciaCaja = Qr::crear_pdf_CajaTransferencia_qr($request->all());
        return response()->json($TransferenciaCaja);
        
        /*  --------------------------------------------------------------------------------- */

    } 
    
}
