<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class VentaAbonoGiro extends Model
{
    protected $connection = 'retail';
    protected $table = 'ventas_abono_giro';

    public static function guardar_referencia($data){

    	try {

	    	/*  --------------------------------------------------------------------------------- */

	    	$giro = VentaAbonoGiro::insertGetId([
	    		'FK_ENTIDAD' => $data["FK_ENTIDAD"],
	    		'FK_ABONO' => $data["FK_ABONO"],
	    		'MONTO' => $data["MONTO"],
	    		'MONEDA' => $data["MONEDA"]
	    	]);

	    	/*  --------------------------------------------------------------------------------- */

	    	Log::info('Venta Giro: Éxito al guardar.', ['ABONO' => $data["FK_ABONO"], 'ID GIRO' => $giro]);

	    	/*  --------------------------------------------------------------------------------- */

    	} catch (Exception $e) {

			/*  --------------------------------------------------------------------------------- */

			// ERROR 

			Log::error('Venta Giro: Error al guardar.', ['ABONO' => $data["FK_ABONO"]]);

			/*  --------------------------------------------------------------------------------- */

		}
    }
}
