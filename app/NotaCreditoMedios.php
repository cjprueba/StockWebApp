<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NotaCreditoMedios extends Model
{
    /*  --------------------------------------------------------------------------------- */

    // INICIAR LAS VARIABLES GLOBALES

    protected $connection = 'retail';
    protected $table = 'nota_credito_medios';
    
    /*  --------------------------------------------------------------------------------- */


    public static function guardar_referencia($data)
    {

    	/*  --------------------------------------------------------------------------------- */

        // INSERTAR REFERENCIAS

        $nc = NotaCreditoMedios::insert(
            [
                'FK_NOTA_CREDITO' => $data['FK_NOTA_CREDITO'],
                'TOTAL' => $data['TOTAL'],
                'FK_MONEDA' => $data['FK_MONEDA'],
                'TIPO_MEDIO' => $data['TIPO_MEDIO'],
            ]
        );

        /*  --------------------------------------------------------------------------------- */

    }
}
