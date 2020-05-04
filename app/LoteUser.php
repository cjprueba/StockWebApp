<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LoteUser extends Model
{
    protected $connection = 'retail';
    protected $table = 'lotes_user';

    public static function guardar_referencia($id_user, $accion, $id_lote, $fecha)
    {

    	/*  --------------------------------------------------------------------------------- */

        // INSERTAR REFERENCIAS

        $cr_usr = LoteUser::insert(
            [
                'FK_USER' => $id_user,
                'ACCION' => $accion,
                'FK_LOTE' => $id_lote,
                'FECHA' => $fecha
            ]
        );

        /*  --------------------------------------------------------------------------------- */

    }
}
