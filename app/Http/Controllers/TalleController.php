<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Talle;

class TalleController extends Controller
{
    public function obtenerTalles(){
        
        /*  --------------------------------------------------------------------------------- */

        // CONTAR PRODUCTOS

        $talle = Talle::obtener_talles();
        return response()->json($talle);

        /*  --------------------------------------------------------------------------------- */
    }
        public function tallesDatatable(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // Mostrar Talles

        $talle = Talle::talles_datatable($request);
        return response()->json($talle);
        
        /*  --------------------------------------------------------------------------------- */

    }
        public function filtrarTalle(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

       // OBTENER TODOS LOS DATOS DEL TALLE

         $talle = Talle::filtrar_talles($request->all());
        return response()->json($talle);
        
        /*  --------------------------------------------------------------------------------- */

    }
       public function obtenerCodigo(){
        
        /*  --------------------------------------------------------------------------------- */

        // OBTENER MARCAS

        $talle = Talle::obtener_codigo();
        return response()->json($talle);

        /*  --------------------------------------------------------------------------------- */
    }
           public function talleGuardar(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // Mostrar Usuarios

        $talle = Talle::talle_guardar($request->all());
        return response()->json($talle);
        
        /*  --------------------------------------------------------------------------------- */

    }
             public function talleEliminar(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // Mostrar Usuarios

        $talle = Talle::talle_eliminar($request->all());
        return response()->json($talle);
        
        /*  --------------------------------------------------------------------------------- */

    }
}
