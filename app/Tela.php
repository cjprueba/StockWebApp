<?php

namespace App;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Tela extends Model
{
    public static function obtener_telas()
    {

    	/*  --------------------------------------------------------------------------------- */

    	// OBTENER TODAS LAS CATEGORIAS

    	$telas = DB::connection('retail')
        ->table('TELAS')
        ->select(DB::raw('CODIGO, DESCRIPCION'))
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR EL VALOR

        if ($telas) {
        	return ['telas' => $telas];
        } else {
        	return ['telas' => 0];
        }

        /*  --------------------------------------------------------------------------------- */

    }
}
