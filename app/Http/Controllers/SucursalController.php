<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Sucursal;

class SucursalController extends Controller
{   

    /*  ---------------------------------GUARDAR---------------------------------------- */

    public function guardarSucursal(Request $request){
       
        $sucursal = Sucursal::guardarSucursales($request->all());
        return response()->json($sucursal);
    
    }

    /*  ---------------------------------ELIMINAR---------------------------------------- */

    public function eliminarSucursal(Request $request){

        $sucursal = Sucursal::eliminarSucursales($request->all());
        return response()->json($sucursal);
        
    }

    /*  ---------------------------------FILTRAR---------------------------------------- */

    public function filtrarSucursal(Request $request){

        $sucursal = Sucursal::filtrar_Sucursal($request->all());
        return response()->json($sucursal);
    }

    /*  ---------------------------------CODIGO---------------------------------------- */

    public function sucursalNueva(){
        
        /*  --------------------------------------------------------------------------------- */

        // OBTENER SUCURSALES

        $sucursal = Sucursal::nuevaSucursal();
        return response()->json($sucursal);

        /*  --------------------------------------------------------------------------------- */
    }

    public function sucursalDatatable(Request $request){

        /*  --------------------------------------------------------------------------------- */

       // OBTENER TODOS LOS DATOS DE lAS SUCURSALES

         $sucursal = Sucursal::sucursalesDatatable($request);
        return response()->json($sucursal);
        
        /*  --------------------------------------------------------------------------------- */

    }    

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
