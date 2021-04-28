<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\UsuarioTieneSucursales;

class UsuarioTieneSucursalesController extends Controller
{
   public function obtenerSucursal()
   {
   		$sucursal = UsuarioTieneSucursales::obtener_sucursales();
        return response()->json($sucursal);
   }


   public function cambiar_Sucursal(Request $request)
   {
   		$sucursal = UsuarioTieneSucursales::cambiar_user_sucursal($request->all());
        return response()->json($sucursal);
   }
}
