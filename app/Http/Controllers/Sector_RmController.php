<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Sector_Rm;
class Sector_RmController extends Controller
{
    //
         public function Crear(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER TODOS LOS DATOS DEL PRODUCTO

/*      $file=public_path('qr.png');
       return \QRCode::text('QR Code Generator for Laravel! xD')->setOutfile($file)->png();*/

        $sector = Sector_Rm::crear_sector($request->all());
        return response()->json($qr);
        
        /*  --------------------------------------------------------------------------------- */

    } 

}
