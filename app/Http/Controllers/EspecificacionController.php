<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Especificacion;
class EspecificacionController extends Controller
{
    //
        public function obtenerAvisos(){
        
        /*  --------------------------------------------------------------------------------- */

        // OBTENER PROVEEDORES

        $Especificacion = Especificacion::obtener_avisos();
        return response()->json($Especificacion);

        /*  --------------------------------------------------------------------------------- */
    }
}
