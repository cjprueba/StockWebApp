<?php

namespace App\Http\Controllers\FactExp;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\FactExp\FacturaExportacion;
class FacturaExportacionController extends Controller
{
    //
        public function factura(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        return FacturaExportacion::factura_pdf($request->all());

        /*  --------------------------------------------------------------------------------- */

    }
}
