<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class VentaAbonoTarjeta extends Model
{
    protected $connection = 'retail';
    protected $table = 'ventas_abono_tarjeta';

    public static function guardar_referencia($data){

    	try {

	    	/*  --------------------------------------------------------------------------------- */

	    	$tarjeta = VentaAbonoTarjeta::insertGetId([
	    		'FK_TARJETA' => $data["FK_TARJETA"],
	    		'FK_ABONO' => $data["FK_ABONO"],
	    		'MONTO' => $data["MONTO"],
	    		'MONEDA' => $data["MONEDA"]
	    	]);

	    	/*  --------------------------------------------------------------------------------- */

	    	Log::info('Venta Tarjeta: Éxito al guardar.', ['ABONO' => $data["FK_ABONO"], 'ID TARJETA' => $tarjeta]);

	    	/*  --------------------------------------------------------------------------------- */

    	} catch (Exception $e) {

			/*  --------------------------------------------------------------------------------- */

			// ERROR 

			Log::error('Venta Tarjeta: Error al guardar.', ['ABONO' => $data["FK_ABONO"]]);

			/*  --------------------------------------------------------------------------------- */

		}
    }
}
