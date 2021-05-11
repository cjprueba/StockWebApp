<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Caja;

class CajaController extends Controller
{
	public function obtenerCaja(Request $request)
	{

        /*  --------------------------------------------------------------------------------- */

       // OBTENER TODOS LOS DATOS DEL TALLE

        $caja = Caja::caja_obtener($request->all());
        return response()->json($caja);
        
        /*  --------------------------------------------------------------------------------- */
    }


    public function cajaAsignar(Request $request){
    	//OBTENER

    	$caja = Caja::asignar_Caja($request->all());
        return response()->json($caja);
    }


    public function cajaExiste(Request $request){

    	$caja = Caja::existe_Caja($request->all());
        return response()->json($caja);

    }


    public function cajaQuitar(Request $request){

    	$caja = Caja::quitar($request->all());
        return response()->json($caja);

    }
}
