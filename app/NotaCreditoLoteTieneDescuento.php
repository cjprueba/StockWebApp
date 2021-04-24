<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NotaCreditoLoteTieneDescuento extends Model
{
	 protected $connection = 'retail';
    protected $table = 'nota_credito_lote_tiene_descuento';
    //
        public static function guardar_referencia($data)
    {

    	/*  --------------------------------------------------------------------------------- */

        // INSERTAR REFERENCIAS

        $nc = NotaCreditoLoteTieneDescuento::insert(
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
