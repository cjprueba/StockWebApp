<?php

namespace App;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
     public static function obtener_categorias()
    {

    	/*  --------------------------------------------------------------------------------- */

    	// OBTENER TODAS LAS CATEGORIAS

    	$categorias = DB::connection('retail')
        ->table('LINEAS')
        ->select(DB::raw('CODIGO, DESCRIPCION, ATRIBTELA, ATRIBCOLOR, ATRIBTALLE, ATRIBGENERO, ATRIBMARCA'))
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR EL VALOR

        if ($categorias) {
        	return ['categorias' => $categorias];
        } else {
        	return ['categorias' => 0];
        }

        /*  --------------------------------------------------------------------------------- */

    }
}
