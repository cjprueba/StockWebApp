<?php

namespace App\Http\Controllers;
use App\Exports\DescuentoMarca;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class ExportDescuentoController extends Controller
{

	public function __construct(){
        $this->middleware('auth');
    }

    public function mostrar(Request $request)
    {
        return Excel::download(new DescuentoMarca($request->all()), 'DescuentosMarca.xlsx');
    }
}
