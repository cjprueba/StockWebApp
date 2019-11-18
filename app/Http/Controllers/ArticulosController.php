<?php

namespace App\Http\Controllers;
use App\Articulos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class ArticulosController extends Controller
{
    //
        public function PorCantidad(Request $request)
    {


            $ventas = Articulos::generarConsulta($request->all());
            return response()->json($ventas);

        //return response()->json([$request->all()]);
    }
            public function PorMonto(Request $request)
    {


            $ventas = Articulos::generarPorMonto($request->all());
            return response()->json($ventas);

        //return response()->json([$request->all()]);
    }


}
