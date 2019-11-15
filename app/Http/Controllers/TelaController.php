<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Tela;

class TelaController extends Controller
{
    public function obtenerTelas(){
        
        /*  --------------------------------------------------------------------------------- */

        // CONTAR PRODUCTOS

        $tela = Tela::obtener_telas();
        return response()->json($tela);

        /*  --------------------------------------------------------------------------------- */
        
    }
}
