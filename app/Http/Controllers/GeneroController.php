<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Genero;

class GeneroController extends Controller
{
    public function obtenerGeneros(){
        
        /*  --------------------------------------------------------------------------------- */

        // CONTAR PRODUCTOS

        $genero = Genero::obtener_generos();
        return response()->json($genero);

        /*  --------------------------------------------------------------------------------- */
    }
}
