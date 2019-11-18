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
}
