<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Banco;

class BancoController extends Controller
{
    public function dataTable(Request $request)
    {
    	/*  --------------------------------------------------------------------------------- */

    	// OBTENER LAS TARJETAS 

        $bancos = Banco::datatable_bancos($request);
        return response()->json($bancos);

        /*  --------------------------------------------------------------------------------- */

    }
}
