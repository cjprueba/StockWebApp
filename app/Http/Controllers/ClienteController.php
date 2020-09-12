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


       // OBTENER TODOS LOS DATOS

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

        // OBTENER

        $cliente = Cliente::nuevoCliente();
        return response()->json($cliente);

        /*  --------------------------------------------------------------------------------- */
    }

    public function creditoCliente(Request $request){
        
        /*  --------------------------------------------------------------------------------- */

        // OBTENER MARCAS

        $cliente = Cliente::creditoCliente($request->all());
        return response()->json($cliente);

        /*  --------------------------------------------------------------------------------- */
    }

    public function creditoClienteDatatable(Request $request){
        
        /*  --------------------------------------------------------------------------------- */

        // OBTENER MARCAS

        $cliente = Cliente::credito_cliente_datatable($request);
        return response()->json($cliente);

        /*  --------------------------------------------------------------------------------- */

    }    

    /*  ---------------------------EMPRESA DATATABLE------------------------------------- */

    public function datatableEmpresa(Request $request){

       // OBTENER TODOS LOS DATOS 

        $empresa = Cliente::empresasDatatable($request);
        return response()->json($empresa);

    }
}
