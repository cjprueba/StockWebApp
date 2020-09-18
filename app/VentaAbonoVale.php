<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class VentaAbonoVale extends Model
{
    protected $connection = 'retail';
    protected $table = 'ventas_abono_vale';

    public static function guardar_referencia($data){

    	try {

	    	/*  --------------------------------------------------------------------------------- */

	    	$vale = VentaAbonoVale::insertGetId([
	    		'FK_ABONO' => $data["FK_ABONO"],
	    		'MONTO' => $data["MONTO"],
	    		'MONEDA' => $data["MONEDA"]
	    	]);

	    	/*  --------------------------------------------------------------------------------- */

	    	Log::info('Venta Vale: Éxito al guardar.', ['ABONO' => $data["FK_ABONO"], 'ID VALE' => $vale]);

	    	/*  --------------------------------------------------------------------------------- */

    	} catch (Exception $e) {

			/*  --------------------------------------------------------------------------------- */

			// ERROR 

			Log::error('Venta Vale: Error al guardar.', ['ABONO' => $data["FK_ABONO"]]);

			/*  --------------------------------------------------------------------------------- */

		}
    }
}
