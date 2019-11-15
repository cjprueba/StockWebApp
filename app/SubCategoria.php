<?php

namespace App;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class SubCategoria extends Model
{
    public static function obtener_subCategorias()
    {

    	/*  --------------------------------------------------------------------------------- */

    	// OBTENER TODAS LAS SUB CATEGORIAS

    	$subCategorias = DB::connection('retail')
        ->table('SUBLINEAS')
        ->select(DB::raw('CODIGO, DESCRIPCION'))
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR EL VALOR

        if ($subCategorias) {
        	return ['subCategorias' => $subCategorias];
        } else {
        	return ['subCategorias' => 0];
        }

        /*  --------------------------------------------------------------------------------- */

    }
}
