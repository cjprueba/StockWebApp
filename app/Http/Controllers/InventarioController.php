<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Inventario;

class InventarioController extends Controller
{
    public function agregarEditarProducto(Request $request){
        
        /*  --------------------------------------------------------------------------------- */

        // CONTAR PRODUCTOS

        $inventario = Inventario::insertar_producto($request);
        return response()->json($inventario);

        /*  --------------------------------------------------------------------------------- */
    }

    public function guardarInventario(Request $request){
        
        /*  --------------------------------------------------------------------------------- */

        // MOSTRAR IMPORTAR

        $inventario = Inventario::guardar_inventario($request);
        return response()->json($inventario);

        /*  --------------------------------------------------------------------------------- */
    }

    public function productosDataTable(Request $request)
    {

    	/*  --------------------------------------------------------------------------------- */

    	// MOSTRAR PRODUCTOS INGRESADOS 

        $inventario = Inventario::mostrarDatatable($request);
        return response()->json($inventario);

        /*  --------------------------------------------------------------------------------- */
    }

    public function inventarioDataTable(Request $request)
    {

    	/*  --------------------------------------------------------------------------------- */

    	// MOSTRAR PRODUCTOS INGRESADOS 

        $inventario = Inventario::mostrarDatatableInventario($request);
        return response()->json($inventario);

        /*  --------------------------------------------------------------------------------- */
    }

    public function Inventario_Cerrado(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // DESCARGA REPORTE 

        return Excel::download(new Inventario($request->all()), 'Inventario.xlsx');

        /*  --------------------------------------------------------------------------------- */

    }

}
