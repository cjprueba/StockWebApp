<?php

namespace App;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Genero extends Model
{
    public static function obtener_generos()
    {

    	/*  --------------------------------------------------------------------------------- */

    	// OBTENER TODAS LAS CATEGORIAS

    	$generos = DB::connection('retail')
        ->table('GENERO')
        ->select(DB::raw('CODIGO, DESCRIPCION'))
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR EL VALOR

        if ($generos) {
        	return ['generos' => $generos];
        } else {
        	return ['generos' => 0];
        }

        /*  --------------------------------------------------------------------------------- */

    }
}
