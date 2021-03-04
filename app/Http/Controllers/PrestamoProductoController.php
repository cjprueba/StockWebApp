<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\PrestamoProducto;

class PrestamoProductoController extends Controller
{
    //
     public function prestar(Request $request)
    {
        /*  --------------------------------------------------------------------------------- */

        // REALIZAR PAGO

        $prestamo = PrestamoProducto::prestar($request->all());
        return response()->json($prestamo);

        /*  --------------------------------------------------------------------------------- */

    }
        public function prestamoMostrar(Request $request)
    {
        /*  --------------------------------------------------------------------------------- */

        // MOSTRAR PROVEEDORES 

        $prestamo = PrestamoProducto::prestamoMostrar($request);
        return response()->json($prestamo);

        /*  --------------------------------------------------------------------------------- */

    }
        public function prestamoProductoDetalle(Request $request)
    {
        /*  --------------------------------------------------------------------------------- */

        // MOSTRAR PROVEEDORES 

        $prestamo = PrestamoProducto::prestamoProductoDetalle($request);
        return response()->json($prestamo);

        /*  --------------------------------------------------------------------------------- */

    }

        public function reporte(Request $request)
    {
        /*  --------------------------------------------------------------------------------- */

        // MOSTRAR PROVEEDORES 

        $prestamo = PrestamoProducto::reporte($request);
        return response()->json($prestamo);

        /*  --------------------------------------------------------------------------------- */

    }

        public function devolver(Request $request)
    {
        /*  --------------------------------------------------------------------------------- */

        // REALIZAR PAGO

        $prestamo = PrestamoProducto::devolver($request->all());
        return response()->json($prestamo);

        /*  --------------------------------------------------------------------------------- */

    }
}
