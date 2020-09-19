<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Mpdf\Mpdf;
use App\DevTransferencia;
use App\Exports\TransferenciaGeneral;
use App\VentaTransferencia;

class DevTransferenciaController extends Controller
{
    //

    public function mostrarDataTable(Request $request)
    {
        $devtransferencia = DevTransferencia::mostrarDatatable($request);
        return response()->json($devtransferencia);
    }
        public function mostrarImportar(Request $request)
    {
        $devtransferencia = DevTransferencia::mostrarImportar($request);
        return response()->json($devtransferencia);
    }
        public function mostrarProductosDevolucion(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // MOSTRAR IMPORTAR

        $devtransferencia = DevTransferencia::mostrar_productos_devolucion($request);
        return response()->json($devtransferencia);

        /*  --------------------------------------------------------------------------------- */

    }
            public function mostrarProductosDevolucionImp(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // MOSTRAR IMPORTAR

        $devtransferencia = DevTransferencia::mostrar_productos_devolucion_imp($request);
        return response()->json($devtransferencia);

        /*  --------------------------------------------------------------------------------- */

    }
      public function enviarDevTransferencia(Request $request){

        /*  --------------------------------------------------------------------------------- */

        // ENVIAR TRANSFERENCIA

        $devtransferencia = DevTransferencia::enviar_dev_transferencia($request);
        return response()->json($devtransferencia);

        /*  --------------------------------------------------------------------------------- */

    }
        public function eliminarDevTransferencia(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // ELIMINAR TRANSFERENCIA

        $devtransferencia = DevTransferencia::eliminar($request->all(), 1);
        return response()->json($devtransferencia); 
        
        /*  --------------------------------------------------------------------------------- */
        
    }
        public function importarDevTransferencia(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // MOSTRAR IMPORTAR

        $transferencia = DevTransferencia::importar_dev_transferencia($request->all());
        return response()->json($transferencia);

        /*  --------------------------------------------------------------------------------- */

    }
        public function rechazarDevTransferencia(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // MOSTRAR IMPORTAR

        $Devtransferencia = DevTransferencia::rechazar_dev_transferencia($request);
        return response()->json($Devtransferencia);

        /*  --------------------------------------------------------------------------------- */

    }
}
