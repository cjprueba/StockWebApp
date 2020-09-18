<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class VentaAbonoTransferencia extends Model
{
    protected $connection = 'retail';
    protected $table = 'ventas_abono_transferencia';

    public static function guardar_referencia($data){

    	try {

	    	/*  --------------------------------------------------------------------------------- */

	    	$transferencia = VentaAbonoTransferencia::insertGetId([
	    		'FK_BANCO' => $data["FK_BANCO"],
	    		'FK_ABONO' => $data["FK_ABONO"],
	    		'MONTO' => $data["MONTO"],
	    		'MONEDA' => $data["MONEDA"]
	    	]);

	    	/*  --------------------------------------------------------------------------------- */

	    	Log::info('Venta Transferencia: Éxito al guardar.', ['ABONO' => $data["FK_ABONO"], 'ID TRANSFERENCIA' => $transferencia]);

	    	/*  --------------------------------------------------------------------------------- */

    	} catch (Exception $e) {

			/*  --------------------------------------------------------------------------------- */

			// ERROR 

			Log::error('Venta Transferencia: Error al guardar.', ['ABONO' => $data["FK_ABONO"]]);

			/*  --------------------------------------------------------------------------------- */

		}
    }
}
