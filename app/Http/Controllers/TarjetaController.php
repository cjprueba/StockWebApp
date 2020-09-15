<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Tarjeta;
use App\VentaTarjeta;

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

    public function reporteTarjeta(Request $request){
        
        /*  --------------------------------------------------------------------------------- */

        // GUARDAR  

        $tarjeta = VentaTarjeta::rptVentaTarjeta($request->all());
        return response()->json($tarjeta);
        
        /*  --------------------------------------------------------------------------------- */

    }

    public function generarVentaTarjeta(Request $request){

        /*  --------------------------------------------------------------------------------- */

        // MOSTRAR IMPORTAR

        $tarjeta = VentaTarjeta::generarReporteVentaTarjeta($request);
        return response()->json($tarjeta);

        /*  --------------------------------------------------------------------------------- */

    }
}
