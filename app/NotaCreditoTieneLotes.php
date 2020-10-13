<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NotaCreditoTieneLotes extends Model
{
    /*  --------------------------------------------------------------------------------- */

    // INICIAR LAS VARIABLES GLOBALES

    protected $connection = 'retail';
    protected $table = 'nota_credito_tiene_lote';
    
    /*  --------------------------------------------------------------------------------- */


    public static function guardar_referencia($data)
    {

    	/*  --------------------------------------------------------------------------------- */

        // INSERTAR REFERENCIAS

        $nc = NotaCreditoTieneLotes::insert(
            [
                'FK_VENTA_DET' => $data['FK_VENTA_DET'],
                'FK_NOTA_CREDITO_DET' => $data['FK_NOTA_CREDITO_DET'],
                'ID_LOTE' => $data['ID_LOTE'],
                'CANTIDAD' => $data['CANTIDAD']
            ]
        );

        /*  --------------------------------------------------------------------------------- */

    }
}
