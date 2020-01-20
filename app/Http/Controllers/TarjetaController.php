<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Tarjeta;

class TarjetaController extends Controller
{
    public function dataTable(Request $request)
    {
    	/*  --------------------------------------------------------------------------------- */

    	// OBTENER LAS TARJETAS 

        $tarjeta = Tarjeta::datatable_tarjetas($request);
        return response()->json($tarjeta);

        /*  --------------------------------------------------------------------------------- */

    }
}
