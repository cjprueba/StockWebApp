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
        public function salidaMostrar(Request $request)
    {
        /*  --------------------------------------------------------------------------------- */

        // MOSTRAR PROVEEDORES 

        $salida = SalidaProducto::salidaMostrar($request);
        return response()->json($salida);

        /*  --------------------------------------------------------------------------------- */

    }
        public function salidaProductoDetalle(Request $request)
    {
        /*  --------------------------------------------------------------------------------- */

        // MOSTRAR PROVEEDORES 

        $salida = SalidaProducto::salidaProductoDetalle($request);
        return response()->json($salida);

        /*  --------------------------------------------------------------------------------- */

    }

        public function reporte(Request $request)
    {
        /*  --------------------------------------------------------------------------------- */

        // MOSTRAR PROVEEDORES 

        $salida = SalidaProducto::reporte($request);
        return response()->json($salida);

        /*  --------------------------------------------------------------------------------- */

    }
}
