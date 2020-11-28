<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Sucursal_Rm;
class Sucursal_RmController extends Controller
{
    //
     public function Crear(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER TODOS LOS DATOS DEL PRODUCTO

/*      $file=public_path('qr.png');
       return \QRCode::text('QR Code Generator for Laravel! xD')->setOutfile($file)->png();*/

        $sucursal = Sucursal_Rm::crear_sucursal($request->all());
        return response()->json($qr);
        
        /*  --------------------------------------------------------------------------------- */

    } 
}
