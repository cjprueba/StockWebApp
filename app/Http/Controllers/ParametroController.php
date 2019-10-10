<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Parametro;

class ParametroController extends Controller
{
     public function mostrar(Request $request)
    {
        $parametros = Parametro::mostrarParametro();
        return response()->json($parametros);
    }
}
