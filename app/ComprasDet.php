<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class ComprasDet extends Model
{
     public static function id_cd($cod_prod, $lote)
    {

    	/*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

    	/*  --------------------------------------------------------------------------------- */

    	// CONSULTAR COMPRAS DET 

    	$comprasdet = DB::connection('retail')
        ->table('COMPRASDET')
        ->select(DB::raw('ID'))
        ->where('COD_PROD', '=', $cod_prod)
        ->where('LOTE', '=', $lote)
        ->where('ID_SUCURSAL', '=', $user->id_sucursal)
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR VALOR 

        if (count($comprasdet) > 0) {
            return $comprasdet[0]->ID;
        } else {
            return 0;
        }

        /*  --------------------------------------------------------------------------------- */
    }
}
