<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Lotes_tiene_ComprasDet extends Model
{
    /*  --------------------------------------------------------------------------------- */

    // INICIAR LAS VARIABLES GLOBALES

    protected $connection = 'retail';
    protected $table = 'lote_tiene_comprasdet';
    
    /*  --------------------------------------------------------------------------------- */

    public static function guardar_referencia($id_cr, $id_lote)
    {

    	/*  --------------------------------------------------------------------------------- */

        // INSERTAR REFERENCIAS

        $cdtl = Lotes_tiene_ComprasDet::insert(
            [
                'ID_COMPRAS_DET' => $id_cr,
                'ID_LOTE' => $id_lote
            ]
        );

        /*  --------------------------------------------------------------------------------- */

    }
}
