<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Marca;

class MarcaController extends Controller
{
    public function obtenerMarcas(){
        
        /*  --------------------------------------------------------------------------------- */

        // OBTENER MARCAS

        $marcas = Marca::obtener_marcas();
        return response()->json($marcas);

        /*  --------------------------------------------------------------------------------- */
    }
}
