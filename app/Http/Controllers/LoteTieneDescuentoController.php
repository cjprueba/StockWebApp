<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\LoteTieneDescuento;

class LoteTieneDescuentoController extends Controller
{
    
    public function guardarDescuentoLote(Request $request){
        
        /*  --------------------------------------------------------------------------------- */

        // MOSTRAR IMPORTAR

        $descuentoLote = LoteTieneDescuento::guardarLoteDescuento($request);
        return response()->json($descuentoLote);

        /*  --------------------------------------------------------------------------------- */
    }
}
