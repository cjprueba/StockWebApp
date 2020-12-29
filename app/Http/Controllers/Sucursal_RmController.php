<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Sucursal_Rm;

class Sucursal_RmController extends Controller{

    /*  --------------------------------- DATATABLE--------------------------------------- */

    public function sucursalRmDatatable(Request $request){

        $rm_sucursal = Sucursal_Rm::sucursalRmDatatable($request);
        return response()->json($rm_sucursal);

    }

    /*  --------------------------------- FILTRAR ---------------------------------------- */

    public function filtrarSucursalRm(Request $request){

        $rm_sucursal = Sucursal_Rm::filtrarSucursalRM($request->all());
        return response()->json($rm_sucursal);
    }

    /*  --------------------------------- GUARDAR ---------------------------------------- */

    public function guardarSucursalRm(Request $request){
       
        $rm_sucursal = Sucursal_Rm::guardarSucursalRM($request->all());
        return response()->json($rm_sucursal);
    }

    /*  --------------------------------- ELIMINAR ---------------------------------------- */

    public function eliminarSucursalRm(Request $request){

        $rm_sucursal = Sucursal_Rm::eliminarSucursalRM($request->all());
        return response()->json($rm_sucursal);
        
    }

    /*  ---------------------------- NUEVO SECTOR ---------------------------------------- */

    public function sucursalNuevaRm(){

        $rm_sucursal = Sucursal_Rm::nuevaSucursalRM();
        return response()->json($rm_sucursal);

    }
}
