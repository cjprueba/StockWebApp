<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Parametro extends Model
{
     public static function mostrarParametro()
    {

    	/*  --------------------------------------------------------------------------------- */

    	// OBTENER LOS DATOS DEL USUARIO LOGUEADO 

    	$user = auth()->user();

    	/*  --------------------------------------------------------------------------------- */

    	// OBTENER PARAMETROS

    	$parametros = DB::connection('retail')
        ->table('parametros')
        ->select(DB::raw('parametros.MONEDA, MONEDAS.DESCRIPCION, MONEDAS.CANDEC, parametros.TAB_UNICA, parametros.ID_SUCURSAL'))
        ->join('MONEDAS', 'parametros.MONEDA', '=', 'MONEDAS.CODIGO')
        ->where('parametros.ID_SUCURSAL','=',$user->id_sucursal)
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR EL VALOR

        if ($parametros) {
        	return ['parametros' => $parametros];
        } else {
        	return ['parametros' => 0];
        }

        /*  --------------------------------------------------------------------------------- */
    }

    public static function tab_unica()
    {
        
        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // OBTENER PARAMETROS

        $parametros = DB::connection('retail')
        ->table('parametros')
        ->select(DB::raw('TAB_UNICA'))
        ->where('ID_SUCURSAL','=',$user->id_sucursal)
        ->get();

        /*  --------------------------------------------------------------------------------- */

        // RETORNAR EL VALOR

        if (count($parametros) > 0) {
            return $parametros[0]->TAB_UNICA;
        } else {
            return 0;
        }

        /*  --------------------------------------------------------------------------------- */

    }    
}
