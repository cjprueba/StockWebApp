<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Servicios;

class ServicioController extends Controller
{

    public function obtenerServicioPOS(Request $request)
    {
        
        /*  --------------------------------------------------------------------------------- */

        // GUARDAR PRODUCTO 

        $servicios = Servicios::servicios_pos($request->all());
        return response()->json($servicios);
        
        /*  --------------------------------------------------------------------------------- */

    }

    public function reporteDelivery(Request $request){
        
        /*  --------------------------------------------------------------------------------- */

        // GUARDAR PRODUCTO 

        $servicios = Servicios::rptServicioDelivery($request->all());
        return response()->json($servicios);
        
        /*  --------------------------------------------------------------------------------- */

    }

    public function generarRptDelivery(Request $request){

        /*  --------------------------------------------------------------------------------- */

        // MOSTRAR IMPORTAR

        $servicios = Servicios::generarReporteDelivery($request);
        return response()->json($servicios);

        /*  --------------------------------------------------------------------------------- */

    }
}
