<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Transporte;
class TransporteController extends Controller
{
    //
                public function filtrarTransporte(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

       // OBTENER TODOS LOS DATOS DEL TALLE

         $transporte = Transporte::filtrar_transporte($request->all());
        return response()->json($transporte);
        
        /*  --------------------------------------------------------------------------------- */

    }
            public function transporteDatatable(Request $request)
    {
        /*  --------------------------------------------------------------------------------- */

        // MOSTRAR PROVEEDORES 

        $transporte = Transporte::transporte_datatable($request);
        return response()->json($transporte);

        /*  --------------------------------------------------------------------------------- */

    }
        public function obtenerCodigo(){
        
        /*  --------------------------------------------------------------------------------- */

        // OBTENER CODIGO TRANSPORTE

        $Transporte = Transporte::obtener_codigo();
        return response()->json($Transporte);

        /*  --------------------------------------------------------------------------------- */
    }
      public function transporteGuardar(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // Mostrar Usuarios

        $transporte = Transporte::transporte_guardar($request->all());
        return response()->json($transporte);
        
        /*  --------------------------------------------------------------------------------- */

    }
    public function transporteEliminar(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // Eliminar Telas

        $transporte = Transporte::transporte_eliminar($request->all());
        return response()->json($transporte);
        
        /*  --------------------------------------------------------------------------------- */

    }
}
