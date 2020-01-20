<?php

namespace App;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Gondola extends Model
{

    protected $connection = 'retail';
    protected $table = 'gondolas';

     public static function obtener_gondolas()
    {

    	/*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

    	// OBTENER TODAS LAS GONDOLAS

    	$gondolas = Gondola::select(DB::raw('ID, CODIGO, DESCRIPCION'))
        ->where('ID_SUCURSAL', '=', $user->id_sucursal)
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


    public static function obtener_gondolas_por_producto($codigo)
    {

        /*  --------------------------------------------------------------------------------- */

        // OBTENER LOS DATOS DEL USUARIO LOGUEADO 

        $user = auth()->user();

        /*  --------------------------------------------------------------------------------- */

        // OBTENER TODAS LAS GONDOLAS

        $gondolas = Gondola::select(DB::raw('GONDOLAS.ID, CODIGO, DESCRIPCION, GONDOLA_TIENE_PRODUCTOS.FECALTAS'))
        ->rightjoin('GONDOLA_TIENE_PRODUCTOS', 'GONDOLA_TIENE_PRODUCTOS.ID_GONDOLA', '=', 'GONDOLAS.ID')
        ->where('GONDOLAS.ID_SUCURSAL', '=', $user->id_sucursal)
        ->where('GONDOLA_TIENE_PRODUCTOS.GONDOLA_COD_PROD', '=', $codigo)
        ->get();

        /*  --------------------------------------------------------------------------------- */
       
        // RETORNAR EL VALOR

        return ['response' => true, 'gondolas' => $gondolas];

        /*  --------------------------------------------------------------------------------- */

    }
}
