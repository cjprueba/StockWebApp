<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Color;

class ColorController extends Controller
{
    public function obtenerColores(){
        
        /*  --------------------------------------------------------------------------------- */

        // CONTAR PRODUCTOS

        $color = Color::obtener_colores();
        return response()->json($color);

        /*  --------------------------------------------------------------------------------- */
    }
}
