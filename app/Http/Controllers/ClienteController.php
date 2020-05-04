<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Cliente;

class ClienteController extends Controller
{

    public function datatable(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // Mostrar Colores

        $cliente = Cliente::cliente_datatable($request);
        return response()->json($cliente);
        
        /*  --------------------------------------------------------------------------------- */

    }

}
