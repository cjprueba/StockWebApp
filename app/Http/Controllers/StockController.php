<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Stock;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\StockExport;

class StockController extends Controller
{

     public function obtenerLotesConCantidad(Request $request)
    {
        
        /*  --------------------------------------------------------------------------------- */

        // GUARDAR PRODUCTO 

        $stock = Stock::obtener_lotes_con_cantidad($request->all());
        return response()->json($stock);
        
        /*  --------------------------------------------------------------------------------- */

    }


    public function vencidos(Request $request)
    {
        
        /*  --------------------------------------------------------------------------------- */

        // GUARDAR PRODUCTO 

        $stock = Stock::vencidos($request);
        return response()->json($stock);
        
        /*  --------------------------------------------------------------------------------- */

    }

    public function terminados(Request $request)
    {
        
        /*  --------------------------------------------------------------------------------- */

        // GUARDAR PRODUCTO 

        $stock = Stock::terminados($request);
        return response()->json($stock);
        
        /*  --------------------------------------------------------------------------------- */

    }

    public function terminados_reporte(Request $request)
    {
        
        /*  --------------------------------------------------------------------------------- */

        // GUARDAR PRODUCTO 

        $stock = Stock::terminados_reporte($request);
        return response()->json($stock);
        
        /*  --------------------------------------------------------------------------------- */

    }

    public function descargar(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // DESCARGAR ARCHIVO EXCEL 

        return Excel::download(new StockExport($request->all()), 'Stock.xlsx');

        /*  --------------------------------------------------------------------------------- */

    }
        public function loteProducto(Request $request)
    {
        /*  --------------------------------------------------------------------------------- */

        // MOSTRAR PROVEEDORES 

        $lote = Stock::loteProducto($request);
        return response()->json($lote);

        /*  --------------------------------------------------------------------------------- */

    }
}
