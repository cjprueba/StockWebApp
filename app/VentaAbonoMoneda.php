<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class VentaAbonoMoneda extends Model
{
    protected $connection = 'retail';
    protected $table = 'ventas_abono_monedas';
    public $timestamps = false;
    
    public static function guardar_referencia($data){

    	try {

	    	/*  --------------------------------------------------------------------------------- */

	    	$credito = VentaAbonoMoneda::insertGetId([
	    		'FK_ABONO' => $data["FK_ABONO"],
	    		'MONTO' => $data["MONTO"],
	    		'FK_MONEDA' => $data["FK_MONEDA"]
	    	]);

	    	/*  --------------------------------------------------------------------------------- */

	    	Log::info('Venta Abono Moneda: Éxito al guardar.', ['ABONO' => $data["FK_ABONO"], 'ID CREDITO' => $credito]);

	    	/*  --------------------------------------------------------------------------------- */

    	} catch (Exception $e) {

			/*  --------------------------------------------------------------------------------- */

			// ERROR 

			Log::error('Venta Crédito: Error al guardar.', ['ABONO' => $data["FK_ABONO"]]);

			/*  --------------------------------------------------------------------------------- */

		}
    }
}
