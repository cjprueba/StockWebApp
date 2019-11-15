<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Talle;

class TalleController extends Controller
{
    public function obtenerTalles(){
        
        /*  --------------------------------------------------------------------------------- */

        // CONTAR PRODUCTOS

        $talle = Talle::obtener_talles();
        return response()->json($talle);

        /*  --------------------------------------------------------------------------------- */
    }
}
