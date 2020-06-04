<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Servicios;

class ServicioController extends Controller
{

    public function obtenerServicioPOS(Request $request)
    {
        
        /*  --------------------------------------------------------------------------------- */

        // GUARDAR PRODUCTO 

        $servicios = Servicios::servicios_pos($request->all());
        return response()->json($servicios);
        
        /*  --------------------------------------------------------------------------------- */

    }
}
