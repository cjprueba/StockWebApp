<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VentasTieneAgencia extends Model
{
    /*  --------------------------------------------------------------------------------- */

    // INICIAR LAS VARIABLES GLOBALES

    protected $connection = 'retail';
    protected $table = 'ventas_tiene_agencia';
    
    /*  --------------------------------------------------------------------------------- */


    public static function guardar_referencia($data)
    {

    	/*  --------------------------------------------------------------------------------- */

        // INSERTAR REFERENCIAS

        $tdtl = VentasTieneAgencia::insert(
            [
                'FK_VENTA' => $data['FK_VENTA'],
                'FK_AGENCIA' => $data['FK_AGENCIA']
            ]
        );

        /*  --------------------------------------------------------------------------------- */

    }
}
