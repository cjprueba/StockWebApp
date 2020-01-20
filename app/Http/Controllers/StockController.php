<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Stock;

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

}
