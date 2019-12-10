<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Lotes_tiene_TransferenciaDet extends Model
{
    /*  --------------------------------------------------------------------------------- */

    // INICIAR LAS VARIABLES GLOBALES

    protected $connection = 'retail';
    protected $table = 'lote_tiene_transferenciadet';
    
    /*  --------------------------------------------------------------------------------- */

    public static function guardar_referencia($id_tr, $id_lote)
    {

    	/*  --------------------------------------------------------------------------------- */

        // INSERTAR REFERENCIAS

        $tdtl = Lotes_tiene_TransferenciaDet::insert(
            [
                'ID_TRANSFERENCIA_DET' => $id_tr,
                'ID_LOTE' => $id_lote
            ]
        );

        /*  --------------------------------------------------------------------------------- */

    }
}
