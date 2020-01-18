<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Tela;

class TelaController extends Controller
{
    public function obtenerTelas(){
        
        /*  --------------------------------------------------------------------------------- */

        // CONTAR PRODUCTOS

        $tela = Tela::obtener_telas();
        return response()->json($tela);

        /*  --------------------------------------------------------------------------------- */
        
    }
       public function telasDatatable(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // Mostrar Talles

        $tela = Tela::telas_datatable($request);
        return response()->json($tela);
        
        /*  --------------------------------------------------------------------------------- */

}
       public function obtenerCodigo(){
        
        /*  --------------------------------------------------------------------------------- */

        // OBTENER MARCAS

        $tela = Tela::obtener_codigo();
        return response()->json($tela);

        /*  --------------------------------------------------------------------------------- */
    }
        public function filtrarTela(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

       // OBTENER TODOS LOS DATOS DEL TALLE

         $tela = Tela::filtrar_tela($request->all());
        return response()->json($tela);
        
        /*  --------------------------------------------------------------------------------- */

    }
               public function telaGuardar(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // Mostrar Usuarios

        $tela = Tela::tela_guardar($request->all());
        return response()->json($tela);
        
        /*  --------------------------------------------------------------------------------- */

    }
        public function telaEliminar(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // Eliminar Telas

        $tela = Tela::tela_eliminar($request->all());
        return response()->json($tela);
        
        /*  --------------------------------------------------------------------------------- */

    }
}