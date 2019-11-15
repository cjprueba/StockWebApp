<?php

namespace App;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Moneda extends Model
{
     public static function obtener_monedas()
    {

    	/*  --------------------------------------------------------------------------------- */

    	// OBTENER TODAS LAS MONEDAS

    	$monedas = DB::connection('retail')
        ->table('MONEDAS')
        ->select(DB::raw('CODIGO, DESCRIPCION, CANDEC'))
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR EL VALOR

        if ($monedas) {
        	return ['monedas' => $monedas];
        } else {
        	return ['monedas' => 0];
        }

        /*  --------------------------------------------------------------------------------- */

    }
}
