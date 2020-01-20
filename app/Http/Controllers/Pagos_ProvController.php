<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Pagos_Prov;

class Pagos_ProvController extends Controller
{
    public function datatable(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // MOSTRAR DATATABLE

        $pago = Pagos_Prov::pago_datatable($request);
        return response()->json($pago);

        /*  --------------------------------------------------------------------------------- */

    }

    public function pagoUnico(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // MOSTRAR DATATABLE

        $pago = Pagos_Prov::pagoUnico($request->all());
        return response()->json($pago);

        /*  --------------------------------------------------------------------------------- */

    }
}
