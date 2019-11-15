<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Gondola;

class GondolaController extends Controller
{
    public function obtenerGondolas(){
        
        /*  --------------------------------------------------------------------------------- */

        // OBTENER GONDOLAS

        $gondolas = Gondola::obtener_gondolas();
        return response()->json($gondolas);

        /*  --------------------------------------------------------------------------------- */
    }
}
