<?php

namespace  App\Http\Controllers;
use App\Exports\Compras;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
class CompraController extends Controller
{
    //
                public function Descargar(Request $request)
    {


           return Excel::download(new Compras($request->all()), 'Inventario.xlsx');

        //return response()->json([$request->all()]);
    }
}
