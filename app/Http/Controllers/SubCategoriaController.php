<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SubCategoria;

class SubCategoriaController extends Controller
{
    public function obtenerSubCategorias(Request $request){
        
        /*  --------------------------------------------------------------------------------- */

        // OBTENER SUB CATEGORIAS

        $sub_categoria = SubCategoria::obtener_subCategorias($request->all());
        return response()->json($sub_categoria);

        /*  --------------------------------------------------------------------------------- */
    }
    public function subCategoriasDatatable(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // Mostrar Talles

        $subCategoria = SubCategoria::subcategorias_datatable($request);
        return response()->json($subCategoria);
        
        /*  --------------------------------------------------------------------------------- */

}
    public function subCategoriaDatatable(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */


        $subCategoria = SubCategoria::subcategoria_datatable($request);
        return response()->json($subCategoria);
        
        /*  --------------------------------------------------------------------------------- */

}
   public function filtrarSubCategoria(Request $request)
{

        /*  --------------------------------------------------------------------------------- */

       // OBTENER TODOS LOS DATOS DEL TALLE

         $subCategoria = SubCategoria::filtrar_sub_categoria($request->all());
        return response()->json($subCategoria);
        
        /*  --------------------------------------------------------------------------------- */

    }
           public function obtenerCodigo(){
        
        /*  --------------------------------------------------------------------------------- */

        // OBTENER MARCAS

        $subCategoria = SubCategoria::obtener_codigo();
        return response()->json($subCategoria);

        /*  --------------------------------------------------------------------------------- */
    }
        public function subCategoriaGuardar(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // Mostrar Usuarios

        $subCategoria = SubCategoria::subCategoria_guardar($request->all());
        return response()->json($subCategoria);
        
        /*  --------------------------------------------------------------------------------- */

    }
            public function subCategoriaEliminar(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // Mostrar Usuarios

        $subCategoria = SubCategoria::subCategoria_eliminar($request->all());
        return response()->json($subCategoria);
        
        /*  --------------------------------------------------------------------------------- */

    }
}
