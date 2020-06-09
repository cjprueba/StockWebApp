<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Cotizacion;

class CotizacionController extends Controller
{
    public function cotizar(Request $request){
    	$cambio = Cotizacion::CALMONED($request->all());
        return response()->json($cambio);
    }

    public function cotizacionDia(Request $request){
    	$cotizaciones = Cotizacion::cotizacion_dia_monedas($request->all());
        return response()->json($cotizaciones);
    }

    public function cotizacionCompraDia(Request $request){
    	$cotizaciones = Cotizacion::cotizacion_dia_monedas_compra($request->all());
        return response()->json($cotizaciones);
    }

    /*  ------------------------------------ ELIMINAR ----------------------------------- */
    
    public function eliminarCotizacion(Request $request){
        
        $eliminar = Cotizacion::eliminar_cotizacion($request->all());
        return response()->json($eliminar);
    }

    /*  ----------------------------------- FILTRAR ------------------------------------- */

    public function filtrarCotizacion(Request $request){
        
        $filtro = Cotizacion::filtrar_cotizacion($request->all());
        return response()->json($filtro);   
    }

    /*  ------------------------------------ GUARDAR ------------------------------------- */

    public function guardarCotizacion(Request $request){

        $cotizaciones = Cotizacion::cotizacion_guardar($request->all());
        return response()->json($cotizaciones);
    }
    
    /*  --------------------------------------------------------------------------------- */

}
