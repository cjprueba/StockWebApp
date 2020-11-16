<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Movimiento_Caja;
class Movimiento_CajaController extends Controller
{

    //
        public function guardarMovimiento(Request $request){
        
        /*  --------------------------------------------------------------------------------- */

        // OBTENER MARCAS

        $movimiento = Movimiento_Caja::guardar_movimiento($request->all());
        return response()->json($movimiento);

        /*  --------------------------------------------------------------------------------- */
    }
}
