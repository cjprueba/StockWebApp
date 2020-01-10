<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Pagos_Prov_Tarjeta extends Model
{
    protected $connection = 'retail';
    protected $table = 'pagos_prov_tarjeta';

    public static function guardar_referencia($data){

    	try {

    	/*  --------------------------------------------------------------------------------- */

    	$tarjeta = Pagos_Prov_Tarjeta::insertGetId([
    		'FK_TARJETA' => $data["FK_TARJETA"],
    		'FK_PAGO_PROV' => $data["FK_PAGO_PROV"],
    		'MONTO' => $data["MONTO"],
    		'MONEDA' => $data["MONEDA"]
    	]);

    	/*  --------------------------------------------------------------------------------- */

    	Log::info('Pago Proveedor Tarjeta: Éxito al guardar.', ['PAGO' => $data["FK_PAGO_PROV"], 'ID TARJETA' => $tarjeta]);

    	/*  --------------------------------------------------------------------------------- */

    	} catch (Exception $e) {

			/*  --------------------------------------------------------------------------------- */

			// ERROR 

			Log::error('Pago Proveedor Tarjeta: Error al guardar.', ['PAGO' => $data["FK_PAGO_PROV"]]);

			/*  --------------------------------------------------------------------------------- */

		}
    }
}
