<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Rol;
class RolController extends Controller
{
    //
     public function obtenerRol(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER TODOS LOS DATOS DEL PRODUCTO

        $rol = Rol::obtener_roles($request->all());
        return response()->json($rol);
        
        /*  --------------------------------------------------------------------------------- */

    }
}
