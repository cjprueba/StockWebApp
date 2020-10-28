<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User_Supervisor;
class User_SupervisorController extends Controller
{
    //
            public function obtener_autorizacion(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

       // OBTENER TODOS LOS DATOS DEL PRODUCTO

        $autorizacion = User_Supervisor::obtener_autorizacion($request->all());
        return response()->json($autorizacion);
        
        /*  --------------------------------------------------------------------------------- */

    }
}
