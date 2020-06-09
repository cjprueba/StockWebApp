<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Caja;

class CajaController extends Controller
{
	public function obtenerCaja(Request $request)
	{

        /*  --------------------------------------------------------------------------------- */

       // OBTENER TODOS LOS DATOS DEL TALLE

        $caja = Caja::caja_obtener($request->all());
        return response()->json($caja);
        
        /*  --------------------------------------------------------------------------------- */

    }
}
