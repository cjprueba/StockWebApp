<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Deuda;

class DeudaController extends Controller
{
    public function deuda_datatable(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // MOSTRAR DATATABLE

        $deuda = Deuda::deuda_datatable($request);
        return response()->json($deuda);

        /*  --------------------------------------------------------------------------------- */

    }

    public function deuda_compra_datatable(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // MOSTRAR DATATABLE

        $deuda = Deuda::deuda_compra_datatable($request);
        return response()->json($deuda);

        /*  --------------------------------------------------------------------------------- */

    }

    public function datos_nota(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // MOSTRAR DATATABLE

        $deuda = Deuda::datos_nota($request->all());
        return response()->json($deuda);

        /*  --------------------------------------------------------------------------------- */

    }
}
