<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ComprasDet;

class ComprasDetController extends Controller
{

    public function obtenerProductosNroCaja(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // MOSTRAR CABECERA DE TRANSFERENCIA 

        $comprasDet = ComprasDet::productos_nro_caja($request->all());
        return response()->json($comprasDet);

        /*  --------------------------------------------------------------------------------- */

    }

}
