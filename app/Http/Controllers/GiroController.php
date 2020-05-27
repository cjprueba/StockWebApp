<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Giro;

class GiroController extends Controller
{
    public function datatableEntidades(Request $request)
	{

        /*  --------------------------------------------------------------------------------- */

       	// OBTENER TODOS LOS DATOS DEL TALLE

        $giros = Giro::datatable_entidades($request);
        return response()->json($giros);
        
        /*  --------------------------------------------------------------------------------- */

    }
}
