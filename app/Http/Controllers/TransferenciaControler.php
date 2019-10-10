<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Transferencia;


class TransferenciaControler extends Controller
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
        
    }

    public function mostrar(Request $request)
    {
        $ventas = Transferencia::generarConsulta($request->all());
        return response()->json($ventas);
    }

    
    public function guardarTransferencia(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // COMPROBAR SI HAY STOCK DISPONIBLE

        $transferencia = Transferencia::guardar($request->all());
        return response()->json($transferencia); 
        
        /*  --------------------------------------------------------------------------------- */
        
    }
}
