<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Temp_venta;

use Illuminate\Support\Facades\DB;

class Temp_ventaController extends Controller
{
    //
        public function devolucionProductos(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // MOSTRAR IMPORTAR

        $insert = Temp_venta::insertar_reporte($request);
        return response()->json($insert);

        /*  --------------------------------------------------------------------------------- */

    }
}
