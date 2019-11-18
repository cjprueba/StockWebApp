<?php

namespace App\Http\Controllers;
use App\Vendedores;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class VendedorController extends Controller
{
    //
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


            $ventas = Vendedores::generarConsulta($request->all());
            return response()->json($ventas);

        //return response()->json([$request->all()]);
    }

}
