<?php

namespace App;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Ventas_Descuento extends Model
{
    /*  --------------------------------------------------------------------------------- */

    // INICIAR LAS VARIABLES GLOBALES

    protected $connection = 'retail';
    protected $table = 'ventas_descuento';
    
    /*  --------------------------------------------------------------------------------- */


    public static function guardar_referencia($porcentaje, $total, $id_v, $id_moneda)
    {

    	/*  --------------------------------------------------------------------------------- */

        // INSERTAR REFERENCIAS

        Ventas_Descuento::insert(
            [
                'PORCENTAJE' => $porcentaje,
                'TOTAL' => $total,
                'FK_VENTAS' => $id_v,
                'FK_MONEDA' => $id_moneda
            ]
        );

        /*  --------------------------------------------------------------------------------- */

    }
}
