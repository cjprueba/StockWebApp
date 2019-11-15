<?php

namespace App;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Talle extends Model
{
    public static function obtener_talles()
    {

    	/*  --------------------------------------------------------------------------------- */

    	// OBTENER TODAS LAS CATEGORIAS

    	$talles = DB::connection('retail')
        ->table('TALLES')
        ->select(DB::raw('CODIGO, DESCRIPCION'))
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR EL VALOR

        if ($talles) {
        	return ['talles' => $talles];
        } else {
        	return ['talles' => 0];
        }

        /*  --------------------------------------------------------------------------------- */

    }
}
