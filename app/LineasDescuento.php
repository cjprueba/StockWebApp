<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class LineasDescuento extends Model
{
    /*  --------------------------------------------------------------------------------- */

    // INICIAR LAS VARIABLES GLOBALES

    protected $connection = 'retail';
    protected $table = 'lineas_descuento';
    
    /*  --------------------------------------------------------------------------------- */

    public static function obtener_descuento($categoria, $id_sucursal)
    {

    	/*  --------------------------------------------------------------------------------- */

    	$fecha = date('Y-m-d');

    	/*  --------------------------------------------------------------------------------- */

        // OBTENER DESCUENTO

        $descuento = LineasDescuento::select(DB::raw('DESCUENTO'))
        ->where('CODIGO_LINEA', '=', $categoria)
        ->where('ID_SUCURSAL', '=', $id_sucursal)
        ->where('FECHAINI', '<=', $fecha)
        ->where('FECHAFIN', '>=', $fecha)
        ->limit(1)
        ->get();

        /*  --------------------------------------------------------------------------------- */

        if (count($descuento) > 0) {
        	$descuento = $descuento[0]->DESCUENTO;
        } else {
        	$descuento = 0;
        }

        /*  --------------------------------------------------------------------------------- */

        return $descuento;

        /*  --------------------------------------------------------------------------------- */

    }
}
