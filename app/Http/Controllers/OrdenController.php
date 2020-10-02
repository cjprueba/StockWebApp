<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Orden;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class OrdenController extends Controller
{
    
    /*  ----------------------------DATATABLE ORDEN COMPLETADA------------------------------- */

    public function datatable(Request $request){

        return Orden::datatableOrden($request);

        /*  --------------------------------------------------------------------------------- */

    }

    /*  ---------------------------DATATABLE ORDEN DETALLE COM------------------------------- */

    public function mostrarProductos(Request $request){

        // MOSTRAR IMPORTAR

        $orden = Orden::mostrarProductos($request);
        return response()->json($orden);

        /*  --------------------------------------------------------------------------------- */

    }

    /*  ---------------------------FACTURA PDF ORDEN COMPLETADA------------------------------ */
    
    public function factura(Request $request){

        $orden = Orden::facturaPdf($request->all());
        return response()->json($orden);

        /*  --------------------------------------------------------------------------------- */

    }

    /*  ----------------------------CABECERA ORDEN COMPLETADA-------------------------------- */

    public function obtenerCabecera(Request $request){

        // MOSTRAR CABECERA 

        $orden = Orden::mostrarCabecera($request->all());
        return response()->json($orden);

        /*  --------------------------------------------------------------------------------- */

    }

    /*  --------------------------PDF DIRECCION ORDEN COMPLETADA------------------------------ */

    public function direccionPDF(Request $request){


       // OBTENER TODOS LOS DATOS

        $orden = Orden::direccionPDF($request->all());
        return response()->json($orden);

    }

    /*  ------------------------------ENVIAR ORDEN COMPLETADA-------------------------------- */

    public function enviarOrden(Request $request){

        // ENVIAR 

        $orden = Orden::enviarOrden($request);
        return response()->json($orden);

        /*  --------------------------------------------------------------------------------- */

    }

    /*  ----------------------------DATATABLE ORDEN PENDIENTE-------------------------------- */

    public function datatable_pendiente(Request $request){

        return Orden::datatablePendiente($request);

        /*  --------------------------------------------------------------------------------- */

    }

    /*  ----------------------------DATATABLE ORDEN PRECESANDO------------------------------- */

    public function datatable_procesando(Request $request){

        $orden = Orden::datatableProcesando($request);
        return response()->json($orden, 200, ['Content-type'=> 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);    
        /*  --------------------------------------------------------------------------------- */

    }

    /*  --------------------------DATATABLE ORDEN DETALLE PENDIENTE-------------------------- */

    public function mostrarProductosPendiente(Request $request){

        // MOSTRAR IMPORTAR

        $orden = Orden::mostrarProductosPendiente($request);
        return response()->json($orden);

        /*  --------------------------------------------------------------------------------- */

    }

    /*  -----------------------------CABECERA ORDEN PENDIENTE-------------------------------- */

    public function obtenerCabeceraPendiente(Request $request){

        // MOSTRAR CABECERA 

        $orden = Orden::mostrarCabeceraPendiente($request->all());
        return response()->json($orden);

        /*  --------------------------------------------------------------------------------- */

    }

    /*  ----------------------------FACTURA ORDEN PENDI/PROCES------------------------------- */

     public function facturaPendiente(Request $request){

        $orden = Orden::facturaPendientePDF($request->all());
        return response()->json($orden);

        /*  --------------------------------------------------------------------------------- */

    }

    /*  --------------------------DIRECCION PDF ORDEN PENDI/PROCES---------------------------- */

    public function direccionPendientePDF(Request $request){

       // OBTENER TODOS LOS DATOS

        $orden = Orden::direccionPendientePDF($request->all());
        return response()->json($orden);

        /*  --------------------------------------------------------------------------------- */

    }
}
