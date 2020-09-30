<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class VentaAbonoDet extends Model
{
    protected $connection = 'retail';
    protected $table = 'ventas_abono_det';
    

    public static function guardar_referencia($data){

    	try {

	    	/*  --------------------------------------------------------------------------------- */

	    	$abono_det = VentaAbonoDet::insertGetId([
	    		'FK_VENTA' => $data["FK_VENTA"],
	    		'FK_VENTAS_ABONO' => $data["FK_VENTAS_ABONO"],
	    		'PAGO' => $data["PAGO"]
	    	]);

	    	/*  --------------------------------------------------------------------------------- */

	    	Log::info('Venta Abono Detalle: Éxito al guardar.', ['VENTA' => $data["FK_VENTA"], 'ID ABONO DETALLE: ' => $abono_det]);

	    	/*  --------------------------------------------------------------------------------- */

    	} catch (Exception $e) {

			/*  --------------------------------------------------------------------------------- */

			// ERROR 

			Log::error('Venta Abono Detalle: Error al guardar.', ['VENTA' => $data["FK_VENTA"]]);

			/*  --------------------------------------------------------------------------------- */

		}

    }
}
