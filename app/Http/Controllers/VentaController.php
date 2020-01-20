<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Venta;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class VentaController extends Controller
{

    public function __construct(){
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //return Venta::whereDate('FECALTAS', '2019-06-01');
        //return DB::connection('retail')->select('select * from ventas limit 10');
        $ventas = Venta::ventas(date("Y-m-d"));
        return response()->json($ventas);
    }


   

    public function mostrar(Request $request)
    {
        if ($request["Opcion"] === 2) {
            $ventas = Venta::generarTablaMarca($request->all());
            return response()->json($ventas);
        } else if  ($request["Opcion"] === 3) {
            $ventas = Venta::generarTablaCategoria($request->all());
            return response()->json($ventas);
        } else {
            $ventas = Venta::generarConsulta($request->all());
            return response()->json($ventas);
        }
        //return response()->json([$request->all()]);
    }

    public function periodos_superados(Request $request)
    {
        $ventas = Venta::periodos_superados($request);
        return response()->json($ventas);
    }
   
}
