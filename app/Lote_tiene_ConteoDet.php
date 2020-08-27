<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Lote_tiene_ConteoDet extends Model
{
    /*  --------------------------------------------------------------------------------- */

    // INICIAR LAS VARIABLES GLOBALES

    protected $connection = 'retail';
    protected $table = 'lote_tiene_conteodet';
    
    /*  --------------------------------------------------------------------------------- */

    public static function guardar_referencia($id_cr, $id_lote, $accion, $usuario, $cantidad)
    {

    	/*  --------------------------------------------------------------------------------- */

        // INSERTAR REFERENCIAS

        $cdtl = Lote_tiene_ConteoDet::insert(
            [
                'ID_CONTEO_DET' => $id_cr,
                'ID_LOTE' => $id_lote,
                'ACCION' => $accion,
                'FK_USER' => $usuario,
                'CANTIDAD' => $cantidad
            ]
        );

        /*  --------------------------------------------------------------------------------- */

    }
}
