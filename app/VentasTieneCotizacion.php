<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class VentasTieneCotizacion extends Model
{
    protected $connection = 'retail';
	protected $table = 'ventas_tiene_cotizacion';
	public $timestamps = false;

	public static function guardar_referencia($data){

    	try {
	    	
	    	/*  --------------------------------------------------------------------------------- */

	    	if ($data['COTIZACION']["formula_gs_id"] !== 0 && $data['COTIZACION']["formula_gs_id"] !== "" && $data['COTIZACION']["formula_gs_id"] !== NULL){

	    		$id_venta_tiene_cotizacion = VentasTieneCotizacion::insertGetId([
		    		'FK_VENTA' => $data["FK_VENTA"],
		    		'FK_COTIZACION' => $data['COTIZACION']["formula_gs_id"],
		    	]);

	    	}
	    	
	    	if ($data['COTIZACION']["formula_usd_id"] !== 0 && $data['COTIZACION']["formula_usd_id"] !== "" && $data['COTIZACION']["formula_usd_id"] !== NULL){
	    		
	    		$id_venta_tiene_cotizacion = VentasTieneCotizacion::insertGetId([
		    		'FK_VENTA' => $data["FK_VENTA"],
		    		'FK_COTIZACION' => $data['COTIZACION']["formula_usd_id"],
		    	]);

	    	}

	    	if ($data['COTIZACION']["formula_ps_id"] !== 0 && $data['COTIZACION']["formula_ps_id"] !== "" && $data['COTIZACION']["formula_ps_id"] !== NULL){
	    		
	    		$id_venta_tiene_cotizacion = VentasTieneCotizacion::insertGetId([
		    		'FK_VENTA' => $data["FK_VENTA"],
		    		'FK_COTIZACION' => $data['COTIZACION']["formula_ps_id"],
		    	]);

	    	}

	    	if ($data['COTIZACION']["formula_rs_id"] !== 0 && $data['COTIZACION']["formula_rs_id"] !== "" && $data['COTIZACION']["formula_rs_id"] !== NULL){
	    		
	    		$id_venta_tiene_cotizacion = VentasTieneCotizacion::insertGetId([
		    		'FK_VENTA' => $data["FK_VENTA"],
		    		'FK_COTIZACION' => $data['COTIZACION']["formula_rs_id"],
		    	]);

	    	}

	    	if ($data['COTIZACION']["formula_gs_reves_id"] !== 0 && $data['COTIZACION']["formula_gs_reves_id"] !== "" && $data['COTIZACION']["formula_gs_reves_id"] !== NULL){
	    		
	    		$id_venta_tiene_cotizacion = VentasTieneCotizacion::insertGetId([
		    		'FK_VENTA' => $data["FK_VENTA"],
		    		'FK_COTIZACION' => $data['COTIZACION']["formula_gs_reves_id"],
		    	]);

	    	}

	    	if ($data['COTIZACION']["formula_usd_reves_id"] !== 0 && $data['COTIZACION']["formula_usd_reves_id"] !== "" && $data['COTIZACION']["formula_usd_reves_id"] !== NULL){
	    		
	    		$id_venta_tiene_cotizacion = VentasTieneCotizacion::insertGetId([
		    		'FK_VENTA' => $data["FK_VENTA"],
		    		'FK_COTIZACION' => $data['COTIZACION']["formula_usd_reves_id"],
		    	]);

	    	}

	    	if ($data['COTIZACION']["formula_ps_reves_id"] !== 0 && $data['COTIZACION']["formula_ps_reves_id"] !== "" && $data['COTIZACION']["formula_ps_reves_id"] !== NULL){
	    		
	    		$id_venta_tiene_cotizacion = VentasTieneCotizacion::insertGetId([
		    		'FK_VENTA' => $data["FK_VENTA"],
		    		'FK_COTIZACION' => $data['COTIZACION']["formula_ps_reves_id"],
		    	]);

	    	}

	    	if ($data['COTIZACION']["formula_rs_reves_id"] !== 0 && $data['COTIZACION']["formula_rs_reves_id"] !== "" && $data['COTIZACION']["formula_rs_reves_id"] !== NULL){
	    		
	    		$id_venta_tiene_cotizacion = VentasTieneCotizacion::insertGetId([
		    		'FK_VENTA' => $data["FK_VENTA"],
		    		'FK_COTIZACION' => $data['COTIZACION']["formula_rs_reves_id"],
		    	]);

	    	}
	    	/*  --------------------------------------------------------------------------------- */

	    	Log::info('Venta Tiene Contizacion: Éxito al guardar.', ['VENTA' => $data["FK_VENTA"]]);

	    	/*  --------------------------------------------------------------------------------- */

    	} catch (Exception $e) {

			/*  --------------------------------------------------------------------------------- */

			// ERROR 

			Log::error('Venta Tiene Contizacion: Error al guardar.', ['VENTA' => $data["FK_VENTA"]]);

			/*  --------------------------------------------------------------------------------- */

		}
    }
}
