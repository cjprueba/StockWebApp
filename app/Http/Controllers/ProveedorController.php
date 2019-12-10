<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Proveedor;

class ProveedorController extends Controller
{
    public function obtenerProveedores(){
        
        /*  --------------------------------------------------------------------------------- */

        // OBTENER PROVEEDORES

        $proveedor = Proveedor::obtener_proveedores();
        return response()->json($proveedor);

        /*  --------------------------------------------------------------------------------- */
    }

    public function mostrar(Request $request)
    {
        /*  --------------------------------------------------------------------------------- */

        // MOSTRAR PROVEEDORES 

        $ventas = proveedor::generarConsulta($request->all());
        return response()->json($ventas);

        /*  --------------------------------------------------------------------------------- */

    }

    public function descargar(Request $request)
    {

    	/*  --------------------------------------------------------------------------------- */

    	// DESCARGAR REPORTE PROVEEDORES 
    	
        return Excel::download(new VentasProveedor($request->all()), 'VentasProveedores.xlsx');

        /*  --------------------------------------------------------------------------------- */

    }
}
