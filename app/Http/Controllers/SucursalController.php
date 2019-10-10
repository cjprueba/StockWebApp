<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Sucursal;

class SucursalController extends Controller
{
     public function mostrar(Request $request)
    {
        $sucursales = Sucursal::mostrarSucursal($request->all());
        return response()->json($sucursales);
    }

     public function encontrar(Request $request)
    {
        $sucursales = Sucursal::encontrarSucursal($request->all());
        return response()->json($sucursales);
    }
}
