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
}
