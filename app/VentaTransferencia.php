<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class VentaTransferencia extends Model
{
    protected $connection = 'retail';
    protected $table = 'ventas_transferencia';

    public static function guardar_referencia($data){

    	try {

	    	/*  --------------------------------------------------------------------------------- */

	    	$transferencia = VentaTransferencia::insertGetId([
	    		'FK_BANCO' => $data["FK_BANCO"],
	    		'FK_VENTA' => $data["FK_VENTA"],
	    		'MONTO' => $data["MONTO"],
	    		'MONEDA' => $data["MONEDA"]
	    	]);

	    	/*  --------------------------------------------------------------------------------- */

	    	Log::info('Venta Transferencia: Éxito al guardar.', ['VENTA' => $data["FK_VENTA"], 'ID TRANSFERENCIA' => $transferencia]);

	    	/*  --------------------------------------------------------------------------------- */

    	} catch (Exception $e) {

			/*  --------------------------------------------------------------------------------- */

			// ERROR 

			Log::error('Venta Transferencia: Error al guardar.', ['VENTA' => $data["FK_VENTA"]]);

			/*  --------------------------------------------------------------------------------- */

		}
    }
}
