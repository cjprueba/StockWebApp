<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Descuento;

class DescuentoController extends Controller
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
        
    }

    public function mostrar(Request $request)
    {
        //return response()->json([$request->all()]);
        $ventas = Descuento::generarConsulta($request->all());
        return response()->json($ventas);
    }

    
}
