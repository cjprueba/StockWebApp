<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Especificacion;
class EspecificacionController extends Controller
{
    //
        public function obtenerAviso(){
        
        /*  --------------------------------------------------------------------------------- */

        // OBTENER PROVEEDORES

        $Especificacion = Especificacion::obtener_avisos();
        return response()->json($Especificacion);

        /*  --------------------------------------------------------------------------------- */
    }
        public function aceptarTerminos(Request $request)
    {
        /*  --------------------------------------------------------------------------------- */

        // MOSTRAR PROVEEDORES 

        $aceptar = Especificacion::aceptar_terminos($request->all());
        return response()->json($aceptar);

        /*  --------------------------------------------------------------------------------- */

    }
}
