<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SalidaProducto;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\Reportes\SalidaProductos\SalidaProductosExport;
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

        public function devolver(Request $request)
    {
        /*  --------------------------------------------------------------------------------- */

        // REALIZAR PAGO

        $salida = SalidaProducto::devolver($request->all());
        return response()->json($salida);

        /*  --------------------------------------------------------------------------------- */

    }
                public function descargarSalida(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // DESCARGAR REPORTE PROVEEDORES 

    
         
        return Excel::download(new SalidaProductosExport($request->all()), 'SalidaProducto.xlsx');

        /*  --------------------------------------------------------------------------------- */

    }
            public function reporteSalidaProductos(Request $request)
    {
        /*  --------------------------------------------------------------------------------- */

        // REALIZAR PAGO

        $salida = SalidaProducto::generarReporteSalidaProductos($request->all());
        return response()->json($salida);

        /*  --------------------------------------------------------------------------------- */

    }
    
}
