<?php

namespace App\Http\Controllers;
use App\DevolucionVendedores;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class DevolucionController extends Controller
{
    //
        public function mostrar(Request $request)
    {


            $ventas = DevolucionVendedores::generarConsulta($request->all());
            return response()->json($ventas);

        //return response()->json([$request->all()]);
    }
}
