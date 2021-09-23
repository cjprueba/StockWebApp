<?php

namespace App\Http\Controllers;
use App\Cajero;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CajeroController extends Controller
{
	public function __construct(){
        $this->middleware('auth');
    }

    public function index()
    {
        //return Venta::whereDate('FECALTAS', '2019-06-01');
        //return DB::connection('retail')->select('select * from ventas limit 10');
        $ventas = Venta::ventas(date("Y-m-d"));
        return response()->json($ventas);
    }

    public function mostrar(Request $request)
    {
        $cajero = Cajero::generarConsulta($request->all());
        return response()->json($cajero);
        //return response()->json([$request->all()]);
    }

    public function cajeroBusqueda(Request $request)
    {
        $cajero = Cajero::busquedaCajero($request->all());
        return response()->json($cajero);
    }

    public function datatable(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // Mostrar Colores

        $cajero = Cajero::cajero_datatable($request);
        return response()->json($cajero);
        
        /*  --------------------------------------------------------------------------------- */

    }

    public function reporteCajero(Request $request){
        /*  --------------------------------------------------------------------------------- */
        // GUARDAR 
        $cajero = Cajero::rptVentaCajero($request->all());
        return response()->json($cajero);
        /*  --------------------------------------------------------------------------------- */

    }
    public function generarVentaCajero(Request $request){

        /*  --------------------------------------------------------------------------------- */

        // MOSTRAR IMPORTAR

        $cajero = Cajero::generarReporteVentaCajero($request);
        return response()->json($cajero);

        /*  --------------------------------------------------------------------------------- */

    }
}
