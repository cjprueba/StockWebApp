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
}
