<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\NewCotizacion;
class NewCotizacionController extends Controller
{
    public function obtenerCotizaciones(){

        $cotizaciones = NewCotizacion::obtener_cotizaciones();
        return response()->json($cotizaciones);
    }
        public function guardarCotizacion(Request $request){

        $cotizaciones = NewCotizacion::guardar_cotizacion($request->all());
        return response()->json($cotizaciones);
    }
}
