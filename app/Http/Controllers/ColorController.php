<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Color;

class ColorController extends Controller
{
	      public function obtenerCodigo(){
        
        /*  --------------------------------------------------------------------------------- */

        // OBTENER MARCAS

        $color = Color::obtener_codigo();
        return response()->json($color);

        /*  --------------------------------------------------------------------------------- */
    }
       public function colorGuardar(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // Mostrar Usuarios

        $color = Color::colores_guardar($request->all());
        return response()->json($color);
        
        /*  --------------------------------------------------------------------------------- */

    }
    public function obtenerColores(){
        
        /*  --------------------------------------------------------------------------------- */

        // CONTAR PRODUCTOS

        $color = Color::obtener_colores();
        return response()->json($color);

        /*  --------------------------------------------------------------------------------- */
    }
    public function ColoresDatatable(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // Mostrar Colores

        $color = Color::colores_datatable($request);
        return response()->json($color);
        
        /*  --------------------------------------------------------------------------------- */

    }
            public function filtrarColor(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

       // OBTENER TODOS LOS DATOS DE LA MARCA

         $color = Color::filtrar_colores($request->all());
        return response()->json($color);
        
        /*  --------------------------------------------------------------------------------- */

    }
         public function colorEliminar(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // Mostrar Usuarios

        $color = Color::colores_eliminar($request->all());
        return response()->json($color);
        
        /*  --------------------------------------------------------------------------------- */

    }
}
