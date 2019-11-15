<?php

namespace App;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Marca extends Model
{
    public static function obtener_marcas()
    {

    	/*  --------------------------------------------------------------------------------- */

    	// OBTENER TODAS LAS CATEGORIAS

    	$marca = DB::connection('retail')
        ->table('MARCA')
        ->select(DB::raw('CODIGO, DESCRIPCION'))
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR EL VALOR

        if ($marca) {
        	return ['marcas' => $marca];
        } else {
        	return ['marcas' => 0];
        }

        /*  --------------------------------------------------------------------------------- */

    }
}
