<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class VentaGiro extends Model
{
    protected $connection = 'retail';
    protected $table = 'ventas_giro';

    public static function guardar_referencia($data){

    	try {

	    	/*  --------------------------------------------------------------------------------- */

	    	$giro = VentaGiro::insertGetId([
	    		'FK_ENTIDAD' => $data["FK_ENTIDAD"],
	    		'FK_VENTA' => $data["FK_VENTA"],
	    		'MONTO' => $data["MONTO"],
	    		'MONEDA' => $data["MONEDA"]
	    	]);

	    	/*  --------------------------------------------------------------------------------- */

	    	Log::info('Venta Giro: Éxito al guardar.', ['VENTA' => $data["FK_VENTA"], 'ID GIRO' => $giro]);

	    	/*  --------------------------------------------------------------------------------- */

    	} catch (Exception $e) {

			/*  --------------------------------------------------------------------------------- */

			// ERROR 

			Log::error('Venta Giro: Error al guardar.', ['VENTA' => $data["FK_VENTA"]]);

			/*  --------------------------------------------------------------------------------- */

		}
    }
}
