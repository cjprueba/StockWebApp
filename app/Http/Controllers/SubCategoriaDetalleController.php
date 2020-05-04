<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SubCategoriaDetalle;

class SubCategoriaDetalleController extends Controller
{

    public function obtenerSubCategoriasDetalle(Request $request){
        
        /*  --------------------------------------------------------------------------------- */

        // OBTENER SUB CATEGORIAS

        $sub_categoria = SubCategoriaDetalle::obtener_subCategoriasDetalle($request->all());
        return response()->json($sub_categoria);

        /*  --------------------------------------------------------------------------------- */
    }

    public function datatableSubCategoriaDetalle(Request $request)
    {
        $subcategoriaDetalle = SubCategoriaDetalle::datatable($request);
        return response()->json($subcategoriaDetalle);
    }

    public function filtrarSubCategoriaDetalle(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER TODOS LOS DATOS DE LA SUBCATEGORIA DETALLE

        $SubCategoriaDetalle = SubCategoriaDetalle::filtrar($request->all());
        return response()->json($SubCategoriaDetalle);
        
        /*  --------------------------------------------------------------------------------- */

    }

    public function nuevoSubCategoriaDetalle()
    {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER CODIGO NUEVO

        $SubCategoriaDetalle = SubCategoriaDetalle::nuevo();
        return response()->json($SubCategoriaDetalle);
        
        /*  --------------------------------------------------------------------------------- */

    }

    public function guardarSubCategoriaDetalle(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // GUARDAR

        $SubCategoriaDetalle = SubCategoriaDetalle::guardar($request->all());
        return response()->json($SubCategoriaDetalle);
        
        /*  --------------------------------------------------------------------------------- */

    }

    public function eliminarSubCategoriaDetalle(Request $request)
    {

        /*  --------------------------------------------------------------------------------- */

        // ELIMINAR

        $SubCategoriaDetalle = SubCategoriaDetalle::eliminar($request->all());
        return response()->json($SubCategoriaDetalle);
        
        /*  --------------------------------------------------------------------------------- */

    }
}
