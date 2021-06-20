<?php

namespace App;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Ventas_det_Descuento extends Model
{
    /*  --------------------------------------------------------------------------------- */

    // INICIAR LAS VARIABLES GLOBALES

    protected $connection = 'retail';
    protected $table = 'ventasdet_descuento';
    
    /*  --------------------------------------------------------------------------------- */


    public static function guardar_referencia($porcentaje, $total, $id_vd, $id_moneda, $cod_prod,$tipo_descuento)
    {

    	/*  --------------------------------------------------------------------------------- */

        // INSERTAR REFERENCIAS

        $tdtl = Ventas_det_Descuento::insert(
            [
                'PORCENTAJE' => $porcentaje,
                'TOTAL' => $total,
                'FK_VENTASDET' => $id_vd,
                'FK_MONEDA' => $id_moneda,
                'FK_COD_PROD' => $cod_prod,
                'TIPO_DESCUENTO'=>$tipo_descuento
            ]
        );

        /*  --------------------------------------------------------------------------------- */

    }
}
