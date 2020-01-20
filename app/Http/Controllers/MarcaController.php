<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Marca;

class MarcaController extends Controller
{
    public function obtenerMarcas(Request $request){
        
        /*  --------------------------------------------------------------------------------- */

        // OBTENER MARCAS

        $marcas = Marca::obtener_marcas($request->all());
        return response()->json($marcas);

        /*  --------------------------------------------------------------------------------- */
    }
}
