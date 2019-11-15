<?php

namespace App;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Color extends Model
{
     public static function obtener_colores()
    {

    	/*  --------------------------------------------------------------------------------- */

    	// OBTENER TODAS LAS CATEGORIAS

    	$colores = DB::connection('retail')
        ->table('COLORES')
        ->select(DB::raw('CODIGO, DESCRIPCION'))
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR EL VALOR

        if ($colores) {
        	return ['colores' => $colores];
        } else {
        	return ['colores' => 0];
        }

        /*  --------------------------------------------------------------------------------- */

    }
}
