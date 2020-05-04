<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VentaTarjeta extends Model
{
    protected $connection = 'retail';
    protected $table = 'ventas_tarjeta';

    public static function guardar_referencia($data){

    	try {

	    	/*  --------------------------------------------------------------------------------- */

	    	$tarjeta = VentaTarjeta::insertGetId([
	    		'FK_TARJETA' => $data["FK_TARJETA"],
	    		'FK_VENTA' => $data["FK_VENTA"],
	    		'MONTO' => $data["MONTO"],
	    		'MONEDA' => $data["MONEDA"]
	    	]);

	    	/*  --------------------------------------------------------------------------------- */

	    	Log::info('Venta Tarjeta: Éxito al guardar.', ['VENTA' => $data["FK_VENTA"], 'ID TARJETA' => $tarjeta]);

	    	/*  --------------------------------------------------------------------------------- */

    	} catch (Exception $e) {

			/*  --------------------------------------------------------------------------------- */

			// ERROR 

			Log::error('Venta Tarjeta: Error al guardar.', ['VENTA' => $data["FK_VENTA"]]);

			/*  --------------------------------------------------------------------------------- */

		}
    }
}
