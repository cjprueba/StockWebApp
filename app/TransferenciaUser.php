<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransferenciaUser extends Model
{
    protected $connection = 'retail';
    protected $table = 'transferencias_user';

    public static function guardar_referencia($id_user, $accion, $id_transferencia, $fecha)
    {

    	/*  --------------------------------------------------------------------------------- */

        // INSERTAR REFERENCIAS

        $cr_usr = TransferenciaUser::insert(
            [
                'FK_USER' => $id_user,
                'ACCION' => $accion,
                'FK_TRANSFERENCIA' => $id_transferencia,
                'FECHA' => $fecha
            ]
        );

        /*  --------------------------------------------------------------------------------- */

    }
}
