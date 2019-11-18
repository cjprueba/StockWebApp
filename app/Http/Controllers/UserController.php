<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
class UserController extends Controller
{
    //
    public function obtenerRoles()
    {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER TODOS LOS DATOS DEL PRODUCTO

        $rol = User::obtener_roles();
        return response()->json($rol);
        
        /*  --------------------------------------------------------------------------------- */

    }
        public function guardarRol(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // GUARDAR ROLES

        $rol = User::guardar_rol($request->all());
        return response()->json($rol);
        
        /*  --------------------------------------------------------------------------------- */

    }
}
