<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VentasDetTieneLotes extends Model
{

    /*  --------------------------------------------------------------------------------- */

    // INICIAR LAS VARIABLES GLOBALES

    protected $connection = 'retail';
    protected $table = 'ventasdet_tiene_lotes';
    
    /*  --------------------------------------------------------------------------------- */


    public static function guardar_referencia($id_vd, $id_lote, $cantidad,$id_descuento_lote)
    {

    	/*  --------------------------------------------------------------------------------- */

        // INSERTAR REFERENCIAS

        $tdtl = VentasDetTieneLotes::insert(
            [
                'ID_VENTAS_DET' => $id_vd,
                'ID_LOTE' => $id_lote,
                'CANTIDAD' => $cantidad,
                'ID_DESCUENTO_LOTE'=>$id_descuento_lote
            ]
        );

        /*  --------------------------------------------------------------------------------- */

    }

}
