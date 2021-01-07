<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\NewCotizacion;
class NewCotizacionController extends Controller
{
    public function obtenerCotizaciones(Request $request){

        $cotizaciones = NewCotizacion::obtener_cotizaciones($request);
        return response()->json($cotizaciones);
    }
        public function guardarCotizacion(Request $request){

        $cotizaciones = NewCotizacion::guardar_cotizacion($request->all());
        return response()->json($cotizaciones);
    }
}
