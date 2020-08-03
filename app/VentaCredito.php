<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VentaCredito extends Model
{
    protected $connection = 'retail';
    protected $table = 'ventas_credito';

    public static function guardar_referencia($data){

    	try {

	    	/*  --------------------------------------------------------------------------------- */

	    	$credito = VentaCredito::insertGetId([
	    		'FK_VENTA' => $data["FK_VENTA"],
	    		'MONTO' => $data["MONTO"],
	    		'MONEDA' => $data["MONEDA"],
	    		'DIAS_CREDITO' => $data["DIAS_CREDITO"],
	    		'FECHA_CREDITO_FIN' => $data["FECHA"]
	    	]);

	    	/*  --------------------------------------------------------------------------------- */

	    	Log::info('Venta Crédito: Éxito al guardar.', ['VENTA' => $data["FK_VENTA"], 'ID CREDITO' => $credito]);

	    	/*  --------------------------------------------------------------------------------- */

    	} catch (Exception $e) {

			/*  --------------------------------------------------------------------------------- */

			// ERROR 

			Log::error('Venta Crédito: Error al guardar.', ['VENTA' => $data["FK_VENTA"]]);

			/*  --------------------------------------------------------------------------------- */

		}
    }
}
