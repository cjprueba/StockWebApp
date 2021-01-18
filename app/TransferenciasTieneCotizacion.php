<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class TransferenciasTieneCotizacion extends Model
{
    //
    protected $connection = 'retail';
	protected $table = 'transferencias_tiene_cotizacion';
	public $timestamps = false;
	public static function guardar_referencia($data){

    	try {
	    	
	    	/*  --------------------------------------------------------------------------------- */

	    	if ($data['COTIZACION']["formula_gs_id"] !== 0 && $data['COTIZACION']["formula_gs_id"] !== "" && $data['COTIZACION']["formula_gs_id"] !== NULL){

	    		$id_venta_tiene_cotizacion = TransferenciasTieneCotizacion::insertGetId([
		    		'FK_TRANSFERENCIA' => $data["FK_TRANSFERENCIA"],
		    		'FK_COTIZACION' => $data['COTIZACION']["formula_gs_id"],
		    	]);

	    	}
	    	
	    	if ($data['COTIZACION']["formula_usd_id"] !== 0 && $data['COTIZACION']["formula_usd_id"] !== "" && $data['COTIZACION']["formula_usd_id"] !== NULL){
	    		
	    		$id_venta_tiene_cotizacion = TransferenciasTieneCotizacion::insertGetId([
		    		'FK_TRANSFERENCIA' => $data["FK_TRANSFERENCIA"],
		    		'FK_COTIZACION' => $data['COTIZACION']["formula_usd_id"],
		    	]);

	    	}

	    	if ($data['COTIZACION']["formula_ps_id"] !== 0 && $data['COTIZACION']["formula_ps_id"] !== "" && $data['COTIZACION']["formula_ps_id"] !== NULL){
	    		
	    		$id_venta_tiene_cotizacion = TransferenciasTieneCotizacion::insertGetId([
		    		'FK_TRANSFERENCIA' => $data["FK_TRANSFERENCIA"],
		    		'FK_COTIZACION' => $data['COTIZACION']["formula_ps_id"],
		    	]);

	    	}

	    	if ($data['COTIZACION']["formula_rs_id"] !== 0 && $data['COTIZACION']["formula_rs_id"] !== "" && $data['COTIZACION']["formula_rs_id"] !== NULL){
	    		
	    		$id_venta_tiene_cotizacion = TransferenciasTieneCotizacion::insertGetId([
		    		'FK_TRANSFERENCIA' => $data["FK_TRANSFERENCIA"],
		    		'FK_COTIZACION' => $data['COTIZACION']["formula_rs_id"],
		    	]);

	    	}

	    	// if ($data['COTIZACION']["formula_gs_reves_id"] !== 0 && $data['COTIZACION']["formula_gs_reves_id"] !== "" && $data['COTIZACION']["formula_gs_reves_id"] !== NULL){
	    		
	    	// 	$id_venta_tiene_cotizacion = TransferenciasTieneCotizacion::insertGetId([
		    // 		'FK_TRANSFERENCIA' => $data["FK_TRANSFERENCIA"],
		    // 		'FK_COTIZACION' => $data['COTIZACION']["formula_gs_reves_id"],
		    // 	]);

	    	// }

	    	// if ($data['COTIZACION']["formula_usd_reves_id"] !== 0 && $data['COTIZACION']["formula_usd_reves_id"] !== "" && $data['COTIZACION']["formula_usd_reves_id"] !== NULL){
	    		
	    	// 	$id_venta_tiene_cotizacion = TransferenciasTieneCotizacion::insertGetId([
		    // 		'FK_TRANSFERENCIA' => $data["FK_TRANSFERENCIA"],
		    // 		'FK_COTIZACION' => $data['COTIZACION']["formula_usd_reves_id"],
		    // 	]);

	    	// }

	    	// if ($data['COTIZACION']["formula_ps_reves_id"] !== 0 && $data['COTIZACION']["formula_ps_reves_id"] !== "" && $data['COTIZACION']["formula_ps_reves_id"] !== NULL){
	    		
	    	// 	$id_venta_tiene_cotizacion = TransferenciasTieneCotizacion::insertGetId([
		    // 		'FK_TRANSFERENCIA' => $data["FK_TRANSFERENCIA"],
		    // 		'FK_COTIZACION' => $data['COTIZACION']["formula_ps_reves_id"],
		    // 	]);

	    	// }

	    	// if ($data['COTIZACION']["formula_rs_reves_id"] !== 0 && $data['COTIZACION']["formula_rs_reves_id"] !== "" && $data['COTIZACION']["formula_rs_reves_id"] !== NULL){
	    		
	    	// 	$id_venta_tiene_cotizacion = TransferenciasTieneCotizacion::insertGetId([
		    // 		'FK_TRANSFERENCIA' => $data["FK_TRANSFERENCIA"],
		    // 		'FK_COTIZACION' => $data['COTIZACION']["formula_rs_reves_id"],
		    // 	]);

	    	// }
	    	/*  --------------------------------------------------------------------------------- */

	    	Log::info('Transferencias Tiene Contizacion: Éxito al guardar.', ['TRANSFERENCIA' => $data["FK_TRANSFERENCIA"]]);

	    	/*  --------------------------------------------------------------------------------- */

    	} catch (Exception $e) {

			/*  --------------------------------------------------------------------------------- */

			// ERROR 

			Log::error('Transferencias Tiene Contizacion: Error al guardar.', ['TRANSFERENCIA' => $data["FK_TRANSFERENCIA"]]);

			/*  --------------------------------------------------------------------------------- */

		}
    }
}
