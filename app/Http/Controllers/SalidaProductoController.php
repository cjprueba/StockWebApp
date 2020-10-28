<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SalidaProducto;
class SalidaProductoController extends Controller
{
    //

        public function salida(Request $request)
    {
        /*  --------------------------------------------------------------------------------- */

        // REALIZAR PAGO

        $salida = SalidaProducto::salida($request->all());
        return response()->json($salida);

        /*  --------------------------------------------------------------------------------- */

    }
}
