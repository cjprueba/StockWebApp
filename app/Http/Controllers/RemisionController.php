<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Remision;

class RemisionController extends Controller
{
    /*  ---------------------------------GUARDAR---------------------------------------- */

    public function guardarRemision(Request $request){

    	$remision=Remision::guardarRemisiones($request->all());
    	return response()->json($remision);
    }

    /*  ---------------------------------FILTRAR---------------------------------------- */

    public function filtrarRemision(Request $request){

        $remision = remision::filtrarRemisiones($request->all());
        return response()->json($remision);
    }

    /*  ---------------------------------ELIMINAR---------------------------------------- */

    public function eliminarRemision(Request $request){
        
        $eliminar = Remision::eliminarRemisiones($request->all());
        return response()->json($eliminar);

    }

    /*  ---------------------------------CODIGO---------------------------------------- */

    public function notaNueva(){
        
        /*  --------------------------------------------------------------------------------- */

        // OBTENER NOTA DE REMISION

        $remision = Remision::ultimo_codigo();
        return response()->json($remision);

        /*  --------------------------------------------------------------------------------- */
    }


    /*  ---------------------------------DATATABLE---------------------------------------- */

    public function remisionDatatable(Request $request){


       // OBTENER TODOS LOS DATOS DE lAS NOTAS DE REMISION

        $remision = Remision::remisionDatatable($request);
        return response()->json($remision);

    }

    /*  ---------------------------------PDF TICKET---------------------------------------- */

    public function remision_pdf(Request $request){


       // OBTENER TODOS LOS DATOS DE lAS NOTAS DE REMISION

        $remision = Remision::ticket_pdf($request->all());
        return response()->json($remision);

    }

    /*  ---------------------------------CUERPO---------------------------------------- */

     public function mostrarCuerpo(Request $request){

        /*  --------------------------------------------------------------------------------- */

        // MOSTRAR EL CUERPO 

        $remision = Remision::mostrar_cuerpo($request->all());
        return response()->json($remision);

        /*  --------------------------------------------------------------------------------- */

    }

    /*  ---------------------------------MODIFICAR---------------------------------------- */

    public function modificarRemision(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // MODIFICAR

        $remision = Remision::modificar($request->all());
        return response()->json($remision); 
        
        /*  --------------------------------------------------------------------------------- */
        
    }
}
