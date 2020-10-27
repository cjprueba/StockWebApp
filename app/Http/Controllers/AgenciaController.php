<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Agencia;

class AgenciaController extends Controller
{
    public function datatable(Request $request)
    {

		/*  --------------------------------------------------------------------------------- */
		
		$agencia = Agencia::datatable($request);
        return response()->json($agencia);

        /*  --------------------------------------------------------------------------------- */
    }
}
