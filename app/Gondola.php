<?php

namespace App;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Gondola extends Model
{
     public static function obtener_gondolas()
    {

    	/*  --------------------------------------------------------------------------------- */

    	// OBTENER TODAS LAS GONDOLAS

    	$gondolas = DB::connection('retail')
        ->table('GONDOLAS')
        ->select(DB::raw('CODIGO, DESCRIPCION'))
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR EL VALOR

        if ($gondolas) {
        	return ['gondolas' => $gondolas];
        } else {
        	return ['gondolas' => 0];
        }

        /*  --------------------------------------------------------------------------------- */

    }
}
