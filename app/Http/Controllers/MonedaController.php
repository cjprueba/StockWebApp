<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Moneda;

class MonedaController extends Controller
{
    public function obtenerMonedas(){
        
        /*  --------------------------------------------------------------------------------- */

        // CONTAR PRODUCTOS

        $moneda = Moneda::obtener_monedas();
        return response()->json($moneda);

        /*  --------------------------------------------------------------------------------- */
    }
}
