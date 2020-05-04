<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompraUser extends Model
{

	protected $connection = 'retail';
    protected $table = 'compras_user';

    public static function guardar_referencia($id_user, $accion, $id_compra, $fecha)
    {

    	/*  --------------------------------------------------------------------------------- */

        // INSERTAR REFERENCIAS

        $cr_usr = CompraUser::insert(
            [
                'FK_USER' => $id_user,
                'ACCION' => $accion,
                'FK_COMPRA' => $id_compra,
                'FECHA' => $fecha
            ]
        );

        /*  --------------------------------------------------------------------------------- */

    }
}
