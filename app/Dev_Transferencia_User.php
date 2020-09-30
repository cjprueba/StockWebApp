<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Dev_Transferencia_User extends Model
{
    //
        protected $connection = 'retail';
    protected $table = 'dev_transferencia_user';
    public static function guardar_referencia($id_user, $accion, $id_transferencia, $fecha)
    {

    	/*  --------------------------------------------------------------------------------- */

        // INSERTAR REFERENCIAS

        $cr_usr = Dev_Transferencia_User::insert(
            [
                'FK_USER' => $id_user,
                'ACCION' => $accion,
                'FK_DEV_TRANSFERENCIA' => $id_transferencia,
                'FECHA' => $fecha
            ]
        );

        /*  --------------------------------------------------------------------------------- */

    }
}
