<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Cliente;

class ClienteController extends Controller
{

    public function datatable(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // Mostrar Colores

        $cliente = Cliente::cliente_datatable($request);
        return response()->json($cliente);
        
        /*  --------------------------------------------------------------------------------- */

    }

    /*  ---------------------------------FILTRAR---------------------------------------- */

    public function filtrarCliente(Request $request){

        $cliente = Cliente::filtrarClientes($request->all());
        return response()->json($cliente);
    }
    
    /*  --------------------------------------------------------------------------------- */

    public function clienteDatatable(Request $request){


       // OBTENER TODOS LOS DATOS DE lAS SUCURSALES

         $cliente = Cliente::clientesDatatable($request);
        return response()->json($cliente);

    }

    /*  ---------------------------------GUARDAR---------------------------------------- */

    public function guardarCliente(Request $request){
       
        $cliente = Cliente::guardarClientes($request->all());
        return response()->json($cliente);
    
    }

    /*  ---------------------------------ELIMINAR---------------------------------------- */

    public function eliminarCliente(Request $request){

        $cliente = Cliente::eliminarClientes($request->all());
        return response()->json($cliente);
        
    }
    
    public function nuevoCliente(){
        
        /*  --------------------------------------------------------------------------------- */

        // OBTENER MARCAS

        $cliente = Cliente::nuevoCliente();
        return response()->json($cliente);

        /*  --------------------------------------------------------------------------------- */
    }

    public function creditoCliente(){
        
        /*  --------------------------------------------------------------------------------- */

        // OBTENER MARCAS

        $cliente = Cliente::creditoCliente();
        return response()->json($cliente);

        /*  --------------------------------------------------------------------------------- */
    }
}
