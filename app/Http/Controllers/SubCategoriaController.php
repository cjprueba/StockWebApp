<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SubCategoria;

class SubCategoriaController extends Controller
{
    public function obtenerSubCategorias(){
        
        /*  --------------------------------------------------------------------------------- */

        // OBTENER SUB CATEGORIAS

        $sub_categoria = SubCategoria::obtener_subCategorias();
        return response()->json($sub_categoria);

        /*  --------------------------------------------------------------------------------- */
    }
}
